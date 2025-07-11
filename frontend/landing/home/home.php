<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Planes de Hosting - Suscríbete</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">

  <!-- Bootstrap 5 -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

  <!-- Animate.css -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css"/>

  <!-- jQuery -->
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

  <!-- Waypoints -->
  <script src="https://cdnjs.cloudflare.com/ajax/libs/waypoints/4.0.1/jquery.waypoints.min.js"></script>

  <style>
    body {
      scroll-behavior: smooth;
    }
    .hero {
      background: linear-gradient(to right, #1a1a1a, #333);
      color: white;
      padding: 100px 20px;
      text-align: center;
    }
    .plan {
      transition: transform 0.3s;
    }
    .plan:hover {
      transform: scale(1.05);
    }
    .beneficio-icono {
      font-size: 3rem;
      color: #007bff;
    }
  </style>

</head>
<body>

    <!-- Navbar -->
    <!-- Navbar con menú hamburguesa -->
    <?php include 'frontend/landing/nav.php' ?>


  <!-- Hero -->
  <section class="hero">
    <div class="container">
      <h1 class="display-4 animate__animated animate__fadeInDown">La tienda que tu mereces lo tienes aqui!</h1>
      <p class="lead mt-3 animate__animated animate__fadeInUp">Planes rápidos, seguros y con soporte 24/7</p>
      <a href="#planes" class="btn btn-primary btn-lg mt-4">Elige tu plan</a>
    </div>
  </section>

  <!-- Planes de Hosting -->
  <section id="planes" class="py-5 bg-light">
    <div class="container text-center">
      <h2 class="mb-4">Nuestros Planes</h2>
      <div class="row">
        <div class="col-md-4 mb-4 plan-anim">
          <div class="card plan">
            <div class="card-header bg-primary text-white">Básico</div>
            <div class="card-body">
              <h3>$1999MXN/mes</h3>
              <ul class="list-unstyled mt-3 mb-4">
                <li>1 Administrador</li>
                <li>2 Vendedores</li>
              </ul>
              <a href="#" class="btn btn-outline-primary">Suscribirme</a>
            </div>
          </div>
        </div>
        <div class="col-md-4 mb-4 plan-anim">
          <div class="card plan">
            <div class="card-header bg-success text-white">Medio</div>
            <div class="card-body">
              <h3>$2999MXN/mes</h3>
              <ul class="list-unstyled mt-3 mb-4">
                <li>2 Administrador</li>
                <li>4 Vendedores</li>
              </ul>
              <a href="#" class="btn btn-outline-success">Suscribirme</a>
            </div>
          </div>
        </div>
        <div class="col-md-4 mb-4 plan-anim">
          <div class="card plan">
            <div class="card-header bg-dark text-white">Avanzado</div>
            <div class="card-body">
              <h3>$3999MXN/mes</h3>
              <ul class="list-unstyled mt-3 mb-4">
                <li>3 Administrador</li>
                <li>10 Vendedores</li>
              </ul>
              <a href="#" class="btn btn-outline-dark">Suscribirme</a>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>

  <!-- Beneficios -->
  <section class="py-5">
    <div class="container text-center">
      <h2 class="mb-5">¿Por qué elegirnos?</h2>
      <div class="row">
        <div class="col-md-3 beneficio-anim">
          <div class="beneficio-icono mb-3">🚀</div>
          <h5>Alta velocidad</h5>
          <p>Servidores optimizados para máximo rendimiento.</p>
        </div>
        <div class="col-md-3 beneficio-anim">
          <div class="beneficio-icono mb-3">🔒</div>
          <h5>Seguridad avanzada</h5>
          <p>Protección anti-DDoS, SSL y firewall incluidos.</p>
        </div>
        <div class="col-md-3 beneficio-anim">
          <div class="beneficio-icono mb-3">💡</div>
          <h5>Fácil de usar</h5>
          <p>Panel intuitivo, soporte técnico disponible 24/7.</p>
        </div>
        <div class="col-md-3 beneficio-anim">
          <div class="beneficio-icono mb-3">📞</div>
          <h5>Soporte humano</h5>
          <p>No bots. Personas reales que te ayudan en minutos.</p>
        </div>
      </div>
    </div>
  </section>

  <!-- Contacto -->
  <section class="bg-light py-5">
    <div class="container">
      <h2 class="text-center mb-4">¿Tienes dudas?</h2>
      <form class="w-50 mx-auto contacto-anim">
        <div class="mb-3">
          <input type="text" class="form-control" placeholder="Tu nombre">
        </div>
        <div class="mb-3">
          <input type="email" class="form-control" placeholder="Tu correo">
        </div>
        <div class="mb-3">
          <textarea class="form-control" rows="4" placeholder="Escribe tu mensaje..."></textarea>
        </div>
        <div class="text-center">
          <button class="btn btn-primary">Enviar mensaje</button>
        </div>
      </form>
    </div>
  </section>

  <!-- Footer -->
  <footer class="bg-dark text-white text-center py-4">
    © 2025 Changarrito - Todos los derechos reservados.
  </footer>

  <!-- Bootstrap 5 JS (necesario para menú hamburguesa) -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>


  <!-- Animaciones con jQuery + Waypoints -->
  <script>
    // Animar los planes
    $('.plan-anim').waypoint(function() {
      $(this.element).addClass('animate__animated animate__fadeInUp');
    }, {
      offset: '80%'
    });

    // Animar beneficios
    $('.beneficio-anim').waypoint(function() {
      $(this.element).addClass('animate__animated animate__fadeIn');
    }, {
      offset: '85%'
    });

    // Animar formulario
    $('.contacto-anim').waypoint(function() {
      $(this.element).addClass('animate__animated animate__fadeInLeft');
    }, {
      offset: '90%'
    });
  </script>

</body>
</html>
