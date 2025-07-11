<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Clientes y Planes Stripe</title>
  <style>
    table { border-collapse: collapse; width: 100%; margin-top: 20px; }
    th, td { padding: 8px; border: 1px solid #ccc; }
  </style>
</head>
<body>

<h2>Clientes con suscripciones activas</h2>

<table id="tabla-clientes">
  <thead>
    <tr>
      <th>Email</th>
      <th>Plan</th>
      <th>Estado</th>
      <th>Renovación</th>
    </tr>
  </thead>
  <tbody>
    <tr><td colspan="4">Cargando...</td></tr>
  </tbody>
</table>

<script>
fetch("stripe_planes_clientes.php")
  .then(res => res.json())
  .then(data => {
    const tbody = document.querySelector("#tabla-clientes tbody");
    tbody.innerHTML = "";

    if (data.length === 0) {
      tbody.innerHTML = "<tr><td colspan='4'>No hay clientes activos.</td></tr>";
      return;
    }

    data.forEach(row => {
      const tr = document.createElement("tr");
      tr.innerHTML = `
        <td>${row.email}</td>
        <td>${row.plan}</td>
        <td>${row.estado}</td>
        <td>${row.renovacion}</td>
      `;
      tbody.appendChild(tr);
    });
  })
  .catch(err => {
    document.querySelector("#tabla-clientes tbody").innerHTML = "<tr><td colspan='4'>Error cargando datos</td></tr>";
    console.error(err);
  });
</script>

</body>
</html>
