<!DOCTYPE html>
<html lang="es">
<head>

  <meta charset="UTF-8">
  <title>Plan Avanzado</title>
  
  <script src="https://js.stripe.com/v3/"></script>
  <?php include 'frontend/head.php'; ?>
  <?php include 'css/style.php'; ?>

</head>
<body> 

    <script>
        if (!localStorage.getItem("token")) {
        window.location.href = "<?= BASE_URL . '/login' ?>"; 
        }
    </script>

    <a href="<?= $volver ?>" class="btn btn-outline-primary">
        Volver
    </a>

    <div class="bg-light d-flex align-items-center justify-content-center vh-100" >
        <div class="d-flex flex-row bg-white shadow rounded overflow-hidden" style="max-width: 900px; width: 100%;">
        
            <!-- Bloque izquierdo: descripción -->
            <div class="bg-primary text-white p-4 d-flex flex-column justify-content-center" style="width: 50%;">
                <h3>Plan Medio</h3>
                <p class="mb-1"><strong>$2999 MXN/mes</strong></p>
                <ul class="list-unstyled mt-3">
                    <li>▪️ 2 Administrador</li>
                    <li>▪️ 4 Vendedores</li>
                </ul>
                <p class="mt-3">Para negocios en crecimiento.</p>
            </div>

            <!-- Bloque derecho: formulario -->
            <?php include 'frontend/aplicacion/suscripciones/bloque_derecho.php' ?>

        </div>
    </div>

    <?php include 'frontend/aplicacion/suscripciones/js/error_modal.php' ?>

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


        }

        cargar();
        
    </script>
    
    <?php include $script ?>

</body>
</html>