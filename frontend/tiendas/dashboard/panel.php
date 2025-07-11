<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Panel de Administración - Mi Tienda</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

  <!-- Toastr CSS -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">

  <?php include $ruta . 'css/sidebar.php' ?>

</head>
<body class="bg-light">

  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

  <!-- Toastr JS -->
  <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>

  <?php include $ruta . 'dashboard/js/panel.php' ?>
  
  <?php include $ruta . 'js/token-logout.php' ?>
  <?php include $ruta . 'js/main.php' ?>

  <script>

    const fn = cargarPanel;
    app(fn);

  </script>

    <script>

        if ("Notification" in window && Notification.permission !== "granted") {
            Notification.requestPermission();
        }

        const ws = new WebSocket("ws://localhost:8080"); 

        ws.onmessage = (event) => {
            try {
                const data = JSON.parse(event.data);

                if (data.tipo === "venta_nueva") {
                    // Notificación visual con Toastr
                    toastr.success(`Venta registrada por S/ ${data.total}`, "🛒 Nueva venta", {
                        timeOut: 5000,
                        closeButton: true,
                        progressBar: true
                    });

                    console.log("📢 Venta nueva:", data);
                }

            } catch (err) {
                console.error("❌ Error al procesar mensaje:", err);
            }
        };

    </script>
  
</body>
</html>
