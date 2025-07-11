<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Mis Tiendas</title>
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

    <div class="container py-5">
        <h2 class="mb-4" id="titulo"></h2>
        <div id="contenedor-tiendas"></div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <?php include 'frontend/aplicacion/js/script.php' ?>

    <script>

        const contenedor = document.getElementById("contenedor-tiendas");
        const titulo = document.getElementById("titulo");

        async function cargar() {

            const token = localStorage.getItem("token");
            
            const res = await fetch("<?= BASE_URL . '/api/tiendas'?>", {
                headers: {
                    "Authorization": "Bearer " + token,
                    "Content-Type": "application/json"
                }
            });

            const data = await res.json();

            console.log(data);

            if (data.error) {
                logout();
                return;
            } 

            if (data.tiendas.length >= data.limite) {
                contenedor.innerHTML += `
                    <div class="alert alert-info mt-4">
                        Has alcanzado el límite de tiendas para tu plan.
                        <br><small>¿Necesitas más tiendas? Mejora tu plan de suscripción.</small>
                        <div class="mt-2">
                            <a href="<?= BASE_URL . '/upgrade/planes' ?>" class="btn btn-warning btn-sm">Upgrade de Plan</a>
                        </div>
                    </div>`;
            } else {
                contenedor.innerHTML += `
                    <div class="text-end mt-3">
                        <a href="<?= BASE_URL . '/crear_tienda' ?>" class="btn btn-primary">Crear Tienda</a>
                    </div>
                    <br>
                    `;
            }

            if (data.tiendas.length === 0) {
                contenedor.innerHTML = `
                <div class="alert alert-warning">
                    No tienes tiendas creadas todavía.
                    <a href="<?= BASE_URL . '/crear_tienda' ?>" class="btn btn-primary btn-sm ms-3">Crear Tienda</a>
                </div>
                `;
            } else {

                titulo.innerHTML = `Mis Tiendas: (${data.planSeleccionado})`;

                data.tiendas.forEach(tienda => {
                    const card = document.createElement("div");
                    card.className = "card mb-3";
                    card.innerHTML = `
                        <div class="card-body d-flex justify-content-between align-items-center">
                        <div>
                            <h5 class="mb-1">${tienda.nombre}</h5>
                            <small class="text-muted">Estado: ${tienda.estado}</small>
                        </div>
                        <div>
                        <button class="btn btn-primary" onclick="editar('${tienda.id}')">Editar</button>
                        <button class="btn btn-danger" onclick="eliminar('${tienda.id}')">Eliminar</button>    
                        <button class="btn btn-success" onclick="entrar('${tienda.slug}', '${tienda.id}')">Entrar</button>  
                        </div>
                        </div>
                    `;
                    contenedor.appendChild(card);
                });
            }
        }


        cargar();

        function editar(id){
            localStorage.setItem("tienda_id_aplicacion", id);
            window.location.href = `<?= BASE_URL ?>/tiendas/update/${id}`;
        }

        async function eliminar(id){

            try{
                const res = await fetch("<?= BASE_URL ?>/api/tiendas/delete", {
                    method: "POST",
                    headers: {"Content-Type": "application/json"},
                    body: JSON.stringify({
                        id:id
                    })
                });

                const data = await res.json();

                console.log(data);
                
                document.getElementById("contenedor-tiendas").innerHTML="";

                cargar();

            }catch(err){
                console.error("Error al cargar tiendas:", err);
            }
            
        }

        function entrar(slug, id){

            localStorage.setItem("slug_aplicacion", slug);
            localStorage.setItem("tienda_id_aplicacion", id);
            //console.log("slug_aplicacion: ", slug);
            //console.log("tienda_id_aplicacion: ", id);
            window.location.href = `<?= BASE_URL ?>/estadisticas`;

        }
      
    </script>

</body>
</html>
