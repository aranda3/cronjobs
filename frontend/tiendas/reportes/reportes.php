<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Reporte de Ventas</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">

    <?php include $ruta . 'css/sidebar.php' ?>

    <style>
        .resumen {
            background: #f8f9fa;
            border: 1px solid #dee2e6;
            padding: 15px;
            border-radius: 5px;
        }
    </style>

</head>

<body class="p-4">

    <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>

    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
  
    <script>

        function createContenido2(){
            const contenido2 = 
            `<div id="contenido-2" >
                <h1 class="mb-4">📊 Reporte de Ventas</h1>

                <div class="row mb-4">
                    <div class="col-md-4 resumen">
                        <h5>Total ventas:</h5>
                        <p id="totalVentas" class="fs-4 text-success">S/ 0.00</p>
                    </div>
                    <div class="col-md-4 resumen">
                        <h5>Cantidad de ventas:</h5>
                        <p id="cantidadVentas" class="fs-4 text-primary">0</p>
                    </div>
                    <div class="col-md-4 resumen">
                        <h5>Última venta:</h5>
                        <p id="ultimaVenta" class="fs-6 text-muted">-</p>
                    </div>
                </div>

                <table id="tablaVentas" class="display table table-bordered">
                    <thead>
                        <tr>
                            <th>Usuario</th>
                            <th>Total (S/)</th>
                            <th>Fecha</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>

                <div class="modal fade modal-lg" id="detalleModal" tabindex="-1" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
                    <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content">
                        <div class="modal-header bg-success text-white">
                        <h5 class="modal-title">Detalles de la Venta</h5>
                        </div>
                        <div class="modal-body">
                            <table id="tablaDetVentas" class="display table table-bordered" style="width:100%;">
                                <thead>
                                    <tr>
                                        <th>Producto</th>
                                        <th>Precio (S/)</th>
                                        <th>Cantidad</th>
                                        <th>Subtotal</th>
                                    </tr>
                                </thead>
                                <tbody></tbody>
                            </table>
                        </div>
                        <div class="modal-footer" id="modal-footer">
                        <button class="btn btn-secondary" onclick="cerrar()">Cerrar</button>     
                        </div>
                    </div>
                    </div>
                </div>
            </div>`;

            return contenido2;
        }  

        async function cargarVistaReportes(){

            let ventas = [];
            let token_tienda = localStorage.getItem("token_tienda");
            const detalleModal = document.getElementById('detalleModal');
            const modal = new bootstrap.Modal(detalleModal);
            
            try {
                const res = await fetch("<?= BASE_URL . '/ctrl/reportes' ?>", {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/json",
                        "Authorization": "Bearer " + token_tienda
                    }
                });

                const data = await res.json();

                console.log(data);

                ventas = data.ventas;

                const tabla = $('#tablaVentas').DataTable({
                    data: data.ventas,
                    pageLength: 5,
                    lengthChange: false,
                    columns: [
                        { data: 'usuario_id' },
                        { 
                            data: 'total',
                            render: data => `S/ ${parseFloat(data).toFixed(2)}`
                        },
                        { data: 'fecha_registro' },
                        {
                            data: null,
                            render: (data, type, row) => {
                                return `<button class="btn btn-sm btn-primary" onclick="detalles(${row.id})">Detalles</button>`;
                            }
                        }
                    ],
                    language: {
                        url: "<?= BASE_URL . '/frontend/json/datatable-1.13.6-es-ES.json' ?>"
                    }
                });

            } catch (err) {
                console.error("Error al cargar productos:", err);
            }

            // Análisis
            let totalSuma = 0;
            let ultimaFecha = "";
            ventas.forEach(v => {
                totalSuma += parseFloat(v.total);
                if (v.fecha_registro > ultimaFecha) ultimaFecha = v.fecha_registro;
            });

            $('#totalVentas').text(`S/ ${totalSuma.toFixed(2)}`);
            $('#cantidadVentas').text(ventas.length);
            $('#ultimaVenta').text(ultimaFecha);

        }

        async function detalles(venta_id){

            try {

                const res = await fetch("<?= BASE_URL . '/ctrl/detventa' ?>", {
                    method: "POST",
                    headers: {"Content-Type": "application/json"},
                    body: JSON.stringify({
                        venta_id: venta_id
                    })
                });

                const data = await res.json();

                console.log(data);

                $('#tablaDetVentas').DataTable().destroy();

                const tabla = $('#tablaDetVentas').DataTable({
                    data: data.detventas,
                    pageLength: 5,
                    lengthChange: false,
                    columns: [
                        { data: 'producto_id' },
                        { data: 'venta_id' },
                        { data: 'cantidad' },
                        { 
                            data: 'subtotal',
                            render: data => `S/ ${parseFloat(data).toFixed(2)}`
                        },
                    ],
                    language: {
                        url: "<?= BASE_URL . '/frontend/json/datatable-1.13.6-es-ES.json' ?>"
                    }
                });

            } catch (err) {
                console.error("Error al cargar detalles:", err);
            }

            modal.show();

            console.log("venta_id: ", venta_id);
        }

        function cerrar(){
            modal.hide();
        }

    </script>

    <?php include $ruta . 'js/token-logout.php' ?>
    <?php include $ruta . 'js/main.php' ?>
        
    <script>

        const fn = cargarVistaReportes;
        app(fn);

    </script>

</body>
</html>
