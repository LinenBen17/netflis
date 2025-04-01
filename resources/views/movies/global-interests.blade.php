@extends('layouts.layout')

@section('title', 'Tendencias | Netflis')

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
                                }).then((result) => {
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
                                }).then((result) => {
                                    window.location.reload();
                                });
                            }
                        }
                    });
                });
            });
        });
    </script>
@endsection

@section('content')
    <div class="p-4">
        <h1 class="text-3xl font-bold mb-6">Tendencias</h1>
        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
            @foreach ($popularMovies as $movie)
                <div
                    class="bg-gray-900 rounded-lg shadow-md overflow-hidden transform hover:scale-105 transition-transform duration-300">
                    <img class="w-full h-64 object-cover" src="https://image.tmdb.org/t/p/w500{{ $movie['poster_path'] }}"
                        alt="{{ $movie['title'] }}">
                    <div class="p-4">
                        <h2 class="text-xl font-semibold text-white">{{ $movie['title'] }}</h2>
                        <p class="text-gray-400 mt-2 text-sm card-overview">
                            {{ $movie['overview'] }}
                        </p>
                        <div class="mt-4">
                            <a href="{{ route('movies.saveInterest', ['id' => $movie['id']]) }}"
                                class="buttonInterest netflix-red text-white px-4 py-2 rounded-md netflix-red-hover font-semibold flex items-center justify-center"
                                data-movie-id="{{ $movie['id'] }}">
                                Me Interesa
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor"
                                    class="w-5 h-5 ml-2">
                                    <path
                                        d="m11.645 20.91-.007-.003-.022-.012a15.247 15.247 0 0 1-.383-.218 25.18 25.18 0 0 1-4.244-3.17C4.688 15.36 2.25 12.174 2.25 8.25 2.25 5.322 4.714 3 7.688 3A5.5 5.5 0 0 1 12 5.052 5.5 5.5 0 0 1 16.313 3c2.973 0 5.437 2.322 5.437 5.25 0 3.925-2.438 7.111-4.739 9.256a25.175 25.175 0 0 1-4.244 3.17 15.247 15.247 0 0 1-.383.219l-.022.012-.007.004-.003.001a.752.752 0 0 1-.704 0l-.003-.001Z" />
                                </svg>
                            </a>
                        </div>
                        <p class="mt-2 text-sm text-gray-500">Seleccionada <span
                                class="font-semibold">{{ $movie['count'] }}</span> veces</p>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
@endsection
