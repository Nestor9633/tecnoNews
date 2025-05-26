<?php
session_start();
require 'config.php';

if (!isset($_SESSION['usuario_id'])) {
    header('Location: login.php');
    exit;
}

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    die('ID de noticia inválido.');
}

$id = (int)$_GET['id'];

// Obtener la noticia
$stmt = $pdo->prepare("SELECT * FROM noticias WHERE id = ?");
$stmt->execute([$id]);
$noticia = $stmt->fetch();

if (!$noticia) {
    die('Noticia no encontrada.');
}

// Obtener categorías
$categorias = $pdo->query("SELECT * FROM categorias")->fetchAll();

// Obtener la imagen asociada
$stmt = $pdo->prepare("SELECT * FROM imagenes WHERE noticia_id = ?");
$stmt->execute([$id]);
$imagen = $stmt->fetch();

// Procesar edición
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $titulo = trim($_POST['titulo']);
    $contenido = trim($_POST['contenido']);
    $categoria_id = (int)$_POST['categoria_id'];

    if ($titulo && $contenido && $categoria_id) {
        $stmt = $pdo->prepare("UPDATE noticias SET titulo = ?, contenido = ?, categoria_id = ? WHERE id = ?");
        $stmt->execute([$titulo, $contenido, $categoria_id, $id]);

        if (isset($_FILES['imagen']) && $_FILES['imagen']['error'] === UPLOAD_ERR_OK) {
            $nombre_archivo = basename($_FILES['imagen']['name']);
            $ruta = 'uploads/' . uniqid() . '_' . $nombre_archivo;
            move_uploaded_file($_FILES['imagen']['tmp_name'], $ruta);

            if ($imagen) {
                $stmt = $pdo->prepare("UPDATE imagenes SET url = ? WHERE noticia_id = ?");
                $stmt->execute([$ruta, $id]);
            } else {
                $stmt = $pdo->prepare("INSERT INTO imagenes (noticia_id, url) VALUES (?, ?)");
                $stmt->execute([$id, $ruta]);
            }
        }

        header("Location: noticia.php?id=$id");
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Editar Noticia</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f1f1f1;
            margin: 0;
            padding: 40px 20px;
        }

        .container {
            max-width: 800px;
            margin: auto;
            background: white;
            padding: 30px;
            border: 2px solid #000;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }

        h1 {
            text-align: center;
            font-size: 2em;
            margin-bottom: 25px;
        }

        form {
            display: flex;
            flex-direction: column;
            gap: 15px;
        }

        label {
            font-weight: bold;
            margin-bottom: 5px;
        }

        input[type="text"],
        textarea,
        select {
            width: 100%;
            padding: 12px;
            border: 1px solid #ccc;
            border-radius: 4px;
            font-size: 1em;
        }

        input[type="file"] {
            padding: 5px 0;
        }

        button {
            background-color: #3f49c4;
            color: white;
            padding: 12px;
            font-size: 1em;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }

        button:hover {
            background-color: #2d36a8;
        }

        .imagen-actual,
        .imagen-preview {
            max-width: 100%;
            overflow: hidden;
            margin-top: 10px;
        }

        .imagen-actual img,
        .imagen-preview img {
            width: 100%;
            height: auto;
            max-height: 300px;
            object-fit: contain;
            display: block;
            border-radius: 4px;
        }

        .volver {
            display: block;
            text-align: center;
            margin-top: 25px;
            color: #3f49c4;
            font-weight: bold;
            text-decoration: none;
        }

        .volver:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>

<div class="container">
    <h1>Editar Noticia</h1>

    <form action="" method="post" enctype="multipart/form-data">
        <label for="titulo">Título:</label>
        <input type="text" name="titulo" id="titulo" value="<?= htmlspecialchars($noticia['titulo']) ?>" required>

        <label for="contenido">Contenido:</label>
        <textarea name="contenido" id="contenido" rows="10" required><?= htmlspecialchars($noticia['contenido']) ?></textarea>

        <label for="categoria_id">Categoría:</label>
        <select name="categoria_id" id="categoria_id" required>
            <?php foreach ($categorias as $cat): ?>
                <option value="<?= $cat['id'] ?>" <?= $cat['id'] == $noticia['categoria_id'] ? 'selected' : '' ?>>
                    <?= htmlspecialchars($cat['nombre']) ?>
                </option>
            <?php endforeach; ?>
        </select>

        <label for="imagen">Cambiar imagen (opcional):</label>
        <input type="file" name="imagen" id="imagen" accept="image/*">

        <?php if ($imagen): ?>
            <div class="imagen-actual">
                <label>Imagen actual:</label>
                <img src="<?= htmlspecialchars($imagen['url']) ?>" alt="Imagen actual">
            </div>
        <?php endif; ?>

        <div class="imagen-preview" id="previewContainer" style="display:none;">
            <label>Vista previa de nueva imagen:</label>
            <img id="previewImage" src="" alt="Vista previa">
        </div>

        <button type="submit">Guardar cambios</button>
    </form>

    <a class="volver" href="noticia.php?id=<?= $id ?>">← Volver a la noticia</a>
</div>

<script>
    const input = document.getElementById('imagen');
    const previewContainer = document.getElementById('previewContainer');
    const previewImage = document.getElementById('previewImage');

    input.addEventListener('change', function () {
        const file = this.files[0];
        if (file) {
            previewContainer.style.display = 'block';
            const reader = new FileReader();
            reader.onload = function (e) {
                previewImage.src = e.target.result;
            };
            reader.readAsDataURL(file);
        } else {
            previewContainer.style.display = 'none';
            previewImage.src = '';
        }
    });
</script>

</body>
</html>
