<?php
$conexion = new mysqli("localhost:3307", "root", "", "noticias_db");
if ($conexion->connect_error) {
    die("Conexión fallida: " . $conexion->connect_error);
}


$categorias = $conexion->query("SELECT * FROM categorias");

$palabra = $_GET['palabra'] ?? '';
$fecha = $_GET['fecha'] ?? '';
$categoria_id = $_GET['categoria'] ?? null;

$sql = "SELECT n.id, n.titulo, LEFT(n.contenido, 100) AS descripcion, n.fecha, u.nombre AS autor, c.nombre AS categoria, i.url 
        FROM noticias n
        JOIN usuarios u ON n.autor_id = u.id
        JOIN categorias c ON n.categoria_id = c.id
        LEFT JOIN imagenes i ON i.noticia_id = n.id
        WHERE 1";

if (!empty($palabra)) {
    $sql .= " AND (n.titulo LIKE '%$palabra%' OR n.contenido LIKE '%$palabra%')";
}
if (!empty($fecha)) {
    $sql .= " AND DATE(n.fecha) = '$fecha'";
}
if (!empty($categoria_id)) {
    $sql .= " AND c.id = $categoria_id";
}

$sql .= " ORDER BY n.fecha DESC";
$noticias = $conexion->query($sql);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>TecnoNews</title>
    <style>
        body { font-family: 'Arial', sans-serif; background: #fff; margin: 0; }
        .header { background: #3f49c4; padding: 10px; color: white; text-align: center; font-size: 2em; font-family: 'Orbitron', sans-serif; }
        .nav { display: flex; justify-content: center; flex-wrap: wrap; }
        .nav a { border: 1px solid #000; margin: 5px; padding: 10px 20px; text-decoration: none; color: #000; font-weight: bold; }
        .search { text-align: center; margin: 20px 0; }
        .search input { padding: 10px; margin: 0 5px; }
        .card { border: 2px solid #000; display: flex; margin: 10px auto; width: 80%; background: #fff; }
        .image { width: 25%; background: magenta; }
        .content { padding: 20px; width: 75%; }
        .title { font-size: 1.5em; font-weight: bold; }
        .meta { color: #333; font-size: 0.9em; margin: 5px 0; }
        .desc { margin: 10px 0; }
        .vermas { color: #00aaff; font-weight: bold; text-decoration: none; }
    </style>
</head>
<body>
    <div class="header">TecnoNews</div>

    <div class="nav">
        <?php while ($cat = $categorias->fetch_assoc()): ?>
            <a href="?categoria=<?= $cat['id'] ?>"><?= $cat['nombre'] ?></a>
        <?php endwhile; ?>
    </div>

    <div class="search">
        <form method="get">
            <input type="text" name="palabra" placeholder="Búsqueda por palabra" value="<?= htmlspecialchars($palabra) ?>">
            <input type="date" name="fecha" value="<?= htmlspecialchars($fecha) ?>">
            <button type="submit">Buscar</button>
        </form>
    </div>

    <?php while ($n = $noticias->fetch_assoc()): ?>
        <div class="card">
            <div class="image">
                <?php if ($n['url']): ?>
                    <img src="<?= $n['url'] ?>" alt="imagen" style="width: 100%; height: 100%; object-fit: cover;">
                <?php endif; ?>
            </div>
            <div class="content">
                <div class="title"><?= htmlspecialchars($n['titulo']) ?></div>
                <div class="meta"><?= htmlspecialchars($n['autor']) ?> | <?= date("d M Y", strtotime($n['fecha'])) ?></div>
                <div class="desc"><?= htmlspecialchars($n['descripcion']) ?>...</div>
                <a class="vermas" href="noticia.php?id=<?= $n['id'] ?>">Ver más.</a>
            </div>
        </div>
    <?php endwhile; ?>
</body>
</html>
