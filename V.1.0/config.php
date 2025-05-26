<?php

$host = 'localhost:3307';
$db = 'noticias_db';
$user = 'root';
$pass = ''; 

try {
    $pdo = new PDO("mysql:host=$host;dbname=$db;charset=utf8mb4", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Error de conexión: " . $e->getMessage());
}

function obtenerNoticias($pdo) {
    $sql = "SELECT 
                noticias.id,
                noticias.titulo,
                noticias.contenido,
                noticias.fecha,
                usuarios.nombre AS autor_nombre,
                categorias.nombre AS categoria_nombre
            FROM noticias
            INNER JOIN usuarios ON noticias.autor_id = usuarios.id
            INNER JOIN categorias ON noticias.categoria_id = categorias.id
            ORDER BY noticias.fecha DESC";

    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}
?>
