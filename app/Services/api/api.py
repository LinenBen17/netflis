from fastapi import FastAPI, HTTPException
from pydantic import BaseModel
import pandas as pd
import numpy as np
from sklearn.feature_extraction.text import TfidfVectorizer
from sklearn.metrics.pairwise import cosine_similarity
from sklearn.preprocessing import MultiLabelBinarizer
from sklearn.neighbors import NearestNeighbors
import json
import logging

app = FastAPI()

# Configurar logging
logging.basicConfig(level=logging.INFO)
logger = logging.getLogger(__name__)

class RecomendacionRequest(BaseModel):
    movie_ids: list[int]
    num_recomendaciones: int = 10

ruta_json = 'movies_dataset.json'
df = None
caracteristicas_generos = None
caracteristicas_sinopsis = None
nn_model = None
peso_generos = 0.3
peso_sinopsis = 0.7

def cargar_datos(ruta_json):
    with open(ruta_json, 'r', encoding='utf-8') as f:
        datos = json.load(f)
    df = pd.DataFrame(datos)
    
    # Filtrar solo las películas en inglés, español, chino o japonés
    df = df[df['original_language'].isin(['en', 'es', 'zh', 'ja'])]
    
    # Reindexar el DataFrame después de filtrar
    df = df.reset_index(drop=True)
    
    return df

def procesar_datos(df, peso_generos=0.3, peso_sinopsis=0.7):
    mlb = MultiLabelBinarizer()
    generos_binarios = mlb.fit_transform(df['genre_ids'])
    generos_df = pd.DataFrame(generos_binarios, columns=mlb.classes_)
    
    tfidf = TfidfVectorizer(stop_words='english', max_features=5000)
    tfidf_matrix = tfidf.fit_transform(df['overview'])
    
    generos_matrix = generos_df.values
    sinopsis_matrix = tfidf_matrix.toarray()
    
    nn_model = NearestNeighbors(n_neighbors=20, metric='cosine')
    caracteristicas_combinadas = np.hstack([
        peso_generos * generos_matrix,
        peso_sinopsis * sinopsis_matrix
    ])
    
    nn_model.fit(caracteristicas_combinadas)
    return generos_df, pd.DataFrame(sinopsis_matrix), df, nn_model, peso_generos, peso_sinopsis

@app.on_event("startup")
async def startup_event():
    global df, caracteristicas_generos, caracteristicas_sinopsis, nn_model, peso_generos, peso_sinopsis
    df = cargar_datos(ruta_json)
    caracteristicas_generos, caracteristicas_sinopsis, df, nn_model, peso_generos, peso_sinopsis = procesar_datos(df, peso_generos, peso_sinopsis)
    logger.info("Datos cargados y modelo inicializado.")

def recomendar_peliculas(ids_peliculas, generos_df, sinopsis_df, df, nn_model, peso_generos, peso_sinopsis, num_recomendaciones=10):
    # Filtrar los IDs que existen en el dataset
    valid_ids = [id for id in ids_peliculas if id in df['id'].values]
    invalid_ids = [id for id in ids_peliculas if id not in df['id'].values]

    logger.info(f"IDs recibidos: {ids_peliculas}")
    logger.info(f"IDs válidos: {valid_ids}")
    logger.info(f"IDs inválidos: {invalid_ids}")

    if not valid_ids:
        raise HTTPException(status_code=404, detail={
            "error": "Ninguna película válida encontrada en el dataset",
            "invalid_ids": invalid_ids,
            "valid_ids": []
        })

    # Obtener los índices correspondientes a los IDs válidos
    indices = df[df['id'].isin(valid_ids)].index.tolist()

    logger.info(f"Índices encontrados: {indices}")
    logger.info(f"Tamaño del DataFrame: {len(df)}")
    logger.info(f"Tamaño de generos_df: {len(generos_df)}")
    logger.info(f"Tamaño de sinopsis_df: {len(sinopsis_df)}")

    if not indices:
        raise HTTPException(status_code=500, detail={
            "error": "Error: No se encontraron índices válidos en el dataset",
            "invalid_ids": invalid_ids,
            "valid_ids": valid_ids
        })

    # Verificar que los índices sean válidos antes de acceder a .iloc
    if max(indices) >= len(df) or min(indices) < 0:
        logger.error(f"Índices fuera de rango: {indices}")
        raise HTTPException(status_code=500, detail={
            "error": "Error: Índices fuera de rango en el dataset",
            "invalid_ids": invalid_ids,
            "valid_ids": valid_ids,
            "indices": indices
        })

    generos_seleccionados = generos_df.iloc[indices].mean(axis=0).values.reshape(1, -1)
    sinopsis_seleccionadas = sinopsis_df.iloc[indices].mean(axis=0).values.reshape(1, -1)

    vectores_peliculas = np.hstack([
        peso_generos * generos_seleccionados,
        peso_sinopsis * sinopsis_seleccionadas
    ])

    distancias, indices_similares = nn_model.kneighbors(vectores_peliculas, n_neighbors=num_recomendaciones + len(valid_ids))

    peliculas_similares_idx = []
    seen_movie_ids = set(valid_ids)

    for i in indices_similares[0]:
        if i >= len(df) or i < 0:
            logger.warning(f"Índice similar fuera de rango: {i}")
            continue  # Evita accesos fuera de rango
        
        movie_id = df.iloc[i]['id']
        if movie_id not in seen_movie_ids:
            peliculas_similares_idx.append(i)
            seen_movie_ids.add(movie_id)

        if len(peliculas_similares_idx) >= num_recomendaciones:
            break

    recomendaciones = df.iloc[peliculas_similares_idx][['id', 'title']].drop_duplicates().to_dict('records')

    logger.info(f"Recomendaciones generadas: {recomendaciones}")

    return {
        "recomendaciones": recomendaciones,
        "invalid_ids": invalid_ids,
        "valid_ids": valid_ids
    }

@app.post("/recomendar")
async def recomendar(request: RecomendacionRequest):
    try:
        logger.info(f"Solicitud recibida: {request.movie_ids}")
        resultado = recomendar_peliculas(
            request.movie_ids,
            caracteristicas_generos,
            caracteristicas_sinopsis,
            df,
            nn_model,
            peso_generos,
            peso_sinopsis,
            request.num_recomendaciones
        )
        return resultado
    except HTTPException as e:
        raise e
    except Exception as e:
        logger.error(f"Error interno: {str(e)}", exc_info=True)
        raise HTTPException(status_code=500, detail={
            "error": "Error interno en el servidor",
            "detail": str(e),
            "invalid_ids": [],
            "valid_ids": []
        })