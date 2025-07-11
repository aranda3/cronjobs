<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Stock de Productos</title>

  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css" rel="stylesheet">

  <?php include $ruta . 'css/sidebar.php' ?>

  <style>
    .total { font-size: 1.5rem; font-weight: bold; }
  </style>

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

                <h3 class="mb-4">✅ Productos Activos</h3>

                <table id="tablaActivo" class="table table-bordered table-striped">
                    <thead class="table-dark">
                        <tr>
                            <th>Nombre</th>
                            <th>Precio Compra</th>
                            <th>Stock</th>
                            <th>Precio Compra x Stock</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>

                <p class="total">Total: S/ <span id="id-activo">0.00</span></p>

                <br>

                <h3 class="mb-4">❌ Productos Inactivos</h3>

                <table id="tablaInactivo" class="table table-bordered table-striped">
                    <thead class="table-dark">
                        <tr>
                            <th>Nombre</th>
                            <th>Precio Compra</th>
                            <th>Stock</th>
                            <th>Precio Compra x Stock</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>

                <p class="total">Total: S/ <span id="id-inactivo">0.00</span></p>

                
            </div>`;

            return contenido2;
        } 

        function calcularActivos(objeto){

            let arrayActivos = [];
            let activos = 0;
                
            objeto.forEach(p => {
                arrayActivos.push({
                    operacion: parseFloat(
                        (parseFloat(p.precio_compra).toFixed(2)) * parseInt(p.stock)
                    ).toFixed(2)
                });
            });

            arrayActivos.forEach(p => {
                activos += parseFloat(p.operacion);
            });

            return activos.toFixed(2);

        }

        function calcularInactivos(objeto){

            let arrayInactivos = [];
            let inactivos = 0;
                
            objeto.forEach(p => {
                arrayInactivos.push({
                    operacion: parseFloat(
                        (parseFloat(p.precio_compra).toFixed(2)) * parseInt(p.stock)
                    ).toFixed(2)
                });
            });

            arrayInactivos.forEach(p => {
                inactivos += parseFloat(p.operacion);
            });

            return inactivos.toFixed(2);

        }
        
        async function cargarVistaGastos(){
            
             try {
                const res = await fetch("<?= BASE_URL . '/ctrl/gastos' ?>", { 
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

                $('#tablaActivo').DataTable({ 
                    data: data.activos,
                    pageLength: 5,
                    lengthChange: false,
                    columns: [
                        { data: 'nombre' },
                        { data: 'precio_compra' },
                        { data: 'stock' },
                        { 
                            data: null,
                            render: (data, type, row) => {
                                return `S/ ${(parseFloat(row.precio_compra).toFixed(2) * parseInt(row.stock)).toFixed(2)}`
                            }
                        }
                    ],
                    language: {
                        url: "<?= BASE_URL . '/frontend/json/datatable-1.13.6-es-ES.json' ?>"
                    }
                });

                $('#tablaInactivo').DataTable({
                    data: data.inactivos,
                    pageLength: 5,
                    lengthChange: false,
                    columns: [
                        { data: 'nombre' },
                        { data: 'precio_compra' },
                        { data: 'stock' },
                        { 
                            data: null,
                            render: (data, type, row) => {
                                return `S/ ${(parseFloat(row.precio_compra).toFixed(2) * parseInt(row.stock)).toFixed(2)}`
                            }
                        }
                    ],
                    language: {
                        url: "<?= BASE_URL . '/frontend/json/datatable-1.13.6-es-ES.json' ?>"
                    }
                });

                let activos = calcularActivos(data.activos);
                let inactivos = calcularInactivos(data.inactivos);

                document.getElementById("id-activo").innerText = activos;
                document.getElementById("id-inactivo").innerText = inactivos;

                console.log("activos: ", activos);
                console.log("inactivos: ", inactivos);

            } catch (err) {  
                console.error("Error al cargar productos:", err);
            }
        }  

    </script>

    <?php include $ruta . 'js/token-logout.php' ?>
    <?php include $ruta . 'js/main.php' ?>

    <script>

        const fn = cargarVistaGastos;
        app(fn);

    </script>


</body>
</html>
