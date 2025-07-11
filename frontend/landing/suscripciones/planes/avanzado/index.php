<!DOCTYPE html>
<html lang="es">
<head>

  <meta charset="UTF-8">
  <title>Plan Avanzado</title>

  <script src="https://js.stripe.com/v3/"></script>
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <?php include 'frontend/aplicacion/suscripciones/head.php'; ?>
  <?php include $style; ?>

</head>
<body> 
 
  <a href="<?= $volver ?>" class="btn btn-outline-primary">
    Volver
  </a>
  
  <div class="bg-light d-flex align-items-center justify-content-center vh-100" >

    <div class="d-flex flex-row bg-white shadow rounded overflow-hidden" style="max-width: 900px; width: 100%;">
      
      <!-- Bloque izquierdo: descripción -->
      <div class="bg-primary text-white p-4 d-flex flex-column justify-content-center" style="width: 50%;">
        <h3>Plan Avanzado</h3>
        <p class="mb-1"><strong>$3999 MXN/mes</strong></p>
        <ul class="list-unstyled mt-3">
          <li>▪️ 3 Administrador</li>
          <li>▪️ 10 Vendedores</li>
        </ul>
        <p class="mt-3">Ideal para tiendas pequeñas que inician operaciones.</p>
      </div>

      <!-- Bloque derecho: formulario -->
      <?php include 'frontend/aplicacion/suscripciones/bloque_derecho.php' ?>

    </div>
  </div>

  <?php include 'frontend/aplicacion/suscripciones/js/error_modal.php' ?>

  <script> 

    const plan = "<?= $price_id = PLANES_DISPONIBLES['PLAN_AVANZADO']['price_id'] ?>";

    //const propietario_id = ?//= $_SESSION['propietario_id'] ?;

    const propietario_id = 1;
    const stripe_customer_id = "cus_SYqjMQqB9vX7sp"; 

    let datosStripe = null;
    
  </script> 

  <?php include $script ?>

</body>
</html>
