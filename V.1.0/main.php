<?php
session_start();
if (!isset($_SESSION['usuario_id'])) {
    header("Location: login.php");
    exit;
}

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
        .header {
            background: #3f49c4;
            padding: 10px 20px;
            color: white;
            font-size: 1.8em;
            font-family: 'Orbitron', sans-serif;
        }
        .nav { display: flex; justify-content: center; flex-wrap: wrap; background: #eee; padding: 10px; }
        .nav a { border: 1px solid #000; margin: 5px; padding: 10px 20px; text-decoration: none; color: #000; font-weight: bold; background: #fff; border-radius: 5px; }
        .search { text-align: center; margin: 20px 0; }
        .search input { padding: 10px; margin: 0 5px; }
        .card { border: 2px solid #000; display: flex; margin: 10px auto; width: 80%; background: #fff; border-radius: 10px; overflow: hidden; box-shadow: 0 2px 5px rgba(0,0,0,0.1); }
        .image { width: 25%; background: #f0f0f0; }
        .content { padding: 20px; width: 75%; }
        .title { font-size: 1.5em; font-weight: bold; }
        .meta { color: #333; font-size: 0.9em; margin: 5px 0; }
        .desc { margin: 10px 0; }
        .vermas { color: #00aaff; font-weight: bold; text-decoration: none; }
    </style>
</head>
<body>

<div class="header">
    <div style="display: flex; justify-content: space-between; align-items: center;">
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


<div class="nav">
    <?php while ($cat = $categorias->fetch_assoc()): ?>
        <a href="?categoria=<?= $cat['id'] ?>"><?= $cat['nombre'] ?></a>
    <?php endwhile; ?>
    <?php if ($_SESSION['tipo'] === 'admin'): ?>
        <a href="admin.php" style="background: #ffcc00;">🔧 Admin</a>
    <?php endif; ?>
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

<script>
function toggleUserInfo() {
    const info = document.getElementById("user-info");
    info.style.display = (info.style.display === "none" || info.style.display === "") ? "block" : "none";
}
</script>

</body>
</html>
