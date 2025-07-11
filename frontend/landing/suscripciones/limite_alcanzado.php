<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Suscripción de prueba</title>
  <script src="https://js.stripe.com/v3/"></script>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    #card-element {
      padding: 12px;
      border: 1px solid #ced4da;
      border-radius: 0.375rem;
      background: white;
    }

    #mensaje {
      margin-top: 20px;
      text-align: center;
      font-weight: 500;
    }
  </style>
</head>
<body > 

 <div class="limite-card text-center">
    <h1>⚠️ Límite de tiendas alcanzado</h1>
    <p class="text-muted mb-4">
      Tu suscripción actual (<strong><?= htmlspecialchars($plan['nombre']) ?></strong>)
      permite hasta <strong><?= $maxTiendas ?></strong> tienda(s).
    </p>
    <p>Para registrar más tiendas, considera actualizar tu plan.</p>
    <a href="<?= BASE_URL; ?>/planes" class="btn btn-primary mt-3">Ver otros planes</a>
  </div>
  
</body>
</html>
