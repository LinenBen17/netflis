<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Películas Populares</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.0.3/dist/tailwind.min.css" rel="stylesheet">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        let movieInterest = [];

        $(document).ready(function() {
            let buttons = document.querySelectorAll('.buttonInterest');

            buttons.forEach(button => {
                button.addEventListener('click', function(e) {
                    e.preventDefault();
                    let movieId = e.target.getAttribute('data-movie-id') ?? 0;

                    console.log(movieId);

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
                                /* .then((result) => {
                                                                    window.location.reload();
                                                                }); */
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
                                /* .then((result) => {
                                                                    window.location.reload();
                                                                }); */
                            }
                        }
                    })
                });
            });
        });
    </script>
    <script>
        console.log(@json($movies));
    </script>
</head>

<body class="bg-gray-100">
    <div class="container mx-auto p-4">
        <h1 class="text-3xl font-bold mb-6">Películas Populares</h1>
        <p>Bienvenido, {{ session('userSession') }}</p>
        <p>gustos,
            @foreach (session('movieInterest') as $item)
                {{ $item }}
            @endforeach
        </p>
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
    </div>

</body>

</html>
