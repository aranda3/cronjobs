<script>

    const token_tienda = localStorage.getItem("token_tienda");

    const slugUrl = window.location.pathname.split('/')[2];
    console.log("slugUrl: ", slugUrl);

    const endUrl = window.location.pathname.split('/')[3];
    console.log("endUrl: ", endUrl);

    function redirigirALogin() {
        window.location.href = "<?= BASE_URL . '/tiendas/login' ?>";
    }

    function logout() {
        localStorage.removeItem("token_tienda");
        window.location.href = "<?= BASE_URL . '/tiendas/login'?>";
    }

</script>