<script>       

  (function () {     
    
    // ✅ Cargar Stripe de forma segura
    const stripeScript = document.createElement("script");
    stripeScript.src = "https://js.stripe.com/v3/";
    stripeScript.onload = async function () {
      if (typeof Stripe !== "function") {
        modalError_1();
        return;
      }

      // ✅ Ya puedes ejecutar tu lógica con Stripe aquí
      await main();

      //medirRendimiento();

    };

    stripeScript.onerror = function () {
      modalError_1(); // Fallback si no se puede cargar Stripe
    };

    document.head.appendChild(stripeScript);

  })();

  async function main(){

    const token = localStorage.getItem("token");
    //console.log(token);
      
    const res = await fetch("<?= BASE_URL . '/api/planes'?>", { 
      headers: {
        "Authorization": "Bearer " + token,
        "Content-Type": "application/json"
      }
    });

    const dataApi = await res.json();

    if (dataApi.error) {
      logout();
      return;
    } 

    iniciarProcesoDePago(dataApi);

  }

  function iniciarProcesoDePago(api){

    const util = {   
      rutas: {
        UPDATE: "<?= BASE_URL . '/sincronizar_bd_upgrade'; ?>",
        PUBLIC_KEY: "<?= PUBLIC_KEY; ?>",
        plan: "<?= $price_id ?>",
        actualizar_suscripcion: "<?= BASE_URL . '/actualizar_suscripcion'; ?>"
      },
      sesiones: {
        propietario_id: api.propietario_id,
        stripe_subscription_id: api.stripe_subscription_id,
        nivel_plan: api.nivelPlanActual
      },
      elementos:{
        formulario: document.getElementById("formulario-pago"),
        card_element: document.getElementById("card-element"),
        mensaje: document.getElementById("mensaje"),
        btnSuscripcion: document.getElementById("btn-suscripcion")
      }
    };

    console.log(util);

    if (!validarObjeto(util.elementos, "Elementos del DOM")) return;
    if (!validarObjeto(util.sesiones, "Sesiones")) return;
    if (!validarObjeto(util.rutas, "Rutas")) return;

    //debugObjeto("Elementos HTML", util.elementos); 
    //debugObjeto("Sesiones", util.sesiones);
    //debugObjeto("Rutas", util.rutas);

    let datosStripe = null;

    const { formulario, mensaje, card_element, btnSuscripcion } = util.elementos; 
    const { propietario_id,  stripe_subscription_id, nivel_plan} = util.sesiones;
    const { UPDATE, PUBLIC_KEY, plan, actualizar_suscripcion } = util.rutas;

    const stripe = Stripe(PUBLIC_KEY); 

    let elements;
    try {
      elements = stripe.elements();
    } catch (err) {
      debugError("Stripe elements() error:", err);
      modalError_1();
      return;
    }
    const card = elements.create("card");
    card.mount(card_element);

    formulario.addEventListener("submit", async (e) => { 
      e.preventDefault();

      btnSuscripcion.disabled = true;

      debugInfo("iniciando proceso..."); 

      //Mostrar loader
      Swal.fire({
        title: 'Procesando pago...',
        allowOutsideClick: false,
        didOpen: () => {
          Swal.showLoading();
        }
      });

      const { error, paymentMethod } = await stripe.createPaymentMethod({ 
        type: "card",
        card: card
      });

      if (error) {
        Swal.fire("Error", error.message, "error");
        debugError(error.message);     
        return;
      }
        
      //tarjeta de prueba
      //4242 4242 4242 4242 

      debugInfo("enviando datos al stripe...");

      const data = await enviarSuscripcion(); 

      if (data.error) { 
        Swal.close();
        debugError(data.error);
        modalError_2();    
        return;
      } 
          
      if (!data.subscription_id) {
        Swal.close();
        debugError("No se encontró la subscription_id: " + data.subscription_id);
        modalError_2();
        return;
      }
      
      debugInfo("✅ Suscripción renovada en el Stripe: " + data.subscription_id);

      datosStripe = {
        new_price_id: plan,
        propietario_id : propietario_id,
        nivel_plan: nivel_plan
      };
      
      if (!validarObjeto(datosStripe, "Objetos")){
        Swal.close();
        modalError_3(data.subscription_id);
        return;
      }

      debugInfo("enviando datos a la base..."); 

      try {
        const updateRes = await fetch(UPDATE, {
          method: "POST",
          headers: { "Content-Type": "application/json" },
          body: JSON.stringify(datosStripe)
        });

        const updateData = await updateRes.json();

        Swal.close();

        if(!updateData.success){
          debugError("No se guardó la suscripcion en la base: " + updateData.error);
          errorMessage(data.subscription_id);
        }

        if (updateData.success) {
          Swal.fire("✅ Éxito", "Suscripción renovada.", "success");
          debugInfo("Datos guardados exitosamente"); 
          successMessage(data.subscription_id);
        }
        
      } catch (err) {
        Swal.close();
        debugError("Error de conexión con el servidor: " + err);
        errorMessage(data.subscription_id);
      }

    });

    async function reintentarGuardarEnBD() {

      mensaje.innerHTML="";

      try {
        const res = await fetch(UPDATE, { 
          method: "POST",
          headers: { "Content-Type": "application/json" },
          body: JSON.stringify(datosStripe)
        }); 

        const data = await res.json();

        if(!data.success){
          Swal.fire("Error", "Error persistente", "error");
          debugError("Reintentar: No se guardó la suscripcion en la base -> " + data.error);
          errorMessage(datosStripe.stripe_subscription_id);
        }

        if (data.success) {
          Swal.fire("✅ Éxito", "Suscripcion actualizada correctamente. Redirigiendo...", "success");
          debugInfo("Reintentar: Datos guardados exitosamente");
          successMessage(datosStripe.stripe_subscription_id);
        } 
        
      } catch (err) {
        Swal.fire("Error", "Error persistente", "error");
        debugError("Reintentar: Error de conexión con el servidor -> " + err);
        errorMessage(datosStripe.stripe_subscription_id);
      }

    }

    function errorMessage(id){
      mensaje.innerHTML = `
      <p>
        ⚠️ Ocurrió un error al procesar tu solicitud, pero el pago fue exitoso.
        Guarda este ID de suscripción y contáctanos para finalizar el proceso:<br>
        <strong>${id}</strong>
      </p>
      <br>
      <button class="btn btn-primary" id="btn-reintentar">Reintentar suscribirme</button>`;

      document.getElementById("btn-reintentar").addEventListener("click", reintentarGuardarEnBD);

    }

    function successMessage(id){
      mensaje.innerHTML =`
      <p>Su suscripción ha sido exitosa. Por seguridad guarde su clave:<br>
        <strong>${id}</strong>
      </p>
      <br>
      <a href="<?= BASE_URL . '/tiendas' ?>" class="btn btn-success">Ir a mis tiendas</a>
      `;
    }
  
    async function enviarSuscripcion() {

      try {
        const res = await fetch(actualizar_suscripcion, { 
          method: "POST",
          headers: { "Content-Type": "application/json" },
          body: JSON.stringify({
            stripe_subscription_id: stripe_subscription_id,
            new_price_id: plan,
            propietario_id: propietario_id
          })
        });

        const data = await res.json();

        return data;

      } catch (err) {
        return { error: "Error de conexión con el servidor." };
      }

    }

  }

</script>