<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="Content-Security-Policy" content="upgrade-insecure-requests">
    <title>@yield('title')</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.0.3/dist/tailwind.min.css" rel="stylesheet">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
        }

        .netflix-red {
            background-color: #E50914;
        }

        .netflix-red-hover:hover {
            background-color: #f40612;
        }

        .card-overview {
            display: -webkit-box;
            -webkit-line-clamp: 4;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }
    </style>
    @yield('head')
</head>

<body class="bg-black text-white">
    <!-- Barra de navegación -->
    <nav class="bg-black bg-opacity-90 text-white p-4 fixed w-full top-0 z-10">
        <div class="container mx-auto flex justify-between items-center">
            <!-- Logo de Netflix -->
            <a href="{{ route('movies.index') }}">
                <img src="{{ asset('storage/images/netflisText.png') }}" alt="Netflix Logo" class="h-8 md:h-10">
            </a>

            <!-- Botón hamburguesa (visible solo en móvil) -->
            <button id="menu-toggle" class="md:hidden text-white focus:outline-none">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                    xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16">
                    </path>
                </svg>
            </button>

            <!-- Opciones de navegación -->
            <div id="nav-menu"
                class="hidden md:flex md:items-center md:space-x-6 absolute md:static top-16 left-0 w-full md:w-auto bg-black md:bg-transparent p-4 md:p-0">
                <a href="{{ route('movies.index') }}"
                    class="block md:inline-block hover:text-gray-300 py-2 md:py-0">Inicio</a>
                <a href="{{ route('movies.ownInterests') }}"
                    class="block md:inline-block hover:text-gray-300 py-2 md:py-0">Mis Intereses</a>
                <a href="{{ route('movies.globalInterests') }}"
                    class="block md:inline-block hover:text-gray-300 py-2 md:py-0">Intereses Globales</a>
                <a href="{{ route('movies.statistics') }}"
                    class="block md:inline-block hover:text-gray-300 py-2 md:py-0">Estadísticas</a>
                <a href="{{ route('logout') }}" class="block md:inline-block hover:text-gray-300 py-2 md:py-0">Cerrar
                    Sesión</a>
            </div>
        </div>
    </nav>

    <!-- Contenedor principal -->
    <div class="container mx-auto pt-20 px-4">
        <!-- Formulario de búsqueda -->
        <div class="flex justify-end mb-6">
            <form method="GET" action="{{ route('movies.search') }}" class="w-full md:w-1/3">
                <div class="flex items-center space-x-4">
                    <input type="text" name="query"
                        class="w-full px-4 py-2 bg-gray-800 border border-gray-700 rounded-md text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-red-600"
                        placeholder="Buscar película..." required>
                    <button type="submit"
                        class="netflix-red text-white px-6 py-2 rounded-md netflix-red-hover focus:outline-none focus:ring-2 focus:ring-red-600 font-semibold">
                        Buscar
                    </button>
                </div>
            </form>
        </div>

        <!-- Contenido de las películas -->
        @yield('content')
    </div>

    <!-- Script para togglear el menú en móvil -->
    <script>
        $(document).ready(function() {
            $('#menu-toggle').click(function() {
                $('#nav-menu').slideToggle('fast');
            });

            // Cerrar el menú al hacer clic fuera en móvil
            $(document).click(function(e) {
                if (!$(e.target).closest('#menu-toggle, #nav-menu').length && window.innerWidth < 768) {
                    $('#nav-menu').slideUp('fast');
                }
            });

            // Asegurar que el menú esté visible en escritorio al redimensionar
            $(window).resize(function() {
                if (window.innerWidth >= 768) {
                    $('#nav-menu').show();
                } else {
                    $('#nav-menu').hide();
                }
            });
        });
    </script>
</body>

</html>
