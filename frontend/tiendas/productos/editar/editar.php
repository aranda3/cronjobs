<?php

$producto = null;

try {

  $pdo = getPDO();

  $stmt = $pdo->prepare("SELECT * FROM tiendas WHERE slug = ?");
  $stmt->execute([$slug]);
  $tienda = $stmt->fetch();
          
  $tienda_id = $tienda['id'];

  $stmt = $pdo->prepare("SELECT * FROM productos WHERE tienda_id = ? AND codigo = ? LIMIT 1");
  $stmt->execute([$tienda_id, $codigo]);

  $producto = $stmt->fetch(PDO::FETCH_ASSOC);

  if (!$producto) {
    echo "<div class='alert alert-danger'>Producto no encontrado.</div>";
    exit;
  }
    
} catch (PDOException $e) {
    echo "<div class='alert alert-danger'>Error de base de datos: {$e->getMessage()}</div>";
    exit;
}

?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Crear Tienda</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>  
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    
  <?php include $ruta . 'js/token.php' ?>

  <?php include $ruta . 'contenido-1.php' ?>
 
  <div id="contenido-2">

    <div class="container py-5">
      <h2 class="mb-4">Editar Producto</h2>

      <a href="<?= BASE_URL . '/' . $slug . '/productos' ?>">volver</a>
      <br><br>

      <form id="form-editar">

        <?php include $ruta . 'productos/campos.php' ?>

        <button type="submit" class="btn btn-primary">Guardar</button>
      </form>
      <div id="mensaje" class="mt-3"></div>
    </div>

  </div>

  <?php include $ruta . 'js/slug.php' ?>

  <?php include $ruta . 'js/logout.php' ?>

  <script>

    let tienda_id = "<?= $tienda_id ?>";
    console.log("tienda_id: ", tienda_id);

    let codigo = "<?= $codigo ?>";
    console.log("codigo: ", codigo);
    
    let codigo_nuevo = document.getElementById("codigo-nuevo");
    let nombre = document.getElementById("nombre");
    let marca = document.getElementById("marca");
    let categoria = document.getElementById("categoria");
    let precio_venta = document.getElementById("precio-venta");
    let precio_compra = document.getElementById("precio-compra");
    let stock = document.getElementById("stock");
    let unidad = document.getElementById("unidad");
    let cantidad_en_paquete = document.getElementById("cantidad-en-paquete");

    codigo_nuevo.value="<?= $producto['codigo'] ?>";
    nombre.value="<?= $producto['nombre'] ?>";
    marca.value="<?= $producto['marca'] ?>";
    categoria.value="<?= $producto['categoria'] ?>";
    precio_venta.value="<?= $producto['precio_venta'] ?>";
    precio_compra.value="<?= $producto['precio_compra'] ?>";
    stock.value="<?= $producto['stock'] ?>";
    unidad.value="<?= $producto['unidad'] ?>";
    cantidad_en_paquete.value="<?= $producto['cantidad_en_paquete'] ?>";

    document.getElementById("form-editar").addEventListener("submit", async (e) => {
      e.preventDefault();

      let producto = 
      {
        codigo: codigo,
        codigo_nuevo: codigo_nuevo.value,
        tienda_id: tienda_id,
        nombre: nombre.value,
        marca: marca.value,
        categoria: categoria.value,
        precio_venta: precio_venta.value,
        precio_compra: precio_compra.value,
        stock: stock.value,
        unidad: unidad.value,
        cantidad_en_paquete: cantidad_en_paquete.value
      }

      //console.log(producto);
      
      try {
        const res = await fetch("<?= BASE_URL . '/api/productos/update' ?>", {
          method: "POST",
          headers: {"Content-Type": "application/json"},
          body: JSON.stringify(producto)
        });

        const data = await res.json();

        console.log(data);

        if(data.error){
          Swal.fire("Error", data.error, "error");
        }else{
          Swal.fire("✅ Éxito", "Producto Actualizado!", "success");
        }
        

      } catch (err) {
        console.error("Error al cargar productos:", err);
      }
      
    });

  </script>


</body>
</html>
