<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title')</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.0.3/dist/tailwind.min.css" rel="stylesheet">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    @yield('head')
</head>

<body class="bg-gray-100">

    <!-- Barra de navegación -->
    <nav class="bg-indigo-600 text-white p-4">
        <div class="container mx-auto flex justify-between items-center">
            <a href="{{ route('movies.index') }}" class="font-bold text-xl">Películas</a>
            <div>
                <a href="{{ route('movies.index') }}" class="mx-2">Inicio</a>
                <a href="{{ route('movies.ownInterests') }}" class="mx-2">Mis Intereses</a>
                <a href="#" class="mx-2">Intereses Globales</a>
                <a href="{{ route('logout') }}" class="mx-2">Cerrar Sesión</a>
            </div>
        </div>
    </nav>

    <!-- Contenido específico de cada vista -->
    @yield('content')

</body>

</html>
