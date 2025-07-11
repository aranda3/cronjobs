 <script>
        
    function createContenido2(){
        const contenido2 = 
        `<div id="contenido-2">
    
            <div class="container py-5">

                <a class="btn btn-primary" href="<?= BASE_URL . '/' . $slug .'/producto/agregar' ?>">Nuevo</a>
                <br><br>

                <h2 class="mb-4">Productos de la Tienda</h2>
                <input type="text" id="buscadorProductos" class="form-control mb-4" placeholder="Buscar producto...">
                <div id="contenedorCards" class="row gy-4"></div>
                <!--<div class="table-responsive">
                <table id="tablaProductos" class="table table-bordered table-striped">
                    <thead class="table-dark">
                    <tr>
                        <th>Producto</th>
                        <th>Categoría</th>
                        <th>Precio</th>
                        <th>Stock</th>
                        <th>Activo</th>
                        <th>Acciones</th>
                    </tr>
                    </thead>
                    <tbody></tbody>
                </table>
                </div>-->
                <div class="d-flex justify-content-between align-items-center mt-3">
                <button id="anterior" class="btn btn-secondary btn-sm">Anterior</button>
                <span id="paginacionInfo"></span>
                <button id="siguiente" class="btn btn-secondary btn-sm">Siguiente</button>
                </div>

            </div>

        </div>`;

        return contenido2;
    }

    async function cargarVistaProductos(){

        let productosGlobal = [];
        let paginaActual = 1;
        const productosPorPagina = 5;
        const inputBuscador = document.getElementById("buscadorProductos");

        try {
            const res = await fetch("<?= BASE_URL . '/productos' ?>", {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    "Authorization": "Bearer " + token_tienda
                },
                body: JSON.stringify({
                    slug: localStorage.getItem("slug")
                })
            });

            const data = await res.json();
            productosGlobal = data.productos;
            renderizarCardsPaginados(productosGlobal);

        } catch (err) {
            console.error("Error al cargar productos:", err);
        }

        inputBuscador.addEventListener("input", () => {
            const termino = inputBuscador.value.toLowerCase();
            const filtrados = productosGlobal.filter(p => 
                p.nombre.toLowerCase().includes(termino) || 
                p.categoria.toLowerCase().includes(termino)
            );
            
            paginaActual = 1;
            renderizarCardsPaginados(filtrados);
        });
    
        function renderizarCardsPaginados(listaProductos = productosGlobal) {

            const totalPaginas = Math.ceil(listaProductos.length / productosPorPagina);
            console.log("listaProductos: ", listaProductos.length);
            console.log("productosPorPagina: ", productosPorPagina);
            console.log("totalPaginas: ", totalPaginas);

            for (let pagina = paginaActual; pagina <= totalPaginas; pagina++) {
                const inicio = (pagina - 1) * productosPorPagina;
                const fin = inicio + productosPorPagina;
                const productosPagina = listaProductos.slice(inicio, fin);
            
                console.log(`🧾 Página ${pagina}:`, productosPagina);
            }

            if (paginaActual > totalPaginas) paginaActual = 1;

            const inicio = (paginaActual - 1) * productosPorPagina;
            const fin = inicio + productosPorPagina;
            const productosPagina = listaProductos.slice(inicio, fin);

            renderizarCards(productosPagina);

            document.getElementById("paginacionInfo").textContent = `Página ${paginaActual} de ${totalPaginas}`;
        
            document.getElementById("anterior").disabled = paginaActual === 1;
            document.getElementById("siguiente").disabled = paginaActual === totalPaginas;

            renderizarCardsPaginados.listaActual = listaProductos;
        }
        
        function renderizarCards(productos) {
            const contenedor = document.getElementById("contenedorCards");
            contenedor.innerHTML = ""; 

            productos.forEach(p => {
                const card = document.createElement("div");
                card.className = "col-md-4 col-sm-6";

                card.innerHTML = `
                <div class="card shadow-sm h-100">
                    <div class="card-body">
                        <h5 class="card-title">${p.nombre}</h5>
                        <p class="card-text mb-1"><strong>Categoría:</strong> ${p.categoria}</p>
                        <p class="card-text mb-1"><strong>Precio:</strong> S/ ${parseFloat(p.precio_venta).toFixed(2)}</p>
                        <p class="card-text mb-1"><strong>Stock:</strong> ${p.stock}</p>
                        <p class="card-text mb-3"><strong>Activo:</strong> ${p.activo ? "✅" : "❌"}</p>
                        <div>
                            <button class="btn btn-warning btn-sm" onclick="editarProducto('${p.codigo}')">Editar</button>
                            <button class="btn btn-danger btn-sm" onclick="eliminarProducto('${p.codigo}')">Eliminar</button>
                        </div>
                    </div>
                </div>`;

                contenedor.appendChild(card);
            });
        }
        
        document.getElementById("anterior").addEventListener("click", () => {
            if (paginaActual > 1) {
                paginaActual--;
                renderizarCardsPaginados(renderizarCardsPaginados.listaActual);
            }
        });

        document.getElementById("siguiente").addEventListener("click", () => {
            const totalPaginas = Math.ceil(renderizarCardsPaginados.listaActual.length / productosPorPagina);
            if (paginaActual < totalPaginas) {
                paginaActual++;
                renderizarCardsPaginados(renderizarCardsPaginados.listaActual);
            }
        });

        // Simulación de funciones
        function editarProducto(codigo) {
            window.location.href = `<?= BASE_URL .'/' . $slug ?>/producto/editar/${codigo}`;
        }

        async function eliminarProducto(codigo) {

            if (confirm("¿Estás seguro de eliminar el producto con código " + codigo + "?")) {
                try {
                    const res = await fetch("<?= BASE_URL ?>/api/productos/eliminar", {
                        method: "POST",
                        headers: { "Content-Type": "application/json" },
                        body: JSON.stringify({
                            codigo,
                            slug: localStorage.getItem("slug")  
                        })
                    });

                    const data = await res.json();

                    if (data.success) {
                        // ✅ Volver a cargar productos actualizados
                        const resProductos = await fetch("<?= BASE_URL . '/productos' ?>", {
                            method: "POST",
                            headers: { "Content-Type": "application/json" },
                            body: JSON.stringify({
                                slug: localStorage.getItem("slug")
                            })
                        });

                        const productosData = await resProductos.json();
                        productosGlobal = productosData.productos;
                        renderizarCardsPaginados(productosGlobal);
                    } else {
                        console.log("No se pudo eliminar el producto: " + data.error);
                    }

                } catch (err) {
                    console.log("Error al eliminar producto:", err);
                    alert("Error al conectar con el servidor.");
                }
            }
        }

    }

</script>