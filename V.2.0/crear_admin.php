<?php
$conexion = new mysqli("localhost:3307", "root", "", "noticias_db");

if ($conexion->connect_error) {
    die("Conexión fallida: " . $conexion->connect_error);
}

$nombre = "Admin Principal";
$email = "admin@tecnonews.com";
$contraseña = password_hash("admin123", PASSWORD_DEFAULT);
$tipo = "admin";

$stmt = $conexion->prepare("INSERT INTO usuarios (nombre, email, contraseña, tipo) VALUES (?, ?, ?, ?)");
$stmt->bind_param("ssss", $nombre, $email, $contraseña, $tipo);

if ($stmt->execute()) {
    echo "✅ Administrador creado correctamente.";
} else {
    echo "❌ Error: " . $stmt->error;
}

$stmt->close();
$conexion->close();
?>
