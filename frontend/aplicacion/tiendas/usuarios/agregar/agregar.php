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

        <h2 class="mb-4" id="titulo"></h2>
        <div id="contenedor-tiendas"></div>


      <h2 class="mb-4">Agregar Usuario</h2>

      <a href="<?= BASE_URL . '/usuarios' ?>">volver</a>
      <br><br>

      <form id="form-agregar">

        <?php include $ruta . 'tiendas/usuarios/campos.php' ?>

        <button type="submit" class="btn btn-primary">Guardar</button>
      </form>
      <div id="mensaje" class="mt-3"></div>
    </div>

  </div>

  

  <?php include $ruta . 'js/script.php' ?>

  <script>

    let email = document.getElementById("email");
    let password = document.getElementById("password");
    let tienda = document.getElementById("tienda");

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

    document.getElementById("form-agregar").addEventListener("submit", async (e) => {
      e.preventDefault();

        let usuario = 
        {
            email: email.value,
            password: password.value,
            tienda: tienda.value
        }

        console.log(usuario);


      try {
        const res = await fetch("<?= BASE_URL . '/api/usuarios/add' ?>", {
          method: "POST",
          headers: {"Content-Type": "application/json"},
          body: JSON.stringify(usuario)
        });

        const data = await res.json();

        console.log(data);

        if(data.error){
          Swal.fire("Error", data.error, "error");
        }else{

          Swal.fire("✅ Éxito", "Usuario Agregado!", "success");
          
          email.value="";
          password.value="";
          tienda.value="";
        }

      } catch (err) {
        console.error("Error al cargar usuarios:", err);
      }
      
    });

  </script>

   <script>

        const contenedor = document.getElementById("contenedor-tiendas");

        async function cargar(id) {
            
          try{
            const token = localStorage.getItem("token");
            
            const res = await fetch("<?= BASE_URL . '/api/filtros'?>", {
               method: "POST",
              headers: {
                "Authorization": "Bearer " + token,
                "Content-Type": "application/json"
              },
              body: JSON.stringify({
                id:id
              })
            });

            const data = await res.json();

            console.log(data);

            /*if (data.error) {
                logout();
                return;
            } */

            if (data.usuarios >= data.plan_usuarios) {

                contenedor.innerHTML = `
                    <div class="alert alert-info mt-4">
                        Has alcanzado el límite de usuarios para tu plan.
                        <br><small>¿Necesitas más usuarios? Mejora tu plan de suscripción.</small>
                        <div class="mt-2">
                            <a href="<?= BASE_URL . '/upgrade/planes' ?>" class="btn btn-warning btn-sm">Upgrade de Plan</a>
                        </div>
                    </div>`;
            }else{
              contenedor.innerHTML = "";
            }


          } catch (err) {
            console.error("Error al cargar usuarios:", err);
          }
        }

      
        tienda.addEventListener('change', function(){
          let id = this.value;
          cargar(id);
        });
        
        
      
  </script>


</body>
</html>
