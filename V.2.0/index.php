<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>TecnoNews</title>
  <link href="https://fonts.googleapis.com/css2?family=Orbitron:wght@400;700&display=swap" rel="stylesheet">
  <style>
    body {
      margin: 0;
      font-family: Orbitron, sans-serif;
      background-color: #fff;
    }

    .top-bar {
      background-color: #414dca;
      color: white;
      position: relative;
      display: flex;
      justify-content: space-between;
      align-items: center;
      padding: 10px 20px;
    }

    .logo-container {
      display: flex;
      align-items: center;
    }

    .logo {
      height: 40px;
      margin-right: 10px;
    }

    .site-title {
      position: absolute;
      left: 50%;
      transform: translateX(-50%);
      font-size: 24px;
      margin: 0;
      font-weight: bold;
      font-family: 'Orbitron', sans-serif;
    }

    .iconos a {
      font-size: 24px;
      margin-left: 10px;
      color: white;
      text-decoration: none;
    }

    .contenido-principal {
      text-align: center;
      padding: 20px;
    }

    .imagen-principal {
      max-width: 100%;
      height: auto;
    }

    .botones {
      margin-top: 20px;
      font-size: 24px;
    }

    .btn {
      display: inline-block;
      margin: 0 10px;
      padding: 10px 20px;
      border: 2px solid black;
      text-decoration: none;
      color: black;
      font-weight: bold;
      border-radius: 5px;
      transition: background-color 0.3s ease;
    }

    .btn:hover {
      background-color: #ddd;
    }
  </style>
</head>
<body>

  <header class="top-bar">
    <div class="logo-container">
      <img src="uploads/logo.png" alt="Logo" class="logo">
    </div>

    <h1 class="site-title">TecnoNews</h1>

    <div class="iconos">
      <a href="register.php">➕</a>
      <a href="login.php">👤</a>
    </div>
  </header>

  <main class="contenido-principal">
    <img src="uploads/imgprincipalCText.png" alt="Imagen principal" class="imagen-principal">
    <div class="botones">
      <a href="login.php" class="btn">Iniciar sesión</a>
      <a href="register.php" class="btn">Registrarse</a>
    </div>
  </main>

</body>
</html>
