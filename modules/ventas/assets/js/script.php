<?php include 'modules/ventas/assets/js/VentaUI.php' ?>

<script> 

    let usuario_id = [];

    let tienda_id = "<?= $tienda_id ?>";
    console.log("tienda_id: ", tienda_id);

    let productos = [];
    let venta = [];

    function createContenido2(){
        return new VentaUI().render();
    }

    async function cargarVistaVenta(){

        try {

            const res = await fetch("<?= BASE_URL . '/api/productosEnStock' ?>", {
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

            const tabla = $('#tablaProductos').DataTable({
                destroy:true,
                pageLength: 1,
                lengthChange: false,
                data: data.productos,
                columns: [
                    { data: 'nombre' },
                    { data: 'precio_venta' },
                    { data: 'stock' },
                    {
                        data: null,
                        render: (data, type, row) => {
                            return `<button class="btn btn-sm btn-primary agregar" data-codigo="${row.codigo}">Agregar</button>`;
                        }
                    }
                ],
                language: {
                        url: "<?= BASE_URL . '/frontend/json/datatable-1.13.6-es-ES.json' ?>"
                }
            });

            console.log(data);
            productos = data.productos;
            usuario_id = data.usuario_id;

            $('#procesarVenta').on('click', () => {
                if (venta.length === 0) {
                    return Swal.fire('⚠️ Sin productos', 'Agrega al menos un producto a la venta.', 'warning');
                }

                Swal.fire({
                    title: '¿Confirmar venta?',
                    text: `Total: S/ ${$('#totalVenta').text()}`,
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonText: 'Sí, vender',
                    cancelButtonText: 'Cancelar'
                }).then(result => {
                    if (result.isConfirmed) {

                        let total = parseFloat($('#totalVenta').text());
                        total = total.toFixed(2);

                        const datosVenta = {
                            tienda_id: tienda_id, 
                            usuario_id: usuario_id, // Igual que tienda_id
                            total,
                            productos: venta.map(p => ({
                            producto_id: p.id, // ⚠️ asegúrate de tener el ID real
                            precio_venta: p.precio_venta,
                            cantidad: 
                                p.cantidad,
                                subtotal: (p.precio_venta * p.cantidad).toFixed(2)
                            }))
                        };

                        console.log(datosVenta);

                        fetch("<?= BASE_URL ?>/api/ventas/crear", {
                            method: "POST",
                            headers: { "Content-Type": "application/json" },
                            body: JSON.stringify(datosVenta)
                        })
                        .then(res => res.json())
                        .then(data => {

                            if (data.success) {

                                Swal.fire('✅ Venta registrada', 'ID de venta: ' + data.venta_id, 'success');
                                venta.length = 0;
                                actualizarTablaVenta();
                                    
                                refrescar();


                            } else if (data.faltantes) {
                                // ⚠️ Mostrar mensaje claro
                                let mensaje = 'Los siguientes productos no tienen suficiente stock:\n\n';
                                data.faltantes.forEach(f => {
                                    const prod = venta.find(p => p.id === f.producto_id);
                                    mensaje += `${prod?.nombre || 'Producto'}: disponible ${f.stock_disponible}\n`;

                                    if (f.stock_disponible > 0) {
                                        prod.cantidad = f.stock_disponible;
                                    } else {
                                        // Eliminar si ya no hay nada
                                        venta = venta.filter(p => p.id !== f.producto_id);
                                    }
                                });

                                actualizarTablaVenta();

                                Swal.fire('⚠️ Stock insuficiente', mensaje, 'warning');
                                
                            }else {
                                Swal.fire('❌ Error', data.error || 'Ocurrió un error.', 'error');
                            }
                        })
                        .catch(err => {
                            console.error("Error al enviar venta:", err);
                            Swal.fire('❌ Error', 'No se pudo procesar la venta.', 'error');
                        });

                    }
                });
            });

                

        } catch (err) {
            console.error("Error al cargar productos:", err);
        }

    }

    function refrescar(){

        const tbody = $('#tablaProductos tbody');

        tbody.html(`
            <tr>
                <td colspan="4" class="text-center">
                    <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">Cargando...</span>
                    </div>
                    <div>Cargando productos...</div>
                </td>
            </tr>
        `);

        $('#tablaProductos').DataTable().clear();

        setTimeout(() => {

            cargarVistaVenta();

        }, 2000);
            
    }

    $(document).on('click', '#btn-refrescar', function () {
        refrescar();
    });
    

    function agregar(){

        const codigo = $(this).data('codigo');
        const producto = productos.find(p => p.codigo === codigo);

        const yaExiste = venta.find(p => p.codigo === codigo);
        if (yaExiste) {
            if (yaExiste.cantidad < producto.stock) {
                Swal.fire("Error", "No puedes agregar el mismo producto.", "error");
                //yaExiste.cantidad += 1;
            }
        } else {
            venta.push({ ...producto, cantidad: 1 });
        }

        actualizarTablaVenta();

    }
    
    Component.onClickClass('agregar', agregar);

    function actualizarTablaVenta() { 
        const cuerpo = $('#tablaVenta tbody');
        cuerpo.empty();

        let total = 0;

        venta.forEach((p, index) => {
            const subtotal = p.precio_venta * p.cantidad;
            total += subtotal;

            cuerpo.append(`
                <tr>
                    <td>${p.nombre}</td>
                    <td>
                            <input type="number" min="1" max="${p.stock}" value="${p.cantidad}" class="form-control form-control-sm cantidad-input" data-index="${index}">
                    </td>
                    <td>S/ ${p.precio_venta}</td>
                    <td>S/ ${(subtotal.toFixed(2))}</td>
                    <td><button class="btn btn-sm btn-danger eliminar" data-index="${index}">X</button></td>
                </tr>
            `);
        });

        $('#totalVenta').text(total.toFixed(2));
    }

    $(document).on('input', '.cantidad-input', function () {
        const index = $(this).data('index');
        let nuevaCantidad = parseInt($(this).val());
        if (isNaN(nuevaCantidad) || nuevaCantidad < 1) nuevaCantidad = 1;
        if (nuevaCantidad > venta[index].stock) nuevaCantidad = venta[index].stock;

        venta[index].cantidad = nuevaCantidad;
            
        // 🔄 Actualiza solo el subtotal de esa fila
        const fila = $(this).closest('tr');
        const precio = venta[index].precio_venta;
        const subtotal = (precio * nuevaCantidad).toFixed(2);
        fila.find('td').eq(3).text(`S/ ${subtotal}`);

        // 🔄 Actualiza el total general
        let total = 0;
        venta.forEach(p => total += p.precio_venta * p.cantidad);
        $('#totalVenta').text(total.toFixed(2));
            
    });

    $(document).on('click', '.eliminar', function () {
        const index = $(this).data('index');
        venta.splice(index, 1);
        actualizarTablaVenta();
    });

</script>