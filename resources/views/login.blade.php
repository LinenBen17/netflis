<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.0.3/dist/tailwind.min.css" rel="stylesheet">
    <title>Login | Netflis</title>
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
    </style>
</head>

<body class="bg-black text-white">
    <div class="container flex justify-center items-center mx-auto p-4 h-screen flex-col">
        <div class="bg-black bg-opacity-75 py-12 px-10 rounded-lg shadow-xl w-full max-w-md">
            <form method="POST" action="{{ route('login') }}">
                @csrf
                <h1 class="text-4xl font-bold mb-8 text-white text-center">Iniciar Sesión</h1>
                <div class="flex flex-col space-y-6">
                    <input type="text" name="username"
                        class="w-full px-4 py-3 bg-gray-800 border border-gray-700 rounded-md focus:outline-none focus:ring-2 focus:ring-red-600 text-white placeholder-gray-400"
                        placeholder="Nombre de Usuario" required>
                    <button type="submit"
                        class="netflix-red text-white px-6 py-3 rounded-md netflix-red-hover focus:outline-none focus:ring-2 focus:ring-red-600 font-semibold">
                        Ingresar
                    </button>
                </div>
            </form>
        </div>
    </div>
</body>

</html>
