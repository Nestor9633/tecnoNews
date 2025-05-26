<?php
session_start();
require_once 'conexion.php';

if (!isset($_SESSION['usuario_id']) || $_SESSION['rol'] !== 'admin') {
    header("Location: login.php");
    exit;
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $noticia_id = $_POST['noticia_id'];
    $imagen = $_FILES['imagen'];

    $nombreArchivo = basename($imagen["name"]);
    $ruta = "uploads/" . uniqid() . "_" . $nombreArchivo;

    if (move_uploaded_file($imagen["tmp_name"], $ruta)) {
        $stmt = $conn->prepare("INSERT INTO imagenes (noticia_id, url) VALUES (?, ?)");
        $stmt->bind_param("is", $noticia_id, $ruta);
        $stmt->execute();
        $stmt->close();
        header("Location: noticia.php?id=$noticia_id");
    } else {
        echo "Error al subir la imagen.";
    }

    $conn->close();
}
?>
