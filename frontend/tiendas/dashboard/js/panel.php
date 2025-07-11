<script>
  
  function createContenido2(){

    const contenido2 = 
    `<div id="contenido-2">

      <div class="container py-5">
        <div class="d-flex justify-content-between align-items-center mb-4">
          <h2>Panel de Administración</h2>
        </div>

        <div id="info-usuario" class="mb-4"></div>

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
        </div>

      </div>

    </div>`
    
    return contenido2;

  }

  async function cargarPanel() {
      
    const res = await fetch("<?= BASE_URL . '/ctrl/panel'?>", {
      headers: {
        "Authorization": "Bearer " + token_tienda
      }
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

    document.getElementById("divGastos").addEventListener("click", function(){

      window.location.href="<?= BASE_URL . '/' . $slug . '/gastos' ?>";

    });

    document.getElementById("divStockTotal").addEventListener("click", function(){

      window.location.href="<?= BASE_URL . '/' . $slug . '/stock' ?>";

    });

    /*document.getElementById("info-usuario").innerText = `Bienvenido ${data.email}`;
   
    */

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
//mostrarProductos(data.productos);
  
          /*{
            label: 'Gastos',
            data: datos.gastos,
            borderColor: 'red',
            fill: false
          }*/

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

  /*function agregarProducto() { 
    Swal.fire({
      title: 'Agregar Producto',
      html:
        '<input id="nombre" class="swal2-input" placeholder="Nombre">' +
        '<input id="stock" type="number" class="swal2-input" placeholder="Stock">' +
        '<input id="precio" type="number" class="swal2-input" placeholder="Precio">',
      confirmButtonText: 'Guardar',
      focusConfirm: false,
      preConfirm: () => {
        const nombre = document.getElementById('nombre').value;
        const stock = document.getElementById('stock').value;
        const precio = document.getElementById('precio').value;
        // Enviar a la API...
      }
    });
  }*/

  

</script>