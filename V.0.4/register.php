<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>TecnoNews - Registro</title>
    <style>
        body {
            margin: 0;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #ffffff;
        }

        .header {
            background-color: #4444cc;
            color: white;
            padding: 10px 0;
            text-align: center;
            font-size: 28px;
            font-weight: bold;
            position: relative;
        }

        .header .icon-left {
            position: absolute;
            left: 20px;
            top: 50%;
            transform: translateY(-50%);
        }

        .header .icon-right {
            position: absolute;
            right: 20px;
            top: 50%;
            transform: translateY(-50%);
        }

        .container {
            display: flex;
            flex-direction: column;
            align-items: center;
            margin-top: 40px;
        }

        .logo {
            width: 100px;
            height: 100px;
        }

        h2 {
            margin-top: 10px;
            font-size: 24px;
        }

        form {
            width: 300px;
            display: flex;
            flex-direction: column;
            gap: 15px;
        }

        input {
            padding: 10px;
            font-size: 16px;
            border: 2px solid black;
            border-radius: 3px;
            color: gray;
            font-weight: bold;
        }

        button {
            padding: 10px;
            font-size: 16px;
            background-color: white;
            border: 2px solid black;
            cursor: pointer;
            font-weight: bold;
        }

        button:hover {
            background-color: #ddd;
        }
    </style>
</head>
<body>
    <div class="header">
        <img src="uploads/logo.png" alt="Icono" class="icon-left" width="40" height="40">
        TecnoNews
        <a href="login.php" class="icon-right" title="Iniciar sesión">👤</a>
    </div>

    <div class="container">
        <img src="uploads/logo.png" alt="Logo" class="logo">
        <h2>Inicio de sesión</h2>
        <form action="registrar.php" method="post">
            <input type="text" name="nombre" placeholder="Nombre" required>
            <input type="email" name="correo" placeholder="Correo electrónico" required>
            <input type="password" name="contrasena" placeholder="Contraseña" required>
            <input type="password" name="confirmar" placeholder="Confirmar contraseña" required>
            <button type="submit">Registrar</button>
        </form>
    </div>
</body>
</html>
