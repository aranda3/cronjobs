<script>    

  function modalError_1(){
    const stripeErrorModal = document.getElementById('stripeErrorModal');
    const modal = new bootstrap.Modal(stripeErrorModal);
    const recargarPagina = document.getElementById("recargarPagina"); 
    modal.show();
    recargarPagina.addEventListener("click", () => location.reload());
  }

  function modalError_2(){
    const ErrorModal = document.getElementById('errorModal');
    const modal = new bootstrap.Modal(ErrorModal);
    modal.show();
  }

  const DEBUG_LEVEL = {
    info: true,
    warn: false,
    error: true
  };

  function debugInfo(...args)  { if (DEBUG_LEVEL.info)  console.info("[INFO]", ...args); }
  function debugWarn(...args)  { if (DEBUG_LEVEL.warn)  console.warn("[WARN]", ...args); }
  function debugError(...args) { if (DEBUG_LEVEL.error) console.error("[ERROR]", ...args); } 

  function debugObjeto(nombreGrupo, objeto) {
    debugInfo(`🔍 ${nombreGrupo}:`);
    for (const [clave, valor] of Object.entries(objeto)) {
      debugInfo(`  ${clave}:`, valor);
    }
  }

  function validarObjeto(objeto, tipo) {
    const faltantes = [];

    for (const [clave, valor] of Object.entries(objeto)) {
      if (!valor) faltantes.push(clave);
    }

    if (faltantes.length > 0) {
      debugError(`❌ ${tipo} faltantes:\n• ${faltantes.join("\n• ")}`);
      modalError_2();
      return false;
    }

    return true;
  }

  function medirRendimiento(){

    const nav = performance.getEntriesByType("navigation")[0];
    const htmlSize = nav.transferSize || nav.encodedBodySize || 0;

    console.log(`🧱 HTML principal → ${Math.round(htmlSize / 1024)} KB`);

    let total = htmlSize;

    performance.getEntriesByType("resource").forEach(r => {
      const size = r.transferSize > 0 ? r.transferSize : r.decodedBodySize;
      total += size;
      console.log(`${r.name} → ${Math.round(size / 1024)} KB`);
    });

    console.log(`📦 Peso total estimado de la página (HTML + recursos): ${Math.round(total / 1024)} KB`);

  }

</script>  