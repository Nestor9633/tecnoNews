<?php
session_start();
require 'config.php';

if (isset($_SESSION['usuario_id'])) {
    header('Location: index.php');
    exit;
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre = $_POST['nombre'] ?? '';
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';

    if (!empty($nombre) && !empty($email) && !empty($password) && !empty($confirm_password)) {
        if ($password === $confirm_password) {
            try {
                $stmt = $pdo->prepare("SELECT id FROM usuarios WHERE email = :email");
                $stmt->execute(['email' => $email]);

                if ($stmt->fetch()) {
                    $error = 'El correo electrónico ya está registrado.';
                } else {
                    $hash = password_hash($password, PASSWORD_DEFAULT);
                    $stmt = $pdo->prepare("INSERT INTO usuarios (nombre, email, contraseña) VALUES (:nombre, :email, :contraseña)");
                    $stmt->execute([
                        'nombre' => $nombre,
                        'email' => $email,
                        'contraseña' => $hash,
                    ]);

                    $_SESSION['usuario_id'] = $pdo->lastInsertId();
                    $_SESSION['usuario_nombre'] = $nombre;
                    header('Location: index.php');
                    exit;
                }
            } catch (PDOException $e) {
                $error = 'Error en la conexión a la base de datos.';
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
    <title>Registro de Usuario</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <header>
        <h1>Registrarse</h1>
        <a href="index.php">Volver al inicio</a>
    </header>
    <main>
        <?php if ($error): ?>
            <p style="color: red;"><?= htmlspecialchars($error) ?></p>
        <?php endif; ?>
        <form method="post" action="register.php">
            <label for="nombre">Nombre:</label>
            <input type="text" name="nombre" id="nombre" required>
            <label for="email">Correo electrónico:</label>
            <input type="email" name="email" id="email" required>
            <label for="password">Contraseña:</label>
            <input type="password" name="password" id="password" required>
            <label for="confirm_password">Confirmar contraseña:</label>
            <input type="password" name="confirm_password" id="confirm_password" required>
            <button type="submit">Registrarse</button>
        </form>
    </main>
</body>
</html>