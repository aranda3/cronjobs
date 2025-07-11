<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Planes de Suscripción</title>

  <?php include 'frontend/head.php'; ?>
  <?php include 'frontend/aplicacion/suscripciones/css/style.php'; ?>

</head>
<body>

<div class="container py-5">
  <h2 class="text-center mb-5">Elige tu plan</h2>

  <div class="row justify-content-center g-4">

    <?php foreach (PLANES_DISPONIBLES as $clave => $plan): ?>  
      <div class="col-md-4">
        <div class="card shadow text-center h-100"> 
          <div class="card-body">
            <h5 class="card-title"><?= $plan['nombre'] ?></h5>
            <p class="card-price">$<?= number_format($plan['precio'], 0) ?> MXN/mes</p>
            <ul class="mb-4">
              <li><?= $plan['max_admins'] ?> Administrador(es)</li>
              <li><?= $plan['max_vendedores'] ?> Vendedor(es)</li>
            </ul>
            <a href="<?= BASE_URL ?>/suscribirme/<?= $plan['ruta'] ?>" class="btn btn-outline-primary w-100">
              Suscribirme
            </a>
          </div>
        </div>
      </div>
    <?php endforeach; ?>

  </div>
</div>

</body>
</html>
