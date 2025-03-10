<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.0.3/dist/tailwind.min.css" rel="stylesheet">
    <title>Login | Netflis</title>
</head>

<body>
    <div class="container flex justify-center items-center mx-auto p-4 h-screen flex-col">
        <div>
            <h1 class="text-3xl font-bold mb-6">Nombre de Usuario</h1>
            <form method="POST" action="{{ route('login') }}">
                @csrf
                <input type="text" name="username"
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-600"
                    placeholder="Nombre de Usuario" required>
            </form>
        </div>
    </div>
</body>

</html>
