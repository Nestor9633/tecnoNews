<?php
require 'config.php';
session_start();

$catStmt = $pdo->query("SELECT id, nombre FROM categorias");
$categorias = $catStmt->fetchAll();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $titulo = $_POST['titulo'] ?? '';
    $contenido = $_POST['contenido'] ?? '';
    $autor_id = $_SESSION['usuario_id'] ?? 1;
    $categoria_id = $_POST['categoria_id'] ?? 1;

    $stmt = $pdo->prepare("INSERT INTO noticias (titulo, contenido, autor_id, categoria_id) VALUES (?, ?, ?, ?)");
    $stmt->execute([$titulo, $contenido, $autor_id, $categoria_id]);
    $noticia_id = $pdo->lastInsertId();

    if (!empty($_FILES['imagen']['name'])) {
        $ruta = 'uploads/' . basename($_FILES['imagen']['name']);
        move_uploaded_file($_FILES['imagen']['tmp_name'], $ruta);
        $imgStmt = $pdo->prepare("INSERT INTO imagenes (noticia_id, url) VALUES (?, ?)");
        $imgStmt->execute([$noticia_id, $ruta]);
    }

    header("Location: admin.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Crear Noticia - TecnoNews</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: white;
            margin: 0;
            padding: 0;
            text-align: center;
        }


        .logo {
            height: 50px;
        }
        .titulo {
            flex-grow: 1;
            text-align: center;
            font-size: 1.1em;
            font-family: 'Orbitron', sans-serif;
            color: white;
        }
        .usuario-btn {
            background: #2e37a6;
            color: white;
            border: none;
            padding: 5px 10px;
            font-size: 0.75em;
            cursor: pointer;
            border-radius: 4px;
        }
        .container {
            padding: 20px;
        }
        .box {
            border: 2px solid black;
            padding: 20px;
            margin: 10px auto;
            max-width: 600px;
            text-align: left;
        }
        label {
            display: block;
            margin-top: 10px;
            font-weight: bold;
        }
        input[type="text"], textarea, select {
            width: 100%;
            padding: 8px;
            margin-top: 5px;
        }
        input[type="file"] {
            margin-top: 5px;
        }
        button {
            margin-top: 15px;
            padding: 10px 20px;
            border: 2px solid black;
            background: white;
            font-weight: bold;
            cursor: pointer;
        }
        .volver {
            margin-top: 20px;
            display: inline-block;
            text-decoration: none;
            color: black;
            border: 2px solid black;
            padding: 10px 20px;
        }
        .preview-container {
            margin-top: 15px;
            text-align: center;
        }
        #preview {
            max-width: 100%;
            max-height: 200px;
            display: none;
            border: 1px solid #ccc;
            margin-top: 10px;
        }
    </style>
</head>
<body>
<head>
    <meta charset="UTF-8">
    <title>TecnoNews</title>
    <style>
        body { font-family: 'Arial', sans-serif; background: #fff; margin: 0; }
        .header { background: #3f49c4; padding: 10px; color: white; text-align: center; font-size: 2em; font-family: 'Orbitron', sans-serif; }
        .nav { display: flex; justify-content: center; flex-wrap: wrap; background: #eee; padding: 10px; }
        .nav a { border: 1px solid #000; margin: 5px; padding: 10px 20px; text-decoration: none; color: #000; font-weight: bold; background: #fff; border-radius: 5px; }
        .search input { padding: 10px; margin: 0 5px; }
        .card { border: 2px solid #000; display: flex; margin: 10px auto; width: 80%; background: #fff; border-radius: 10px; overflow: hidden; box-shadow: 0 2px 5px rgba(0,0,0,0.1); }
        .image { width: 25%; background: #f0f0f0; }
        .content { padding: 20px; width: 75%; }
        .title { font-size: 1.5em; font-weight: bold; }
        .meta { color: #333; font-size: 0.9em; margin: 5px 0; }
        .desc { margin: 10px 0; }
        .vermas { color: #3f49c4; font-weight: bold; text-decoration: none; }
    </style>
</head>
<body>

<div class="header">
    <div style="display: flex; justify-content: space-between; align-items: center;">
        <img src="uploads/logo.png" alt="Logo" style="height: 50px;">    
        <div style="font-size: 1.2em;">TecnoNews</div>       
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

<div class="container">
    <div class="box">
        <h2>Crear Nueva Noticia</h2>
        <form method="post" enctype="multipart/form-data">
            <label for="titulo">Título:</label>
            <input type="text" name="titulo" id="titulo" required>

            <label for="contenido">Contenido:</label>
            <textarea name="contenido" id="contenido" required></textarea>

            <label for="categoria">Categoría:</label>
            <select name="categoria_id" id="categoria">
                <?php foreach ($categorias as $cat): ?>
                    <option value="<?= $cat['id'] ?>"><?= htmlspecialchars($cat['nombre']) ?></option>
                <?php endforeach; ?>
            </select>

            <label for="imagen">Imagen:</label>
            <input type="file" name="imagen" id="imagen" accept="image/*">

            <div class="preview-container">
                <img id="preview" alt="Vista previa de imagen">
            </div>

            <button type="submit">Guardar</button>
        </form>
    </div>
    <a class="volver" href="admin.php">← Volver al inicio</a>
</div>

<script>
    document.getElementById('imagen').addEventListener('change', function(event) {
        const archivo = event.target.files[0];
        const preview = document.getElementById('preview');

        if (archivo) {
            const reader = new FileReader();
            reader.onload = function(e) {
                preview.src = e.target.result;
                preview.style.display = 'block';
            };
            reader.readAsDataURL(archivo);
        } else {
            preview.src = '';
            preview.style.display = 'none';
        }
    });
</script>

</body>
</html>
