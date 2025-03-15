@extends('layouts.layout')

@section('title', 'Mis Intereses | Netflis')

@section('head')
    <script>
        let movieInterest = [];

        $(document).ready(function() {
            let buttons = document.querySelectorAll('.buttonInterest');

            buttons.forEach(button => {
                button.addEventListener('click', function(e) {
                    e.preventDefault();
                    let movieId = e.target.getAttribute('data-movie-id') ?? 0;

                    $.ajax({
                        url: "{{ route('movies.saveInterest', ['id' => '']) }}" + movieId,
                        type: 'GET',
                        success: function(data) {
                            if (data.status == 'success') {
                                Swal.fire({
                                        position: "top-end",
                                        icon: "success",
                                        title: "Película guardada en tus intereses",
                                        showConfirmButton: false,
                                        timerProgressBar: true,
                                        timer: 2000
                                    })
                                    .then((result) => {
                                        window.location.reload();
                                    });
                            } else if (data.status == 'repeated') {
                                Swal.fire({
                                    position: "top-end",
                                    icon: "warning",
                                    title: "Película ya se encuentra en tus intereses",
                                    showConfirmButton: false,
                                    timerProgressBar: true,
                                    timer: 2000
                                });
                            } else {
                                Swal.fire({
                                        position: "top-end",
                                        icon: "error",
                                        title: "Error al guardar la película en tus intereses",
                                        showConfirmButton: false,
                                        timerProgressBar: true,
                                        timer: 2000
                                    })
                                    .then((result) => {
                                        window.location.reload();
                                    });
                            }
                        }
                    })
                });
            });
        });
    </script>
    <script>
        $(document).ready(function() {
            $(".deleteInterest").on("click", function(e) {
                e.preventDefault();

                let movieId = $(this).data("movie-id"); // Obtener el ID correctamente

                $.ajax({
                    url: "{{ route('movies.deleteInterest') }}?id=" +
                        movieId, // Pasar el ID en la query string
                    type: "GET",
                    success: function(data) {
                        if (data.status === "success") {
                            Swal.fire({
                                position: "top-end",
                                icon: "success",
                                title: "Película eliminada de tus intereses",
                                showConfirmButton: false,
                                timerProgressBar: true,
                                timer: 2000
                            }).then(() => {
                                window.location.reload();
                            });
                        } else {
                            Swal.fire({
                                position: "top-end",
                                icon: "error",
                                title: "Error al eliminar la película",
                                showConfirmButton: false,
                                timerProgressBar: true,
                                timer: 2000
                            });
                        }
                    }
                });
            });
        });
    </script>
@endsection

@section('content')
    <div class="container mx-auto p-4">
        <h1 class="text-2xl font-semibold">Mis Intereses</h1>
        <div class="overflow-x-auto mt-6">
            <table class="min-w-full table-auto border-collapse bg-white shadow-md rounded-lg">
                <thead class="bg-gray-100 text-gray-600">
                    <tr>
                        <th class="py-3 px-6 text-left font-semibold">#</th>
                        <th class="py-3 px-6 text-left font-semibold">Nombre</th>
                        <th class="py-3 px-6 text-left font-semibold">Año</th>
                        <th class="py-3 px-6 text-left font-semibold">Géneros</th>
                        <th class="py-3 px-6 text-left font-semibold">Acciones</th>
                    </tr>
                </thead>
                <tbody class="text-gray-600">
                    @foreach ($moviesInterested as $movie)
                        <tr class="border-b border-gray-200 hover:bg-gray-50">
                            <td class="py-3 px-6">{{ $movie['id'] }}</td>
                            <td class="py-3 px-6">{{ $movie['title'] }}</td>
                            <td class="py-3 px-6">{{ $movie['release_date'] }}</td>
                            <td class="py-3 px-6">
                                @foreach ($movie['genres'] as $genre)
                                    <span
                                        class="inline-block bg-blue-100 text-blue-800 py-1 px-3 rounded-full text-xs mr-2">{{ $genre['name'] }}</span>
                                @endforeach
                            </td>
                            <td class="py-3 px-6">
                                <a href="{{ route('movies.deleteInterest', ['id' => $movie['id']]) }}"
                                    class="deleteInterest text-red-600 hover:text-red-800 font-semibold"
                                    data-movie-id="{{ $movie['id'] }}">Eliminar</a>
                                {{-- <a href="{{ route('movies.deleteInterest', ['id' => $movie['id']]) }}"
                                    class="seeInterest text-blue-600 hover:text-blue-800 font-semibold"
                                    data-movie-id="{{ $movie['id'] }}">Ver</a> --}}
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <br>
        <hr>
        <br>
        <h1 class="text-2xl
                                    font-semibold">Nuestras Sugerencias</h1>
        <br>
        @if (isset($recommendations['invalid_ids']))
            <p>No hay recomendaciones disponibles</p>
        @else
            <div class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-4 gap-4">
                @foreach ($recommendations as $movie)
                    <div class="bg-white rounded-lg shadow-md p-4">
                        <img class="w-full h-64 object-cover rounded"
                            src="https://image.tmdb.org/t/p/w500{{ $movie['poster_path'] }}" alt="{{ $movie['title'] }}">
                        <h2 class="text-xl font-semibold mt-4">{{ $movie['title'] }}</h2>
                        <p class="text-gray-600">{{ $movie['overview'] }}</p>
                        <div
                            class="mt-5 flex w-fit justify-center bg-red-500 text-white rounded-lg hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-600">
                            <a href="{{ route('movies.saveInterest', ['id' => $movie['id']]) }}"
                                class="buttonInterest flex items-center px-6 py-2" data-movie-id="{{ $movie['id'] }}">Me
                                Interesa
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor"
                                    class="w-4 h-4">
                                    <path
                                        d="m11.645 20.91-.007-.003-.022-.012a15.247 15.247 0 0 1-.383-.218 25.18 25.18 0 0 1-4.244-3.17C4.688 15.36 2.25 12.174 2.25 8.25 2.25 5.322 4.714 3 7.688 3A5.5 5.5 0 0 1 12 5.052 5.5 5.5 0 0 1 16.313 3c2.973 0 5.437 2.322 5.437 5.25 0 3.925-2.438 7.111-4.739 9.256a25.175 25.175 0 0 1-4.244 3.17 15.247 15.247 0 0 1-.383.219l-.022.012-.007.004-.003.001a.752.752 0 0 1-.704 0l-.003-.001Z" />
                                </svg>
                            </a>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>

    @if (!empty($invalidIds))
        <script>
            console.log("AAA");
            Swal.fire({
                position: "center",
                icon: "warning",
                title: "Aún no contamos con sugerencias para las siguientes películas: {{ implode(', ', $invalidIds) }} ",
                showConfirmButton: true
            });
        </script>
    @endif
@endsection
