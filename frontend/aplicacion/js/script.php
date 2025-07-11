<script>

    function logout() {
        localStorage.removeItem("token");
        window.location.href = "<?= BASE_URL . '/login'?>";
    }

</script>