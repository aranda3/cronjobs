<?php

$usuario = null;

try {

  $pdo = getPDO();

  $stmt = $pdo->prepare("SELECT * FROM usuarios WHERE id = ? LIMIT 1");
  $stmt->execute([$id]);

  $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

  if (!$usuario) {
    echo "<div class='alert alert-danger'>Usuario no encontrado.</div>";
    exit;
  }
    
} catch (PDOException $e) {
    echo "<div class='alert alert-danger'>Error de base de datos: {$e->getMessage()}</div>";
    exit;
}

?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Crear Tienda</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>  
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    
      <script>

        if (!localStorage.getItem("token")) {
            window.location.href = "<?= BASE_URL . '/login' ?>"; 
        }

    </script>

    <?php include 'frontend/aplicacion/nav.php' ?>
    <br>
 
  <div id="contenido-2">

    <div class="container py-5">
      <h2 class="mb-4">Editar Usuario</h2>

      <a href="<?= BASE_URL . '/usuarios' ?>">volver</a>
      <br><br>

      <form id="form-editar">

        <?php include $ruta . 'tiendas/usuarios/campos.php' ?>

        <button type="submit" class="btn btn-primary">Guardar</button>
         <button class="btn btn-success">Cambiar Contraseña</button>
      </form>
      <div id="mensaje" class="mt-3"></div>
    </div>

  </div>




  <?php include $ruta . 'js/script.php' ?>

  <script>
    
    let email = document.getElementById("email");
    let contentPassword = document.getElementById("content-password");
    let tienda = document.getElementById("tienda");
    let id = "<?= $id ?>";

    contentPassword.style.display="none";

    email.value="<?= $usuario['email'] ?>";
    tienda.value="<?= $usuario['tienda_id'] ?>";

    
    async function cargar() {

        const token = localStorage.getItem("token");
            
        const res = await fetch("<?= BASE_URL . '/api/tiendas'?>", {
            headers: {
                "Authorization": "Bearer " + token,
                "Content-Type": "application/json"
            }
        });

        const data = await res.json();

        console.log(data.tiendas);

        if (data.error) {
            logout();
            return;
        } 

        let opciones = "";

        data.tiendas.forEach(tienda => {
         
            opciones += `<option value="${tienda.id}">${tienda.nombre}</option>`;
           
        });

        tienda.innerHTML = opciones;
    }

    cargar();

    document.getElementById("form-editar").addEventListener("submit", async (e) => {
      e.preventDefault();

      let usuario = 
      {
        email: email.value,
        tienda: tienda.value,
        id: id
      }

      console.log(usuario);
      
      try {
        const res = await fetch("<?= BASE_URL . '/api/usuarios/update' ?>", {
          method: "POST",
          headers: {"Content-Type": "application/json"},
          body: JSON.stringify(usuario)
        });

        const data = await res.json();

        console.log(data);

        if(data.error){
          Swal.fire("Error", data.error, "error");
        }else{
          Swal.fire("✅ Éxito", "Usuario Actualizado!", "success");
        }
        

      } catch (err) {
        console.error("Error al cargar usuarios:", err);
      }
      
    });

  </script>


</body>
</html>
