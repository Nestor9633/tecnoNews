<?php
session_start();
require 'config.php';

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    die('ID de noticia inválido.');
}

$id = (int)$_GET['id'];

$stmt = $pdo->prepare("
    SELECT noticias.id, titulo, contenido, fecha, categoria_id, autor_id, usuarios.nombre AS autor, imagenes.url 
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

$categoria = '';
if ($noticia['categoria_id']) {
    $stmt = $pdo->prepare("SELECT nombre FROM categorias WHERE id = ?");
    $stmt->execute([$noticia['categoria_id']]);
    $categoriaData = $stmt->fetch();
    $categoria = $categoriaData ? $categoriaData['nombre'] : '';
}

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_SESSION['usuario_id']) && isset($_POST['comentario'])) {
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
    WHERE comentarios.noticia_id = ? 
    ORDER BY comentarios.fecha ASC
");
$stmt->execute([$id]);
$comentarios = $stmt->fetchAll();

$usuario_logueado = isset($_SESSION['usuario_id']);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title><?= htmlspecialchars($noticia['titulo']) ?> - TecnoNews</title>
    <link href="https://fonts.googleapis.com/css2?family=Orbitron:wght@600&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Orbitron', sans-serif;
            background: white;
            margin: 0;
            padding: 0;
            text-align: center;
        }
        header {
            background: #414dca;
            padding: 15px;
            color: white;
            font-size: 1.6em;
            font-weight: bold;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }
        header img {
            height: 50px;
        }
        .header-title {
            flex-grow: 1;
            text-align: center;
            font-size: 1.5em;
        }
        .container {
            padding: 20px;
        }
        .box {
            border: 2px solid black;
            margin: 10px auto;
            padding: 15px;
            max-width: 700px;
        }
        .fila {
            display: flex;
            justify-content: center;
            gap: 20px;
            margin-bottom: 15px;
        }
        img {
            max-width: 100%;
            height: auto;
        }
        button, .boton {
            padding: 10px 30px;
            border: 2px solid black;
            background: white;
            cursor: pointer;
            font-weight: bold;
            text-decoration: none;
            color: black;
            margin: 5px;
        }
        textarea {
            box-sizing: border-box;
            width: 100%;
            height: 100px;
            margin-top: 10px;
            padding: 10px;
            font-family: inherit;
        }
        .comentario {
            text-align: left;
            margin-bottom: 15px;
        }
        .icono, .atras {
            position: absolute;
            top: 20px;
        }
        .atras {
            left: 20px;
        }
        .icono {
            right: 20px;
        }
        #user-info {
            display: none;
            position: absolute;
            right: 0;
            top: 40px;
            background: white;
            color: black;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
            text-align: left;
            z-index: 999;
        }
        .boton-container {
            display: flex;
            justify-content: center;
            gap: 15px;
            margin-top: 20px;
        }
    </style>
</head>
<body>

<header>
    <img src="uploads/logo.png" alt="Logo">
    <div class="header-title">TecnoNews</div>
    <div class="icono">
    <?php if ($usuario_logueado): ?>
        <button onclick="toggleUserInfo()" style="
            background: #2e37a6;
            color: white;
            border: none;
            padding: 5px 10px;
            cursor: pointer;
            border-radius: 4px;
            font-size: 0.75em;
        ">
            👤 <?= htmlspecialchars($_SESSION['usuario_nombre']) ?>
        </button>
        <div id="user-info">
            <p><strong>Nombre:</strong> <?= htmlspecialchars($_SESSION['usuario_nombre']) ?></p>
            <p><strong>Tipo:</strong> <?= htmlspecialchars($_SESSION['tipo']) ?></p>
            <form action="logout.php" method="post">
                <button type="submit" style="background: red; color: white; border: none; padding: 5px 10px; border-radius: 3px; cursor: pointer;">Cerrar sesión</button>
            </form>
        </div>
    <?php endif; ?>
    </div>
</header>

<script>
function toggleUserInfo() {
    const info = document.getElementById("user-info");
    info.style.display = (info.style.display === "none" || info.style.display === "") ? "block" : "none";
}
</script>

<div class="container">

<div class="box" style="display: flex; align-items: center; justify-content: center; position: relative;">
    <a href="main.php" style="position: absolute; left: 15px;">
        <img src="uploads/home.png" alt="Volver" style="height: 25px;">
    </a>
    <h1 style="margin: 0 auto;"><?= htmlspecialchars($noticia['titulo']) ?></h1>
</div>

<div class="fila">
    <div class="box"><?= htmlspecialchars($noticia['autor']) ?> - <?= htmlspecialchars($noticia['fecha']) ?></div>
    <div class="box"><?= htmlspecialchars($categoria) ?></div>
</div>

<div class="box">
    <?= nl2br(htmlspecialchars($noticia['contenido'])) ?>
    <?php if (!empty($noticia['url'])): ?>
        <div class="box"><img src="<?= htmlspecialchars($noticia['url']) ?>" alt="Imagen de la noticia"></div>
    <?php endif; ?>
</div>

<div class="box">
    <h3>Caja de comentarios</h3>
    <?php if (!empty($comentarios)): ?>
        <?php foreach ($comentarios as $c): ?>
            <div class="comentario">
                <strong><?= htmlspecialchars($c['nombre']) ?></strong> dijo el <?= htmlspecialchars($c['fecha']) ?>:
                <p><?= nl2br(htmlspecialchars($c['comentario'])) ?></p>
            </div>
        <?php endforeach; ?>
    <?php else: ?>
        <p>No hay comentarios aún.</p>
    <?php endif; ?>

    <?php if ($usuario_logueado): ?>
        <form method="post" action="noticia.php?id=<?= $id ?>">
            <textarea name="comentario" placeholder="Escribe tu comentario..." required></textarea><br>
            <?php if (!empty($error)) echo "<p style='color:red;'>$error</p>"; ?>
            <button type="submit">Subir comentario</button>
        </form>
    <?php else: ?>
        <p><a href="login.php">Inicia sesión</a> para comentar.</p>
    <?php endif; ?>
</div>

<?php if ($usuario_logueado): ?>
    <div class="boton-container">
        <a class="boton" href="borrar_noticia.php?id=<?= $id ?>" 
           onclick="return confirm('¿Estás seguro de que quieres borrar esta noticia?');">Borrar</a>
        <a class="boton" href="editar_noticia.php?id=<?= $id ?>">Editar</a>
    </div>
<?php endif; ?>

</div>
</body>
</html>
