<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Iniciar sesión</title>
  <?php include 'frontend/head.php'; ?> 
</head>

<body class="bg-light d-flex align-items-center justify-content-center vh-100">

  <div class="card shadow p-4" style="min-width: 350px; max-width: 400px;">

    <h4 class="mb-4 text-center">Iniciar sesión</h4>

    <form id="form-login">

      <div class="mb-3">
        <label for="slug" class="form-label">Tienda</label>
        <input type="text" class="form-control" id="slug" required>
      </div>

      <div class="mb-3">
        <label for="email" class="form-label">Correo electrónico</label>
        <input type="email" class="form-control" id="email" value="morin@gmail.com" required>
      </div>

      <div class="mb-3">
        <label for="password" class="form-label">Contraseña</label>
        <input type="password" class="form-control" id="password" value="123456" required>
      </div>

      <div id="mensaje" class="text-danger small mb-3"></div>

      <button type="submit" class="btn btn-primary w-100">Entrar</button>
    </form>
  </div>

  <?php include 'frontend/tiendas/auth/js/script.php'; ?>



</body>
</html>
