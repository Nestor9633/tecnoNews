<?php
session_start();
require 'config.php';

if (!isset($_SESSION['usuario_id']) || $_SESSION['tipo'] !== 'admin') {
    header('Location: login.php');
    exit;
}

$noticias = obtenerNoticias($pdo);
$categorias = $pdo->query("SELECT * FROM categorias")->fetchAll(PDO::FETCH_ASSOC);

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['agregar_categoria'])) {
    $nombre = trim($_POST['nombre_categoria']);
    if (!empty($nombre)) {
        $stmt = $pdo->prepare("INSERT INTO categorias (nombre) VALUES (:nombre)");
        $stmt->execute(['nombre' => $nombre]);
        header('Location: admin.php');
        exit;
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['editar_categoria'])) {
    $id = $_POST['categoria_id'];
    $nuevoNombre = trim($_POST['nuevo_nombre']);
    if (!empty($nuevoNombre)) {
        $stmt = $pdo->prepare("UPDATE categorias SET nombre = :nombre WHERE id = :id");
        $stmt->execute(['nombre' => $nuevoNombre, 'id' => $id]);
        header('Location: admin.php');
        exit;
    }
}

if (isset($_GET['eliminar_categoria'])) {
    $id = $_GET['eliminar_categoria'];
    $stmt = $pdo->prepare("DELETE FROM categorias WHERE id = :id");
    $stmt->execute(['id' => $id]);
    header('Location: admin.php');
    exit;
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Panel de Administración - TecnoNews</title>
    <link href="https://fonts.googleapis.com/css2?family=Orbitron:wght@600&display=swap" rel="stylesheet">
    <style>
        body { margin: 0; font-family: 'Segoe UI', sans-serif; background: #f5f5f5; }
        .header {
            background-color: #414dca;
            color: white;
            padding: 15px;
            text-align: center;
            font-family: 'Orbitron', sans-serif;
            font-size: 28px;
        }
        .container {
            padding: 20px;
            max-width: 1000px;
            margin: auto;
            background: white;
            border-radius: 10px;
            box-shadow: 0 0 10px #ccc;
        }
        h2 { color: #3f49c4; }
        table { width: 100%; border-collapse: collapse; margin-top: 15px; }
        th, td { border: 1px solid #ccc; padding: 10px; text-align: left; }
        th { background-color: #eee; }
        a { text-decoration: none; color: #414dca; font-weight: bold; }
        a:hover { text-decoration: underline; }
        .form-group { margin-top: 20px; }
        input[type="text"] {
            padding: 10px;
            width: 300px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }
        button {
            padding: 10px 15px;
            background-color: #414dca;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-weight: bold;
        }
        button:hover { background-color: #414dca; }
        .back-link { margin-top: 20px; display: inline-block; }
        form.inline { display: inline; }
    </style>
</head>
<body>
<header>
<div class="header">
    <div style="display: flex; justify-content: space-between; align-items: center;">
        <img src="uploads/logo.png" alt="Logo" style="height: 50px;">    
        <div style="font-size: 1.2em;">Panel de Administración - TecnoNews</div>       
        <div style="position: relative;">
            <button onclick="toggleUserInfo()" style="background: #2e37a6; color: white; border: none; padding: 10px; cursor: pointer; border-radius: 5px;">
                👤 <?= htmlspecialchars($_SESSION['usuario_nombre']) ?>
            </button>
            <div id="user-info" style="display: none; position: absolute; right: 0; top: 40px; background: white; color: black; padding: 10px; border: 1px solid #ccc; border-radius: 5px; text-align: left; z-index: 999;">
                <p><strong>Nombre:</strong> <?= htmlspecialchars($_SESSION['usuario_nombre']) ?></p>
                <p><strong>Tipo:</strong> <?= htmlspecialchars($_SESSION['tipo']) ?></p>
                <form action="logout.php" method="post" style="margin-top: 10px;">
                    <button type="submit" style="background: red; color: white; border: none; padding: 5px 10px; border-radius: 3px; cursor: pointer;">Cerrar sesión</button>
                </form>
            </div>
        </div>
    </div>
</div>
<script>
function toggleUserInfo() {
    const info = document.getElementById("user-info");
    info.style.display = (info.style.display === "none" || info.style.display === "") ? "block" : "none";
}
</script>
</header>

<div class="container">
    <h2>Gestión de Noticias</h2>
    <p><a href="crear_noticia.php">➕ Crear Nueva Noticia</a></p>

    <?php if (count($noticias) === 0): ?>
        <p>No hay noticias registradas.</p>
    <?php else: ?>
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Título</th>
                    <th>Autor</th>
                    <th>Categoría</th>
                    <th>Fecha</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($noticias as $noticia): ?>
                    <tr>
                        <td><?= $noticia['id'] ?></td>
                        <td><?= htmlspecialchars($noticia['titulo']) ?></td>
                        <td><?= htmlspecialchars($noticia['autor_nombre']) ?></td>
                        <td><?= htmlspecialchars($noticia['categoria_nombre']) ?></td>
                        <td><?= $noticia['fecha'] ?></td>
                        <td>
                            <a href="editar_noticia.php?id=<?= $noticia['id'] ?>">Editar</a> |
                            <a href="eliminar_noticia.php?id=<?= $noticia['id'] ?>" onclick="return confirm('¿Eliminar esta noticia?');">Eliminar</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>

    <h2>Gestión de Categorías</h2>

    <ul>
        <?php foreach ($categorias as $cat): ?>
            <li>
                <?= htmlspecialchars($cat['nombre']) ?>
                <form class="inline" method="post" style="margin-left: 10px;">
                    <input type="hidden" name="categoria_id" value="<?= $cat['id'] ?>">
                    <input type="text" name="nuevo_nombre" placeholder="Nuevo nombre" required>
                    <button type="submit" name="editar_categoria">✏️</button>
                </form>
                <a href="admin.php?eliminar_categoria=<?= $cat['id'] ?>" onclick="return confirm('¿Eliminar esta categoría?');">❌</a>
            </li>
        <?php endforeach; ?>
    </ul>

    <div class="form-group">
        <form method="post">
            <label for="nombre_categoria"><strong>Agregar nueva categoría:</strong></label><br>
            <input type="text" id="nombre_categoria" name="nombre_categoria" required>
            <button type="submit" name="agregar_categoria">Agregar</button>
        </form>
    </div>

    <a class="back-link" href="main.php">← Volver a la página principal</a>
</div>

</body>
</html>
