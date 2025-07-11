<script>

    document.getElementById("form-login").addEventListener("submit", async (e) => {
        e.preventDefault();

        console.log("clickeado!");

        const email = document.getElementById("email").value;
        const password = document.getElementById("password").value;

        const res = await fetch("<?= BASE_URL . '/ctrl/login' ?>", {
            method: "POST",
            headers: { "Content-Type": "application/json" },
            body: JSON.stringify({ email, password })
        });

        const data = await res.json();

        if (data.token) {
            localStorage.setItem("token", data.token);
            window.location.href = "<?= BASE_URL . '/tiendas' ?>"; 
        } else {
            document.getElementById("mensaje").textContent = data.error || "Error de autenticación";
        }
    });

</script>
