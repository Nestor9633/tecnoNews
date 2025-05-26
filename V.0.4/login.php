<?php
session_start();
require 'config.php';


if (isset($_SESSION['usuario_id'])) {
    header('Location: index.php');
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
                // Guardar en sesión
                $_SESSION['usuario_id'] = $user['id'];
                $_SESSION['usuario_nombre'] = $user['nombre'];
                header('Location: index.php');
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
    <title>Iniciar sesión</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <header>
        <h1>Iniciar sesión</h1>
        <a href="index.php">Volver al inicio</a>
    </header>
    <main>
        <?php if ($error): ?>
            <p style="color: red;"><?= htmlspecialchars($error) ?></p>
        <?php endif; ?>
        <form method="post" action="login.php">
            <label for="email">Correo electrónico:</label>
            <input type="email" name="email" id="email" required>
            <label for="password">Contraseña:</label>
            <input type="password" name="password" id="password" required>
            <button type="submit">Iniciar sesión</button>
        </form>
    </main>
</body>
</html>