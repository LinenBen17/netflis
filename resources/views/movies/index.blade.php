<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Películas Populares</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.0.3/dist/tailwind.min.css" rel="stylesheet">
</head>

<body class="bg-gray-100">
    <div class="container mx-auto p-4">
        <h1 class="text-3xl font-bold mb-6">Películas Populares</h1>
        <div class="mb-4 flex justify-end">
            <!-- Formulario de búsqueda -->
            <form method="GET" action="{{ route('movies.search') }}" class="mb-6">
                <div class="flex items-center space-x-4">
                    <input type="text" name="query"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-600"
                        placeholder="Buscar película..." required>
                    <button type="submit"
                        class="px-6 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-600">Buscar</button>
                </div>
            </form>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-4 gap-4">
            @foreach ($movies['results'] as $movie)
                <div class="bg-white rounded-lg shadow-md p-4">
                    <img class="w-full h-64 object-cover rounded"
                        src="https://image.tmdb.org/t/p/w500{{ $movie['poster_path'] }}" alt="{{ $movie['title'] }}">
                    <h2 class="text-xl font-semibold mt-4">{{ $movie['title'] }}</h2>
                    <p class="text-gray-600">{{ $movie['overview'] }}</p>
                </div>
            @endforeach
        </div>
    </div>

</body>

</html>
