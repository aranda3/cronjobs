<script>

    document.getElementById("form-login").addEventListener("submit", async (e) => {
        e.preventDefault();

        console.log("clickeado!");

        const slug = document.getElementById("slug").value.trim();
        const email = document.getElementById("email").value;
        const password = document.getElementById("password").value;

        const res = await fetch(`<?= BASE_URL ?>/${slug}/ctrl/login`, {
            method: "POST",
            headers: { "Content-Type": "application/json" },
            body: JSON.stringify({ email, password, slug  })
        });

        const data = await res.json();

        //abarrotes-palermo-da4b92
        
        if (data.token) {
            localStorage.setItem("token_tienda", data.token);
            localStorage.setItem("slug", slug);
            localStorage.setItem("rol_tienda", slug);
            window.location.href = `<?= BASE_URL ?>/${slug}`; 
        } else {
            document.getElementById("mensaje").textContent = data.error || "Error de autenticación";
        }
    });

</script>
