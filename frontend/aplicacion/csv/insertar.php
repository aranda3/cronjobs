<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Leer CSV en el navegador</title>
</head>
<body>
 
<form action="importar_csv.php" method="POST" enctype="multipart/form-data">
  <label>Subir archivo CSV:</label>
  <input type="file" name="archivo_csv" accept=".csv" required>
  <button type="submit">Importar</button>
</form>



</body>
</html>
