<?php
session_start();
require 'config.php';

if (!isset($_SESSION['usuario_id'])) {
    die('No tienes permisos para realizar esta acción.');
}

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    die('ID de noticia inválido.');
}

$id = (int)$_GET['id'];

$stmt = $pdo->prepare("SELECT autor_id FROM noticias WHERE id = ?");
$stmt->execute([$id]);
$noticia = $stmt->fetch();

if (!$noticia) {
    die('La noticia no existe.');
}

$usuario_id = $_SESSION['usuario_id'];
$usuario_tipo = $_SESSION['usuario_tipo'] ?? '';

if ($usuario_id !== $noticia['autor_id'] && $usuario_tipo !== 'admin') {
    die('No tienes permisos para borrar esta noticia.');
}

$imgStmt = $pdo->prepare("SELECT url FROM imagenes WHERE noticia_id = ?");
$imgStmt->execute([$id]);
$imagenes = $imgStmt->fetchAll();

foreach ($imagenes as $img) {
    if (file_exists($img['url'])) {
        unlink($img['url']);
    }
}

$pdo->prepare("DELETE FROM comentarios WHERE noticia_id = ?")->execute([$id]);
$pdo->prepare("DELETE FROM imagenes WHERE noticia_id = ?")->execute([$id]);
$pdo->prepare("DELETE FROM noticias WHERE id = ?")->execute([$id]);

header('Location: admin.php');
exit;
?>
