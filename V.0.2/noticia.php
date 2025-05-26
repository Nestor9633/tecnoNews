<?php
session_start();
require 'config.php';

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    die('ID de noticia inválido.');
}

$id = (int)$_GET['id'];


$stmt = $pdo->prepare("
    SELECT noticias.id, titulo, contenido, fecha, nombre AS autor, url 
    FROM noticias 
    JOIN usuarios ON noticias.autor_id = usuarios.id 
    LEFT JOIN imagenes ON noticias.id = imagenes.noticia_id 
    WHERE noticias.id = ?
");
$stmt->execute([$id]);
$noticia = $stmt->fetch();

if (!$noticia) {
    die('Noticia no encontrada.');
}


if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_SESSION['usuario_id'])) {
    $comentario = trim($_POST['comentario']);
    if (!empty($comentario)) {
        $stmt = $pdo->prepare("INSERT INTO comentarios (noticia_id, usuario_id, comentario) VALUES (?, ?, ?)");
        $stmt->execute([$id, $_SESSION['usuario_id'], $comentario]);
        header("Location: noticia.php?id=$id");
        exit;
    } else {
        $error = "El comentario no puede estar vacío.";
    }
}


$stmt = $pdo->prepare("
    SELECT comentarios.comentario, comentarios.fecha, usuarios.nombre 
    FROM comentarios 
    JOIN usuarios ON comentarios.usuario_id = usuarios.id 
    WHERE noticia_id = ? 
    ORDER BY fecha ASC
");
$stmt->execute([$id]);
$comentarios = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title><?= htmlspecialchars($noticia['titulo']) ?> - Portal de Noticias</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
<header>
    <h1><?= htmlspecialchars($noticia['titulo']) ?></h1>
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
    <?php if ($noticia['url']): ?>
        <img src="<?= htmlspecialchars($noticia['url']) ?>" alt="Imagen de la noticia">
    <?php endif; ?>

    <p><strong>Por:</strong> <?= htmlspecialchars($noticia['autor']) ?> | <strong>Fecha:</strong> <?= htmlspecialchars($noticia['fecha']) ?></p>
    <p><?= nl2br(htmlspecialchars($noticia['contenido'])) ?></p>

    <section>
        <h2>Comentarios</h2>

        <?php if ($comentarios): ?>
            <?php foreach ($comentarios as $c): ?>
                <article class="comentario">
                    <p><strong><?= htmlspecialchars($c['nombre']) ?></strong> dijo el <?= htmlspecialchars($c['fecha']) ?>:</p>
                    <p><?= nl2br(htmlspecialchars($c['comentario'])) ?></p>
                </article>
            <?php endforeach; ?>
        <?php else: ?>
            <p>No hay comentarios aún.</p>
        <?php endif; ?>

        <?php if (isset($_SESSION['usuario_id'])): ?>
            <form action="noticia.php?id=<?= $id ?>" method="post">
                <textarea name="comentario" rows="4" cols="50" placeholder="Escribe tu comentario aquí..." required></textarea><br>
                <?php if (!empty($error)) echo "<p style='color:red;'>$error</p>"; ?>
                <button type="submit">Enviar comentario</button>
            </form>
        <?php else: ?>
            <p><a href="login.php">Inicia sesión</a> para comentar.</p>
        <?php endif; ?>
    </section>
</main>
</body>
</html>
