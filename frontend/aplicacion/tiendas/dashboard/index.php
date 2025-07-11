<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Panel de Administración - Mi Tienda</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css" rel="stylesheet">

  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

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

         <div  class="mb-4" style="font-size: 20px;"><span style="font-weight:700;">Tienda: </span><span id="info-tienda"><span></div>

            <div class="d-flex justify-content-between align-items-center mb-4">
                <button class="btn btn-success" id="estadisticas">Estadísticas</button>
                <button class="btn btn-success" id="roles">Roles</button>
            </div>

            <div id="visualizar"></div>

        </div>

    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>

        let usuarios = [];
        let venta = [];

        function createEstadisticas(){
            const contenido = 
            `

                        <!-- MÉTRICAS -->
                        <div class="row text-center mb-4">

                        <div class="col-md-3">
                            <div class="card">
                            <div class="card-body">
                                <h5>Ventas</h5>
                                <p class="h3" id="ventasTotal">0</p>
                            </div>
                            </div>
                        </div>

                        <div class="col-md-3" id="divStockTotal" style="cursor:pointer;" >
                            <div class="card">
                            <div class="card-body">
                                <h5>Stock</h5>
                                <p class="h3" id="stockTotal">0</p>
                            </div>
                            </div>
                        </div>

                        <div class="col-md-3">
                            <div class="card">
                            <div class="card-body">
                                <h5>Ingresos</h5>
                                <p class="h3 text-success" id="ingresos">$0</p>
                            </div>
                            </div>
                        </div>

                        <div class="col-md-3" id="divGastos" style="cursor:pointer;">
                            <div class="card">
                            <div class="card-body">
                                <h5>Gastos</h5>
                                <p class="h3 text-danger" id="gastos">$0</p>
                            </div>
                            </div>
                        </div>

                        </div>

                        <!-- CHART -->
                        <div class="card mb-4">
                        <div class="card-body">
                            <h5>Ingresos Semanales</h5><!--Ingresos y Gastos Semanales-->
                            <canvas id="graficoFinanzas"></canvas>
                        </div>
                        </div>`;

                        return contenido;
        }

        function createRoles(){
            
            const contenido = 
           `

                <h4>Buscar Usuarios</h4>
                <table id="tablaUsuarios" class="table table-bordered table-striped">
                    <thead class="table-dark">
                    <tr>
                        <th>Email</th>
                        <th>Rol</th>
                        <th></th>
                    </tr>
                    </thead>
                    <tbody></tbody>
                </table>

                <hr>

                <h4>Aplicar Roles</h4>
               
                <div style="display:fleX;">
                    <div class="col-md-4" style="margin-right:10px;">
                        <span style="font-weight:700">Email</span>
                        <input id="id-email" class="form-control" readonly></input>
                    </div>
                    <div class="col-md-4">
                        <span style="font-weight:700">Rol</span>
                        <select id="id-rol" class="form-control">
                            <option value="administrador">Administrador</option>
                            <option value="vendedor">Vendedor</option>
                            <option value="colaborador">Colaborador</option>
                        </select>
                        
                    </div>
                </div>

                <br>

                <button class="btn btn-primary" onclick="procesar()">Guardar</button>
            `;

            return contenido;
        }

        async function cargarVistaRol(){

            try {

                const tienda_id_aplicacion = localStorage.getItem("tienda_id_aplicacion");

                const res = await fetch("<?= BASE_URL . '/api/usuarios' ?>", {
                    method: "POST",
                    body: JSON.stringify({
                        tienda_id: tienda_id_aplicacion
                    })
                });

                const data = await res.json();

                const tabla = $('#tablaUsuarios').DataTable({
                    pageLength: 1,
                    lengthChange: false,
                    data: data.usuarios,
                    columns: [
                    { data: 'email' },
                    { data: 'rol' },
                    {
                        data: null,
                        render: (data, type, row) => {
                        return `<button class="btn btn-sm btn-primary agregar" data-id="${row.id}">Agregar</button>`;
                        }
                    }
                    ],
                    language: {
                        url: "<?= BASE_URL . '/frontend/json/datatable-1.13.6-es-ES.json' ?>"
                    }
                });

                console.log(data);

                usuarios = data.usuarios;

               

            } catch (err) {
                console.error("Error al cargar productos:", err);
            }

        }

        
        async function cargarPanel() {

            const token_aplicacion = localStorage.getItem("token");
            const tienda_id_aplicacion = localStorage.getItem("tienda_id_aplicacion");

            //console.log("token_aplicacion: ", token_aplicacion);
            console.log("tienda_id_aplicacion: ", tienda_id_aplicacion);
            
            const res = await fetch("<?= BASE_URL . '/ctrl/estadisticas'?>", {
                method: "POST",
                headers: { "Content-Type": "application/json" },
                body: JSON.stringify({
                    tienda_id: tienda_id_aplicacion
                })
            });

            const data = await res.json();

            let ventas = data.ventas;
            let productos = data.productos;
            let productos_totales = data.productos_totales;

            console.log("ventas: ", ventas);

            document.getElementById("ventasTotal").innerText = data.ventas.length;
            document.getElementById("ingresos").innerText = "$" + ingresos(ventas);
            document.getElementById("gastos").innerText = "$" + gastos(productos_totales);
            document.getElementById("stockTotal").innerText = stock(productos);
        
            // Inicializa un mapa de días con 0 ingresos
            const diasSemana = ['Dom', 'Lun', 'Mar', 'Mié', 'Jue', 'Vie', 'Sáb'];
            let ingresosPorDia = new Array(7).fill(0);

            // Obtener fecha actual y fecha hace 7 días
            const hoy = new Date();
            const hace7dias = new Date();       
            hace7dias.setDate(hoy.getDate() - 6); // para incluir hoy

            // Calcular ingresos semanales
            ventas.forEach(v => {
            const fechaVenta = new Date(v.fecha_registro);
            if (fechaVenta >= hace7dias && fechaVenta <= hoy) {
                const dia = fechaVenta.getDay(); // 0 = Domingo, 1 = Lunes, ...
                ingresosPorDia[dia] += parseFloat(v.total);
            }
            });


            cargarGrafico(ingresosPorDia);
            
        }

        function procesar(){

            if (venta.length === 0) {
                return Swal.fire('⚠️ Sin usuarios', 'Agrega al menos un usuario.', 'warning');
            }

            Swal.fire({
                title: '¿Confirmar asignación?',
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: 'Sí',
                cancelButtonText: 'Cancelar'
            }).then( async (result) => {
                        
                if (result.isConfirmed) {

                    const usuario = {
                        rol: document.getElementById("id-rol").value, 
                        id: venta[0].id
                    };

                    console.log(usuario);

                    try {

                        const res2 = await fetch("<?= BASE_URL . '/api/usuarios/rol_change' ?>", {
                            method: "POST",
                            headers: {"Content-Type": "application/json"},
                            body: JSON.stringify(usuario)
                        });

                        const data2 = await res2.json();

                        console.log(data2);
                            
                        if (data2.success) {
                            Swal.fire('✅ Usuario asignado', "", 'success');
                            venta.length = 0;
                            const email = document.getElementById("id-email");
                            email.value = "";
                            const rol = document.getElementById("id-rol");
                            rol.value = ""
                        } else {
                            Swal.fire('❌ Error', data2.error || 'Ocurrió un error.', 'error');
                        }

                    } catch (err2) {
                        console.error("Error al cargar usuarios:", err2);
                    }

                }
            });

        }

        function ingresos(ventas){

            let ingresos = 0;
            
            ventas.forEach(p => { 
            ingresos += parseFloat(p.total);
            });

            //console.log("ingresos: ", ingresos.toFixed(2));

            return ingresos.toFixed(2);

        }

        function gastos(productos){

            let gastos = 0;
            
            productos.forEach(p => {
            gastos += parseFloat(p.precio_compra) * parseInt(p.stock);
            //console.log((parseFloat(p.precio_compra) * parseInt(p.stock)).toFixed(2));
            });

            //console.log("gastos: ", gastos.toFixed(2));

            return gastos.toFixed(2);

        }

        function stock(productos){

            let stock = 0;
            
            productos.forEach(p => {
            if(parseInt(p.stock) == 5){
                stock ++;
                //console.log(p.stock);
            }
            
            });

            //console.log("stock: ", stock);

            return stock;
        }

        function cargarGrafico(ingresos) {
            const ctx = document.getElementById('graficoFinanzas').getContext('2d');
            new Chart(ctx, {
            type: 'line',
            data: {
                labels: ['Dom', 'Lun', 'Mar', 'Mié', 'Jue', 'Vie', 'Sáb'],
                datasets: [
                {
                    label: 'Ingresos últimos 7 días',
                    data: ingresos,
                    borderColor: 'green',
                    backgroundColor: 'rgba(0, 128, 0, 0.1)',
                    fill: false,
                    tension: 0.4
                }
                ]
            },
            options: {
                scales: {
                y: { beginAtZero: true }
                }
            }
            });
        }

        function mostrarProductos(productos) { 
            const tabla = document.getElementById("tablaProductos");
            tabla.innerHTML = "";
            productos.forEach(p => {
            const fila = `
                <tr>
                <td>${p.nombre}</td>
                <td>${p.stock}</td>
                <td>$${p.precio}</td>
                <td>
                    <button class="btn btn-sm btn-warning">Editar</button>
                    <button class="btn btn-sm btn-danger">Eliminar</button>
                </td>
                </tr>`; 
            tabla.innerHTML += fila;
            });
        }

        $(document).on('click', '.agregar', function () {

            const id = $(this).data('id');
            console.log("id: ", id);

            const usuario = usuarios.find(p => p.id === id);

            const yaExiste = venta.find(p => p.id === id);

            if (yaExiste) {
                Swal.fire("Error", "No puedes agregar el mismo usuario.", "error");
            } else {
                venta = [];
                venta.push({ ...usuario, cantidad: 1 });
                console.log(venta);
            }
            
            const email = document.getElementById("id-email");
            email.value = venta[0].email;
            const rol = document.getElementById("id-rol");
            rol.value = venta[0].rol;
        });

    </script>

    <script>

        function main(){

            const slug_aplicacion = localStorage.getItem("slug_aplicacion");
            document.getElementById("info-tienda").innerHTML = slug_aplicacion;
            

            const contenidoEstadistica = createEstadisticas();
            const contenidoRoles = createRoles();

            document.getElementById("visualizar").innerHTML = contenidoEstadistica;
            cargarPanel();
           
            document.getElementById("id-rol").value="";
            
            document.getElementById("estadisticas").addEventListener("click", function(){

                document.getElementById("visualizar").innerHTML = contenidoEstadistica;
                cargarPanel();

            });

            document.getElementById("roles").addEventListener("click", function(){
  
                document.getElementById("visualizar").innerHTML = contenidoRoles;
                cargarVistaRol();
                document.getElementById("id-rol").value="";
                //$('#tablaUsuarios').DataTable().destroy();

            });


        }

    </script>

    <script>

        //const fn = cargarPanel;
        main();

    </script>
  
</body>
</html>
