<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>TecnoNews</title>
  <style>
    body {
      margin: 0;
      font-family: Arial, sans-serif;
      background-color: #f7f7f7;
      color: #333;
    }

    .top-bar {
      background-color: #4444cc;
      color: white;
      padding: 10px 20px;
      display: flex;
      justify-content: space-between;
      align-items: center;
    }

    .logo-container {
      display: flex;
      align-items: center;
    }

    .logo {
      height: 50px;
      margin-right: 10px;
    }

    .site-title {
      font-size: 24px;
      margin: 0;
    }

    .icons a {
      color: white;
      text-decoration: none;
      font-size: 24px;
      margin-left: 15px;
      transition: color 0.3s;
    }

    .icons a:hover {
      color: #ccc;
    }

    .contenido-principal {
      display: flex;
      flex-direction: column;
      align-items: center;
      margin-top: 50px;
    }

    .imagen-principal {
      max-width: 90%;
      height: auto;
      border-radius: 10px;
    }

    .botones {
      text-align: center;
      margin-top: 40px;
    }

    .btn {
      text-decoration: none;
      border: 2px solid black;
      padding: 10px 25px;
      margin: 10px;
      font-size: 18px;
      color: black;
      background-color: white;
      font-weight: bold;
      transition: background 0.3s;
    }

    .btn:hover {
      background-color: #e0e0e0;
    }
  </style>
</head>
<body>

  <header class="top-bar">
    <div class="logo-container">
      <img src="uploads/logo.png" alt="Logo" class="logo">
      <h1 class="site-title">TecnoNews</h1>
    </div>
    <div class="icons">
      <a href="register.php" title="Registrarse">➕</a>
      <a href="login.php" title="Iniciar sesión">👤</a>
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

