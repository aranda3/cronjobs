<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Registro</title>
</head>
<body>
  <h2>Crear cuenta</h2>

  <form id="form-registro">
    <input type="email" id="email" placeholder="Correo"  value="morin@gmail.com"><br>
    <input type="password" id="password" placeholder="Contraseña"  value="123456"><br>
    <button type="submit">Registrarme</button>
  </form>

  <p id="mensaje"></p>

  <script>

    document.getElementById("form-registro").addEventListener("submit", async (e) => {
      e.preventDefault();

      const email = document.getElementById("email").value;
      const password = document.getElementById("password").value;

      const res = await fetch("<?= BASE_URL . '/ctrl/registro' ?>", {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({ email, password })
      });

      const data = await res.json();

      console.log(data);

      /*if (data.token) {
        localStorage.setItem("token", data.token);
        window.location.href = "?= BASE_URL . '/panel' ?>";
      } else {
        document.getElementById("mensaje").textContent = data.error || "Error en el registro";
      }*/
    });
  </script>
</body>
</html>
