<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Stock de Productos</title>

  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css" rel="stylesheet">

  <?php include $ruta . 'css/sidebar.php' ?>

</head>
<body class="bg-light">


   

    <!-- JS -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>

    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>

    <script>

        function createContenido2(){
            const contenido2 = 
            ` <div class="container mt-5" id="contenido-2" >
                <h3 class="mb-4">📦 Productos en Stock</h3>

                <table id="tablaStock" class="table table-bordered table-striped">
                <thead class="table-dark">
                    <tr>
                    <th>Código</th>
                    <th>Nombre</th>
                    <th>Categoría</th>
                    <th>Precio</th>
                    <th>Stock</th>
                    </tr>
                </thead>
                <tbody></tbody>
                </table>
            </div>`;

            return contenido2;
        } 

        async function cargarVistaStock(){

            try {
                const res = await fetch("<?= BASE_URL . '/productos' ?>", {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/json",
                        "Authorization": "Bearer " + localStorage.getItem("token_tienda")
                    },
                    body: JSON.stringify({
                        slug: localStorage.getItem("slug")
                    })
                });

                const data = await res.json();

                console.log(data);

                const tabla = $('#tablaStock').DataTable({
                    data: data.productos,
                    pageLength: 5,
                    lengthChange: false,
                    columns: [
                    { data: 'codigo' },
                    { data: 'nombre' },
                    { data: 'categoria' },
                    { 
                        data: 'precio_venta',
                        render: data => `S/ ${parseFloat(data).toFixed(2)}`
                    },
                    { 
                        data: 'stock',
                        render: function(data) {
                        const clase = data == 5 ? 'text-danger fw-bold' : '';
                        return `<span class="${clase}">${data}</span>`;
                        }
                    }
                    ],
                    language: {
                    url: 'https://cdn.datatables.net/plug-ins/1.13.6/i18n/es-ES.json'
                    }
                });

            } catch (err) {
                console.error("Error al cargar productos:", err);
            }
        }

    </script>

    <?php include $ruta . 'js/token-logout.php' ?>
    <?php include $ruta . 'js/main.php' ?>

    <script>

        const fn = cargarVistaStock;
        app(fn);

    </script>



</body>
</html>
