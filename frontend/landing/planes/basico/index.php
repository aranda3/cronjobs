<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Plan Básico - Crea tu Tienda</title>

  <!-- Bootstrap CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <!-- Animate.css -->
  <link href="https://cdn.jsdelivr.net/npm/animate.css@4.1.1/animate.min.css" rel="stylesheet">
  <style>
    body {
      background: #f9f9f9;
      font-family: 'Segoe UI', sans-serif;
    }
    .hero {
      background: linear-gradient(rgba(0,0,0,0.6), rgba(0,0,0,0.6)), url('https://images.unsplash.com/photo-1581092795360-9fe8c247c515?auto=format&fit=crop&w=1350&q=80') no-repeat center center;
      background-size: cover;
      color: white;
      padding: 100px 0;
    }
    .feature-icon {
      font-size: 2rem;
      color: #0d6efd;
    }
    .card img {
      max-height: 180px;
      object-fit: cover;
    }
  </style>
</head>
<body>

  <?php include 'frontend/landing/nav.php' ?>

  <!-- Hero -->
  <section class="hero text-center">
    <div class="container">
      <h1 class="display-4 animate__animated animate__fadeInDown">Plan Básico</h1>
      <p class="lead animate__animated animate__fadeInUp">Empieza a vender en línea con tu propia tienda profesional</p>
      <a href="#suscribirse" class="btn btn-primary btn-lg mt-3 animate__animated animate__fadeInUp">Suscribirme por $1999 MXN/mes</a>
    </div>
  </section>

  <!-- Información del plan -->
  <section class="container my-5">
    <div class="row text-center mb-4">
      <h2>¿Qué incluye el Plan Básico?</h2>
    </div>
    <div class="row g-4">
      <div class="col-md-4">
        <div class="card h-100 text-center p-3">
          <img src="https://cdn-icons-png.flaticon.com/512/1828/1828859.png" class="mx-auto mb-3" width="80">
          <h5>1 Administrador</h5>
          <p>Control total sobre productos, ventas, reportes y configuración de tienda.</p>
        </div>
      </div>
      <div class="col-md-4">
        <div class="card h-100 text-center p-3">
          <img src="https://cdn-icons-png.flaticon.com/512/3135/3135715.png" class="mx-auto mb-3" width="80">
          <h5>2 Vendedores</h5>
          <p>Usuarios que pueden registrar ventas y gestionar pedidos desde cualquier dispositivo.</p>
        </div>
      </div>
      <div class="col-md-4">
        <div class="card h-100 text-center p-3">
          <img src="https://cdn-icons-png.flaticon.com/512/891/891462.png" class="mx-auto mb-3" width="80">
          <h5>Panel de Control</h5>
          <p>Interfaz amigable para gestionar tu tienda con estadísticas en tiempo real.</p>
        </div>
      </div>
    </div>
  </section>

  <!-- Características adicionales -->
  <section class="bg-white py-5 border-top">
    <div class="container">
      <h2 class="text-center mb-4">Beneficios del sistema</h2>
      <div class="row g-4">
        <div class="col-md-6">
          <ul class="list-group">
            <li class="list-group-item">✔ Registro y control de productos con stock</li>
            <li class="list-group-item">✔ Reportes de ventas por día y vendedor</li>
            <li class="list-group-item">✔ Control de usuarios con roles</li>
            <li class="list-group-item">✔ Diseño responsive y acceso desde móvil</li>
          </ul>
        </div>
        <div class="col-md-6">
          <img src="https://images.unsplash.com/photo-1556740749-887f6717d7e4?auto=format&fit=crop&w=800&q=80" alt="Panel" class="img-fluid rounded shadow">
        </div>
      </div>
    </div>
  </section>

  <!-- Suscripción -->
  <section id="suscribirse" class="py-5 text-center bg-light">
    <div class="container">
      <h3 class="mb-4">¿Listo para empezar?</h3>
      <p class="mb-4">Suscríbete ahora y crea tu tienda con el Plan Básico por solo $1999 MXN al mes.</p>
      <a href="#" class="btn btn-success btn-lg">Iniciar Suscripción</a>
    </div>
  </section>

  <!-- Footer -->
  <footer class="bg-dark text-white text-center py-3">
    <p class="mb-0">&copy; <?= date('Y') ?> Changarrito. Todos los derechos reservados.</p>
  </footer>

  <!-- Scripts -->
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
