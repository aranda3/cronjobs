<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Planes de Suscripción</title>

  <?php include 'frontend/head.php'; ?>
  <?php include 'frontend/aplicacion/suscripciones/css/style.php'; ?>

  <style>

    .card.disabled {
      opacity: 0.5;
      pointer-events: none;
      position: relative;
    }

    .card.disabled::after {
      content: "Plan no disponible";
      position: absolute;
      top: 10px;
      right: 10px;
      background: #6c757d;
      color: white;
      padding: 4px 8px;
      border-radius: 5px;
      font-size: 0.8rem;
    }

  </style>

</head>
<body>

  <script>
    if (!localStorage.getItem("token")) {
      window.location.href = "<?= BASE_URL . '/login' ?>"; 
    }
  </script>

    <?php include 'frontend/aplicacion/nav.php' ?>
    <br>

  <div class="container py-5">
    <h2 class="text-center mb-5">Elige tu plan</h2>
    <div id="contenedor-planes" class="row justify-content-center g-4"></div>
  </div>

</body>
</html>

<?php include 'frontend/aplicacion/js/script.php' ?>

<script>

  async function cargar() {

    const token = localStorage.getItem("token");
    const contenedor = document.getElementById("contenedor-planes");
  
    const res = await fetch("<?= BASE_URL . '/api/planes'?>", {
      headers: {
        "Authorization": "Bearer " + token,
        "Content-Type": "application/json"
      }
    });

    const data = await res.json();

    console.log(data);

    if (data.error) {
      //manejar errores
    } 

    const { nivelPlanActual, planes } = data;

    // Renderizar tarjetas
    for (const clave in planes) {
      const plan = planes[clave];
      const bloquear = plan.nivel <= nivelPlanActual;

      console.log(bloquear);

      const card = document.createElement("div");
      card.className = "col-md-4";

      card.innerHTML = `
        <div class="card shadow text-center h-100 ${bloquear ? 'disabled' : ''}">
          <div class="card-body">
            <h5 class="card-title">${plan.nombre}</h5>
            <p class="card-price">$${plan.precio.toLocaleString()} MXN/mes</p>
            <ul class="mb-4">
              <li>${plan.max_admins} Administrador(es)</li>
              <li>${plan.max_vendedores} Vendedor(es)</li>
            </ul>
            <a href="<?= BASE_URL ?>/upgrade/${plan.ruta}" class="btn btn-outline-primary w-100 ${bloquear ? 'disabled' : ''}">
              Suscribirme
            </a>
          </div>
        </div>
      `;

      contenedor.appendChild(card);
    }
  }

  cargar();
      
</script>

</body>
</html>