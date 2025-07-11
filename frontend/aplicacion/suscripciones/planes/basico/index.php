<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Suscripción de prueba</title>
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <?php include 'frontend/head.php'; ?> 
  <?php include 'css/style.php'; ?>
  
</head>
<body> 

  <a href="<?= $volver ?>" class="btn btn-outline-primary">
    Volver
  </a>

  <div class="bg-light d-flex align-items-center justify-content-center vh-100" >

    <div class="d-flex flex-row bg-white shadow rounded overflow-hidden" style="max-width: 900px; width: 100%;">
      
      <!-- Bloque izquierdo: descripción -->
      <div class="bg-primary text-white p-4 d-flex flex-column justify-content-center" style="width: 50%;">
        <h3>Plan Básico</h3>
        <p class="mb-1"><strong>$1999 MXN/mes</strong></p>
        <ul class="list-unstyled mt-3">
          <li>▪️ 1 Administrador</li>
          <li>▪️ 2 Vendedores</li>
        </ul>
        <p class="mt-3">Ideal para tiendas pequeñas que inician operaciones.</p>
      </div>

      <!-- Bloque derecho: formulario -->
      <?php include 'frontend/aplicacion/suscripciones/bloque_derecho.php' ?>

    </div>
  </div>

  <?php include 'frontend/aplicacion/suscripciones/modal.php' ?>

  <?php include 'frontend/aplicacion/suscripciones/js/script.php' ?>

  <?php include $script ?>

</body>
</html>
