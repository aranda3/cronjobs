<!-- crear_tienda.php -->
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Crear Tienda</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    
    <script>

        if (!localStorage.getItem("token")) {
        window.location.href = "<?= BASE_URL . '/login' ?>"; 
        }

    </script>

  <div class="container py-5">
    <h2 class="mb-4">Registrar Nueva Tienda</h2>

    <a href="<?= BASE_URL . '/tiendas' ?>">volver</a>
    <br><br>

    <form id="form-tienda">

      <div class="mb-3">
        <label for="nombre" class="form-label">Nombre de la tienda</label>
        <input type="text" class="form-control" id="nombre" required>
      </div>

      <button type="submit" class="btn btn-primary" id="btn-guardar">Guardar</button>
    </form>

    <div id="mensaje" class="mt-3"></div>
  </div>

  <?php include 'frontend/aplicacion/js/script.php' ?>

  <script>

    //async function cargar() {

      const token = localStorage.getItem("token");

      document.getElementById("form-tienda").addEventListener("submit", async (e) => {
        e.preventDefault();

        const datos = {
          nombre: document.getElementById("nombre").value
        };

       
        const res = await fetch("<?= BASE_URL ?>/api/tiendas/crear", {
          method: "POST",
          headers: {
            "Content-Type": "application/json",
            "Authorization": "Bearer " + token
          },
          body: JSON.stringify(datos)
        });

        const data = await res.json();

        console.log(data);

        if (data.error) {
          document.getElementById("mensaje").innerHTML = `<div class="alert alert-danger">${data.error}</div>`;
        } else {
          document.getElementById("mensaje").innerHTML = `<div class="alert alert-success">Tienda creada correctamente</div>`;
          document.getElementById("btn-guardar").disabled = true;
        }
        
      });

    //}

    //cargar();

  </script>

</body>
</html>
