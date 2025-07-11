<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Mis Tiendas</title>

  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">

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

            <a class="btn btn-primary" href="<?= BASE_URL . '/usuario/agregar' ?>">Nuevo</a>
            <br><br>

            <h2 class="mb-4">Usuarios</h2>

            <div class="table-responsive">
                <table class="table table-bordered table-striped" id="tablaUsuarios">
                    <thead>
                        <tr>
                            <th>Email</th>
                            <th>Rol</th>
                            <th>Tienda</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>

        </div>

    </div>
  
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

  <!-- DataTables -->
  <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
  <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>



   <script>
        
        document.addEventListener("DOMContentLoaded", async () => {

            const tabla = $('#tablaUsuarios').DataTable({
                pageLength: 5,
                lengthChange: false,
                data: [],
                columns: [
                    { title: "Email" },
                    { title: "Rol" },
                    { title: "Tienda" },
                    { title: "Acciones", orderable: false }
                ],
                language: {
                    url: "<?= BASE_URL . '/frontend/json/datatable-1.13.6-es-ES.json' ?>"
                }
            });

            try {

                const tienda_id_aplicacion = localStorage.getItem("tienda_id_aplicacion");

                const res = await fetch("<?= BASE_URL . '/api/usuarios' ?>", {
                    method: "POST",
                    body: JSON.stringify({
                        tienda_id: tienda_id_aplicacion
                    })
                });

                const data = await res.json();


                console.log(data);

                const usuariosConAcciones = data.usuarios.map(p => {
                    const acciones = `
                    <div style="display:flex;">
                        <button class="btn btn-sm btn-warning me-2" onclick="editarProducto('${p.id}')">Editar</button>
                        <button class="btn btn-sm btn-danger"onclick="eliminarProducto('${p.id}')">Eliminar</button>
                    </div>
                    `;

                    return [
                        p.email,
                        p.rol,
                        p.tienda_id,
                        acciones
                    ];
                });

                tabla.clear().rows.add(usuariosConAcciones).draw();

            } catch (err) {
                console.error("Error al cargar productos:", err);
            }


        });

        // Simulación de funciones
        function editarProducto(id) {
            window.location.href = `<?= BASE_URL ?>/usuario/editar/${id}`;
        }

        function eliminarProducto(id) {
            if (confirm("¿Estás seguro de eliminar el producto ID " + id + "?")) {
            alert("Producto eliminado (simulado).");
            }
        }

    </script>


</body>
</html>
