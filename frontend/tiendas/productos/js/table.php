 <script>
        
        document.addEventListener("DOMContentLoaded", async () => {

            let id_tienda = [];

            const tabla = $('#tablaProductos').DataTable({
                data: [],
                columns: [
                    { title: "Producto" },
                    { title: "Categoría" },
                    { title: "Precio" },
                    { title: "Stock" },
                    { title: "Activo" },
                    { title: "Acciones", orderable: false }
                ],
                language: {
                    url: "<?= BASE_URL . '/frontend/json/datatable-1.13.6-es-ES.json' ?>"
                }
            });

            try {
                const res = await fetch("<?= BASE_URL . '/productos' ?>", {
                    method: "POST",
                    headers: {"Content-Type": "application/json"},
                    body: JSON.stringify({
                        slug: localStorage.getItem("slug")
                    })
                });

                const data = await res.json();


                console.log(data);

                const productosConAcciones = data.productos.map(p => {
                    const acciones = `
                    <div style="display:flex;">
                        <!--<form method="POST" action="<?= BASE_URL ?>/<?= $slug ?>/producto/editar">
                            <input type="hidden" name="codigo" value="${p.codigo}">
                            <input type="hidden" name="tienda_id" value="${p.tienda_id}">
                            <button type="submit" class="btn btn-sm btn-warning">Editar</button>
                        </form>-->
                        <button class="btn btn-sm btn-warning me-2" onclick="editarProducto('${p.codigo}')">Editar</button>
                        <button class="btn btn-sm btn-danger"onclick="eliminarProducto('${p.codigo}')">Eliminar</button>
                    </div>
                    `;

                    return [
                        p.nombre,
                        p.categoria,
                        `S/ ${parseFloat(p.precio).toFixed(2)}`,
                        p.stock,
                        p.activo ? "✅" : "❌",
                        acciones
                    ];
                });

                tabla.clear().rows.add(productosConAcciones).draw();

            } catch (err) {
                console.error("Error al cargar productos:", err);
            }


        });

        // Simulación de funciones
        function editarProducto(codigo) {
            window.location.href = `<?= BASE_URL .'/' . $slug ?>/producto/editar/${codigo}`;
        }

        function eliminarProducto(codigo) {
            if (confirm("¿Estás seguro de eliminar el producto ID " + codigo + "?")) {
            alert("Producto eliminado (simulado).");
            }
        }

    </script>