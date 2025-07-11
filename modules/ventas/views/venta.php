<?php

$pdo = getPDO();
$stmt = $pdo->prepare("SELECT * FROM tiendas WHERE slug = ?");
$stmt->execute([$slug]);
$tienda = $stmt->fetch();
        
$tienda_id = $tienda['id'];

?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Simulador de Ventas</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
  
    <link href="<?= $bootstrap . '5.3.0/bootstrap.min.css' ?>" rel="stylesheet">
    <link href="<?= $datatables . '1.13.6/dataTables.bootstrap5.min.css' ?>" rel="stylesheet">
    <script src="<?= $assets . '/sweetalert2.js' ?>"></script>
 
    <?php include 'frontend/tiendas/css/sidebar.php' ?>

    <?php include 'modules/ventas/assets/css/style.php' ?>

</head>
<body>

    <script src="<?= $jquery . '3.6.0/jquery.min.js' ?>"></script>

    <script src="<?= $datatables . '1.13.6/jquery.dataTables.min.js' ?>"></script>
    <script src="<?= $datatables . '1.13.6/dataTables.bootstrap5.min.js' ?>"></script>

    <script src="<?= $bootstrap . '5.3.0/bootstrap.bundle.min.js' ?>"></script>
  
    <?php include 'modules/JDom/autoload.php'; ?>
    
    <?php include 'modules/ventas/assets/js/script.php' ?>

    <?php include 'frontend/tiendas/js/token-logout.php' ?>
    <?php include 'frontend/tiendas/js/main.php' ?>
        
    <script>

        const fn = cargarVistaVenta;
        app(fn);

    </script>

</body> 
</html>
