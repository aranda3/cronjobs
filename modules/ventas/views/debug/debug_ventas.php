<?php


// Eliminar una venta
if (isset($_GET['delete_venta'])) {
    $id = intval($_GET['delete_venta']);
    $pdo->query("DELETE FROM ventas WHERE id = $id");
    header("Location: " . BASE_URL . "/debug/ventas");
    exit;
}

// Eliminar un detalle
if (isset($_GET['delete_detalle'])) {
    $id = intval($_GET['delete_detalle']);
    $pdo->query("DELETE FROM detalle_ventas WHERE id = $id");
     header("Location: " . BASE_URL . "/debug/ventas");
    exit;
}

// Consultar datos
$ventas = $pdo->query("SELECT * FROM ventas ORDER BY id DESC")->fetchAll(PDO::FETCH_ASSOC);
$detalles = $pdo->query("SELECT * FROM detalle_ventas ORDER BY id DESC")->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Debug Ventas y Detalles</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="container py-4">

    <h2>🧪 Tabla: Ventas</h2>

    <table class="table table-bordered table-striped">
        <thead class="table-dark">
            <tr>
                <th>ID</th>
                <th>Tienda</th>
                <th>Usuario</th>
                <th>Total</th>
                <th>Fecha</th>
                <th>Acción</th>
            </tr>
        </thead>
        <tbody>
        <?php foreach ($ventas as $venta): ?>
            <tr>
                <td><?= $venta['id'] ?></td>
                <td><?= $venta['tienda_id'] ?></td>
                <td><?= $venta['usuario_id'] ?></td>
                <td>S/ <?= $venta['total'] ?></td>
                <td><?= $venta['fecha_registro'] ?></td>
                <td>
                    <a href="?delete_venta=<?= $venta['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('¿Eliminar esta venta?')">Eliminar</a>
                </td>
            </tr>
        <?php endforeach; ?>
        <?php if (empty($ventas)): ?>
            <tr><td colspan="6" class="text-center text-muted">Sin registros</td></tr>
        <?php endif; ?>
        </tbody>
    </table>


    <hr class="my-5">

    <h2>🧪 Tabla: Detalle de Ventas</h2>

    <table class="table table-bordered table-striped">
        <thead class="table-secondary">
            <tr>
                <th>ID</th>
                <th>Venta</th>
                <th>Producto</th>
                <th>Precio</th>
                <th>Cantidad</th>
                <th>Subtotal</th>
                <th>Acción</th>
            </tr>
        </thead>
        <tbody>
        <?php foreach ($detalles as $detalle): ?>
            <tr>
                <td><?= $detalle['id'] ?></td>
                <td><?= $detalle['venta_id'] ?></td>
                <td><?= $detalle['producto_id'] ?></td>
                <td>S/ <?= $detalle['precio_venta'] ?></td>
                <td><?= $detalle['cantidad'] ?></td>
                <td>S/ <?= $detalle['subtotal'] ?></td>
                <td>
                    <a href="?delete_detalle=<?= $detalle['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('¿Eliminar este detalle?')">Eliminar</a>
                </td>
            </tr>
        <?php endforeach; ?>
        <?php if (empty($detalles)): ?>
            <tr><td colspan="7" class="text-center text-muted">Sin registros</td></tr>
        <?php endif; ?>
        </tbody>
    </table>

</body>
</html>
