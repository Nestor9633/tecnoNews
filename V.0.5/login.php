<?php
session_start();
require 'config.php';

if (isset($_SESSION['usuario_id'])) {
    header('Location: main.php');
    exit;
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';

    if (!empty($email) && !empty($password)) {
        try {
            $stmt = $pdo->prepare("SELECT id, nombre, contraseña FROM usuarios WHERE email = :email");
            $stmt->execute(['email' => $email]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($user && password_verify($password, $user['contraseña'])) {
                $_SESSION['usuario_id'] = $user['id'];
                $_SESSION['usuario_nombre'] = $user['nombre'];
                header('Location: main.php');
                exit;
            } else {
                $error = 'Correo electrónico o contraseña incorrectos.';
            }
        } catch (PDOException $e) {
            $error = 'Error en la conexión a la base de datos.';
        }
    } else {
        $error = 'Por favor, completa todos los campos.';
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>TecnoNews - Login</title>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Orbitron:wght@600&display=swap">
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
            font-family: 'Orbitron', sans-serif;
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
            font-size: 24px;
            text-decoration: none;
            color: white;
        }

        .container {
            display: flex;
            flex-direction: column;
            align-items: center;
            margin-top: 50px;
        }

        .logo {
            width: 100px;
            height: 100px;
        }

        h2 {
            font-family: 'Orbitron', sans-serif;
            margin-top: 10px;
            font-size: 24px;
        }

        form {
            width: 300px;
            display: flex;
            flex-direction: column;
            gap: 20px;
        }

        input {
            padding: 12px;
            font-size: 16px;
            border: 2px solid black;
            border-radius: 2px;
            font-weight: bold;
            color: gray;
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
            background-color: #eeeeee;
        }

        .error {
            color: red;
            font-weight: bold;
            margin-bottom: 10px;
        }
    </style>
</head>
<body>
    <div class="header">
        <img src="uploads/logo.png" alt="Logo" class="icon-left" width="40" height="40">
        TecnoNews
        <a href="register.php" class="icon-right" title="Registrarse">➕</a>
    </div>

    <div class="container">
        <img src="uploads/logo.png" alt="Logo" class="logo">
        <h2>Inicio de sesión</h2>
        
        <?php if ($error): ?>
            <p class="error"><?= htmlspecialchars($error) ?></p>
        <?php endif; ?>

        <form action="login.php" method="post">
            <input type="email" name="email" placeholder="Correo electrónico" required>
            <input type="password" name="password" placeholder="Contraseña" required>
            <button type="submit">Entrar</button>
        </form>
    </div>
</body>
</html>
