<?php
// Configura tu conexión PostgreSQL (Render)

$host = 'dpg-d1ob2n49c44c73fcmc40-a'; 
$port = 5432;
$db   = 'mitienda03_postgres';
$user = 'mitienda03_postgres_user';
$pass = 'FAamO0g0MwEtsCtHVXozYKzDtbaMuNP4';

try {
    $pdo = new PDO("pgsql:host=$host;port=$port;dbname=$db", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (Exception $e) {
    die("Error de conexión: " . $e->getMessage());
}

// Obtener todas las tablas públicas
$tablas = [];
$stmt = $pdo->query("SELECT table_name FROM information_schema.tables WHERE table_schema = 'public'");
while ($row = $stmt->fetch()) {
    $tablas[] = $row['table_name'];
}
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Visualizador de Tablas</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <div class="container my-5">
        <h1 class="mb-4">📊 Todas las Tablas (PostgreSQL en Render)</h1>

        <ul class="nav nav-tabs" id="tablasTab" role="tablist">
            <?php foreach ($tablas as $i => $tabla): ?>
                <li class="nav-item" role="presentation">
                    <button class="nav-link <?= $i === 0 ? 'active' : '' ?>" id="tab-<?= $tabla ?>" data-bs-toggle="tab" data-bs-target="#<?= $tabla ?>" type="button" role="tab">
                        <?= htmlspecialchars($tabla) ?>
                    </button>
                </li>
            <?php endforeach; ?>
        </ul>

        <div class="tab-content border border-top-0 p-3 bg-white" id="tablasContent">
            <?php foreach ($tablas as $i => $tabla): ?>
                <div class="tab-pane fade <?= $i === 0 ? 'show active' : '' ?>" id="<?= $tabla ?>" role="tabpanel">
                    <h5 class="mb-3">Tabla: <?= htmlspecialchars($tabla) ?></h5>
                    <?php
                    try {
                        $stmt = $pdo->query("SELECT * FROM \"$tabla\" LIMIT 100");
                        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
                        if ($rows):
                    ?>
                        <div class="table-responsive">
                            <table class="table table-bordered table-hover table-sm">
                                <thead class="table-light">
                                    <tr>
                                        <?php foreach (array_keys($rows[0]) as $col): ?>
                                            <th><?= htmlspecialchars($col) ?></th>
                                        <?php endforeach; ?>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($rows as $fila): ?>
                                        <tr>
                                            <?php foreach ($fila as $valor): ?>
                                                <td><?= htmlspecialchars((string)$valor) ?></td>
                                            <?php endforeach; ?>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php else: ?>
                        <p class="text-muted">Esta tabla no tiene datos.</p>
                    <?php endif;
                    } catch (Exception $e) {
                        echo "<p class='text-danger'>Error al consultar tabla <strong>" . htmlspecialchars($tabla) . "</strong>: " . htmlspecialchars($e->getMessage()) . "</p>";
                    }
                    ?>
                </div>
            <?php endforeach; ?>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
