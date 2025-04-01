<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Bebas+Neue&family=Open+Sans:ital,wght@0,300..800;1,300..800&display=swap"
        rel="stylesheet">
    <title>Netflix 2</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            background-color: black;
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            height: 100vh;
            font-family: "Bebas Neue", sans-serif;
            font-weight: 400;
            font-size: 7rem;
            text-align: center;
            transition: all 1.5s ease-in-out;
        }

        #text {
            text-transform: uppercase;
            font-size: 5rem;
        }

        #logo-container {
            display: none;
            opacity: 0;
            transition: opacity 1.5s ease-in-out;
        }

        img {
            max-width: 80%;
            height: auto;
        }
    </style>
</head>

<body>
    <div id="text">SI NETFLIX ES TAN BUENO <br> ¿POR QUÉ NO EXISTE NETFLIX 2?</div>
    <div id="logo-container">
        <img src="{{ asset('storage/images/netflisText.png') }}" alt="Netflix 2 Logo">
    </div>

    <script>
        setTimeout(() => {
            document.body.style.color = 'black';
            document.body.style.backgroundColor = 'black';
            document.getElementById('text').style.display = 'none';
            let logoContainer = document.getElementById('logo-container');
            logoContainer.style.display = 'block';
            setTimeout(() => {
                logoContainer.style.opacity = '1';
            }, 50);
        }, 7000); // 7 segundos antes de la transición

        setTimeout(() => {
            window.location.href = "{{ route('logout') }}";
        }, 13000);
    </script>
</body>

</html>
