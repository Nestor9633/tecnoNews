<?php
session_start();
require 'config.php'; 

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre = $_POST['nombre'] ?? '';
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';
    $confirmar = $_POST['confirmar'] ?? '';

    if (!empty($nombre) && !empty($email) && !empty($password) && !empty($confirmar)) {
        if ($password === $confirmar) {
            try {
                $stmt = $pdo->prepare("SELECT id FROM usuarios WHERE email = :email");
                $stmt->execute(['email' => $email]);

                if ($stmt->fetch()) {
                    $error = 'El correo electrónico ya está registrado.';
                } else {
                    $hash = password_hash($password, PASSWORD_DEFAULT);
                    $stmt = $pdo->prepare("INSERT INTO usuarios (nombre, email, contraseña) VALUES (:nombre, :email, :contrasena)");
                    $stmt->execute([
                        'nombre' => $nombre,
                        'email' => $email,
                        'contrasena' => $hash
                    ]);
                    header('Location: login.php');
                    exit;
                }
            } catch (PDOException $e) {
                $error = 'Error al registrar el usuario.';
            }
        } else {
            $error = 'Las contraseñas no coinciden.';
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
  <title>TecnoNews - Registro</title>
  <link href="https://fonts.googleapis.com/css2?family=Orbitron:wght@600&display=swap" rel="stylesheet">
  <style>
    body {
      font-family: 'Orbitron', sans-serif;
      background-color: #fff;
      margin: 0;
      padding: 0;
    }

    header {
      background-color: #4444cc;
      color: white;
      padding: 10px;
      display: flex;
      align-items: center;
      justify-content: space-between;
    }

    .logo {
      height: 40px;
    }

    .titulo {
      font-size: 28px;
      font-weight: bold;
    }

    .iconos a {
      font-size: 28px;
      margin-left: 15px;
      color: white;
      text-decoration: none;
    }

    .contenedor {
      display: flex;
      flex-direction: column;
      align-items: center;
      margin-top: 40px;
    }

    .contenedor img {
      width: 100px;
    }

    h2 {
      font-size: 24px;
      margin: 20px 0;
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
      font-family: 'Orbitron', sans-serif;
    }

    button {
      padding: 10px;
      font-size: 16px;
      font-weight: bold;
      border: 2px solid black;
      background-color: white;
      cursor: pointer;
      font-family: 'Orbitron', sans-serif;
    }

    button:hover {
      background-color: #eee;
    }

    .error {
      color: red;
      margin-bottom: 15px;
      font-family: 'Orbitron', sans-serif;
    }
  </style>
</head>
<body>

  <header>
    <img src="uploads/logo.png" alt="Logo" class="logo">
    <div class="titulo">TecnoNews</div>
    <div class="iconos">
      <a href="login.php">👤</a>
    </div>
  </header>

  <main class="contenedor">
    <img src="uploads/logo.png" alt="Logo">
    <h2>Crear cuenta</h2>

    <?php if ($error): ?>
      <div class="error"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <form method="POST" action="register.php">
      <input type="text" name="nombre" placeholder="Nombre" required>
      <input type="email" name="email" placeholder="Correo electrónico" required>
      <input type="password" name="password" placeholder="Contraseña" required>
      <input type="password" name="confirmar" placeholder="Confirmar contraseña" required>
      <button type="submit">Registrarse</button>
    </form>
  </main>

</body>
</html>
