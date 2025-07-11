<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Leer CSV en el navegador</title>
</head>
<body>
  <h2>Sube un archivo CSV</h2>
  <input type="file" id="csvInput" accept=".csv">
  <br><br>
  <table border="1" id="tablaCSV"></table>

  <script>
    document.getElementById('csvInput').addEventListener('change', function (e) {
      const archivo = e.target.files[0];
      if (!archivo) return;

      const lector = new FileReader();
      lector.onload = function (evento) {
        const texto = evento.target.result;
        mostrarTabla(texto);
      };
      lector.readAsText(archivo);
    });

    function mostrarTabla(csv) {
      const filas = csv.trim().split("\n");
      const tabla = document.getElementById("tablaCSV");
      tabla.innerHTML = ""; // Limpia tabla previa

      filas.forEach((fila, index) => {
        const columnas = fila.split(",");
        const tr = document.createElement("tr");

        columnas.forEach(col => {
          const celda = document.createElement(index === 0 ? "th" : "td");
          celda.textContent = col.trim();
          tr.appendChild(celda);
        });

        tabla.appendChild(tr);
      });
    }
  </script>
</body>
</html>
