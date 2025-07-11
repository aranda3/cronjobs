<script>    

  (function () {     
    
    const util = {   
      rutas: {
        GUARDAR: "<?= BASE_URL . '/sincronizar_bd_suscripcion' ?>",
        PUBLIC_KEY: "<?= PUBLIC_KEY; ?>",
        plan: "<?= $price_id ?>",
        crear_suscripcion: "<?= BASE_URL . '/crear_suscripcion'; ?>"
      },
      sesiones: {
        propietario_id: <?= $propietario_id ?>,
        //propietario_id = ?//= $_SESSION['propietario_id'] ?;
        stripe_customer_id: "<?= $stripe_customer_id ?>",
        nivel: "<?= $nivel ?>"
        //stripe_customer_id = ?//= $_SESSION['stripe_customer_id'] ?;
      },
      elementos:{
        formulario: document.getElementById("formulario-pago"),
        card_element: document.getElementById("card-element"),
        mensaje: document.getElementById("mensaje"),
        btnSuscripcion: document.getElementById("btn-suscripcion")
      }
    };

    if (!validarObjeto(util.elementos, "Elementos del DOM")) return;
    if (!validarObjeto(util.sesiones, "Sesiones")) return;
    if (!validarObjeto(util.rutas, "Rutas")) return;

    //debugObjeto("Elementos HTML", util.elementos); 
    //debugObjeto("Sesiones", util.sesiones);
    //debugObjeto("Rutas", util.rutas);
    //verificar que Stripe se haya cargado

    // ✅ Cargar Stripe de forma segura
    const stripeScript = document.createElement("script");
    stripeScript.src = "https://js.stripe.com/v3/";
    stripeScript.onload = function () {
      if (typeof Stripe !== "function") {
        modalError_1();
        return;
      }

      // ✅ Ya puedes ejecutar tu lógica con Stripe aquí
      iniciarProcesoDePago(util);

      //medirRendimiento();

    };

    stripeScript.onerror = function () {
      modalError_1(); // Fallback si no se puede cargar Stripe
    };

    document.head.appendChild(stripeScript);

  })();

  function iniciarProcesoDePago(util){

    let datosStripe = null;


    const { formulario, mensaje, card_element, btnSuscripcion } = util.elementos; 
    const { propietario_id, stripe_customer_id, nivel } = util.sesiones;
    const { GUARDAR, PUBLIC_KEY, plan, crear_suscripcion } = util.rutas;

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

      const data = await enviarSuscripcion(paymentMethod.id);

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
      
      debugInfo("✅ Suscripción creada en el stripe: " + data.subscription_id);

      datosStripe = {
        stripe_subscription_id: data.subscription_id,
        stripe_price_id: plan,
        propietario_id : propietario_id,
        nivel: nivel
      };
      
      if (!validarObjeto(datosStripe, "Objetos")){
        Swal.close();
        modalError_3(datosStripe.stripe_subscription_id);
        return;
      }

      debugInfo("enviando datos a la base..."); 

      try {
        const addRes = await fetch(GUARDAR, { 
          method: "POST",
          headers: { "Content-Type": "application/json" },
          body: JSON.stringify(datosStripe)
        }); 

        const addData = await addRes.json();

        Swal.close();

        if(!addData.success){
          debugError("No se guardó la suscripcion en la base: " + addData.error);
          errorMessage(datosStripe.stripe_subscription_id);
        }

        if (addData.success) {
          Swal.fire("✅ Éxito", "Suscripción creada.", "success");
          debugInfo("Datos guardados exitosamente"); 
          successMessage(datosStripe.stripe_subscription_id);
        }
        
      } catch (err) {
        Swal.close();
        debugError("Error de conexión con el servidor: " + err);
        errorMessage(datosStripe.stripe_subscription_id);
      }

    });

    async function reintentarGuardarEnBD() {

      mensaje.innerHTML="";

      try {
        const res = await fetch(GUARDAR, { 
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
          Swal.fire("✅ Éxito", "Suscripcion creada. Redirigiendo...", "success");
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
      <a href="/panel_tienda" class="btn btn-success">Ir a mi tienda</a>
      `;
    }
  
    async function enviarSuscripcion(payment_method_id) {

      try {
        const res = await fetch(crear_suscripcion, {
          method: "POST",
          headers: { "Content-Type": "application/json" },
          body: JSON.stringify({
            stripe_price_id: plan,
            payment_method_id: payment_method_id
          })
        });

        const data = await res.json();

        return data;

      } catch (err) {
        return { error: "Error de conexión con el servidor, " + err };
      }

    }

  }

  /*const elements = stripe.elements({
    mode: 'payment',
    amount: 299900,
    currency: 'mxn',
    paymentMethodOrder: ['card']
  });
  const paymentElement = elements.create("payment");
  paymentElement.mount("#card-element");*/

</script>