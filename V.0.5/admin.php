<?php
require 'config.php';

$noticias = obtenerNoticias($pdo);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Panel de Administración - Noticias</title>
</head>
<body>
    <h1>Panel de Administración</h1>
    <p><a href="crear_noticia.php">Crear Nueva Noticia</a></p>

    <?php if (count($noticias) === 0): ?>
        <p>No hay noticias para mostrar.</p>
    <?php else: ?>
        <table border="1" cellpadding="8" cellspacing="0">
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
                            <a href="eliminar_noticia.php?id=<?= $noticia['id'] ?>" onclick="return confirm('¿Seguro que quieres eliminar esta noticia?');">Eliminar</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>

    <p><a href="index.php">Volver al sitio público</a></p>
</body>
</html>
