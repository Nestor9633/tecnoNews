<?php
require 'config.php';
session_start();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Portal de Noticias</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <header>
        <h1>Portal de Noticias</h1>
        <nav>
            <a href="index.php">Inicio</a>
            <?php if (isset($_SESSION['usuario_nombre'])): ?>
                <span>Hola, <?= htmlspecialchars($_SESSION['usuario_nombre']) ?></span>
                <a href="logout.php">Cerrar sesión</a>
            <?php else: ?>
                <a href="login.php">Iniciar sesión</a>
                <a href="register.php">Registrarse</a>
            <?php endif; ?>
        </nav>
    </header>
    <main>
        <?php
        $stmt = $pdo->query("SELECT noticias.id, titulo, contenido, fecha, nombre AS autor, url 
                             FROM noticias 
                             JOIN usuarios ON noticias.autor_id = usuarios.id 
                             LEFT JOIN imagenes ON noticias.id = imagenes.noticia_id 
                             ORDER BY fecha DESC;");
        while ($row = $stmt->fetch()) {
            echo "<article>";
            echo "<h2>" . htmlspecialchars($row['titulo']) . "</h2>";
            if ($row['url']) {
                echo "<img src='" . htmlspecialchars($row['url']) . "' alt='Imagen de la noticia'>";
            }
            echo "<p>Por: " . htmlspecialchars($row['autor']) . " el " . htmlspecialchars($row['fecha']) . "</p>";
            echo "<p>" . nl2br(htmlspecialchars(substr($row['contenido'], 0, 200))) . "...</p>";
            echo "<a href='noticia.php?id=" . $row['id'] . "'>Leer más</a>";
            echo "</article>";
        }
        ?>
    </main>
</body>
</html>