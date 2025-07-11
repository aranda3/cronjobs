<script>

  function createContenido3(){

    const div = document.createElement("div");
    div.style.justifyContent = "center";
    div.style.alignItems = "center";
    div.style.height = "100vh";
    div.style.flexDirection = "column";
    div.style.fontFamily = "sans-serif";
    div.style.display = "flex"; 

    const h2 = document.createElement("h2");
    h2.textContent = "Acceso denegado";
    h2.style.color = "#444";

    const p = document.createElement("p");
    p.textContent = "No tienes permiso para ver esta página.";
    p.style.color = "#888";

    div.appendChild(h2);
    div.appendChild(p);

    return div;

  }

  function createContenido1(text){

    const div = document.createElement("div");
    div.style.justifyContent = "center";
    div.style.alignItems = "center";
    div.style.height = "100vh";
    div.style.flexDirection = "column";
    div.style.fontFamily = "sans-serif";
    div.style.display = "flex"; 

    const h2 = document.createElement("h2");
    h2.textContent = text;
    h2.style.color = "#444";

    const p = document.createElement("p");
    p.textContent = "Verifica el enlace o vuelve al inicio.";
    p.style.color = "#888";

    div.appendChild(h2);
    div.appendChild(p);

    return div;

  }

  function fnSidebar(){

    const sidebar = document.getElementById("sidebar");
    const sidebarToggleBtn = document.getElementById("sidebarToggleBtn");
    const arrow = document.getElementById("sidebarArrow");
    
    sidebarToggleBtn.addEventListener("click", function () {

      sidebar.classList.toggle("show");

      if (sidebar.classList.contains("show")) {
        arrow.innerText = "«";
      } else {
        arrow.innerText = "»";
      }

    });

    setTimeout(() =>{
      sidebar.classList.add("show");
    }, 10);

  }
    
  function liDefault(nombre, link){

    const li = document.createElement("li");
    li.className = "nav-item";

    const a = document.createElement("a");
    a.className = "nav-link";
    a.href = link;
    a.textContent = nombre;

    li.appendChild(a);

    return li;
  }

  function fnLiDashboard(){
        
    const li = document.createElement("li");
    li.className = "nav-item";
                
    const a = document.createElement("a");
    a.className = "nav-link";
    a.href = "<?= BASE_URL . '/' . $slug ?>";
    a.textContent = "Dashboard";
    /*a.style.color = "white";
    a.padding = "10px 20px";*/

    li.appendChild(a);

    return li;
  }

  function fnLiCerrarSesion(){

    const li = document.createElement("li");
    li.className = "nav-item";
    li.style.cursor = "pointer";

    const a = document.createElement("a");
    a.className = "nav-link";
    a.textContent = "Cerrar sesión";
    a.setAttribute("onclick", "logout()");

    li.appendChild(a);
        
    return li;
  }

  function createSidebarToggleBtn(){
    
    const div = document.createElement("div");
    div.id = "sidebarToggleBtn";
    
    const span = document.createElement("span");
    span.id = "sidebarArrow";
    span.textContent = "«";

    div.appendChild(span);

    return div;

  }
 
  function createH5(){

    const h5 = document.createElement("h5");
    h5.className = "text-center mb-4";
    h5.textContent = "Menú";

    return h5;

  }

  function main(rol, slug, fn){

    const contenido3 = createContenido3();
    const contenido2 = createContenido2();
    const h5 = createH5();
    const divSidebarToggleBtn = createSidebarToggleBtn();
    
    const liDashboard = fnLiDashboard();
    const liCerrarSesion = fnLiCerrarSesion();
    const liConfiguracion = liDefault("Configuración", "/tiendas");
    const liProductos = liDefault("Productos", "<?= BASE_URL . '/' . $slug . '/productos' ?>");
    const liReportes = liDefault("Reportes", "<?= BASE_URL . '/' . $slug . '/reportes'?>");
    const liVenta = liDefault("Venta", "<?= BASE_URL . '/' . $slug . '/venta'?>");

    const div = document.createElement("div");
    div.id = "sidebar";
   
    const ul = document.createElement("h5");
    ul.className = "nav flex-column";

    if (rol === "propietario"){

      if (slugUrl !== slug) {
        const text = `La página ${slugUrl} no existe`;
        const contenido1 = createContenido1(text);
        document.body.appendChild(contenido1);
        return;
      }
      
      if (endUrl === "productos" || endUrl === "venta" || endUrl === "reportes"){
        document.body.appendChild(contenido3);
        return;
      }
      
      document.body.innerHTML = contenido2;

      ul.appendChild(liDashboard);
      ul.appendChild(liConfiguracion);
      ul.appendChild(liCerrarSesion);
      
      div.appendChild(divSidebarToggleBtn);
      div.appendChild(h5);
      div.appendChild(ul);

      document.body.appendChild(div);

      fnSidebar();

      fn();

    }
            
    if (rol === "vendedor"){

      if (slugUrl !== slug) {
        const text = `La página ${slugUrl} no existe`;
        const contenido1 = createContenido1(text);
        document.body.appendChild(contenido1);
        return;
      }

      if (endUrl === "productos" || endUrl === "reportes"){
        document.body.appendChild(contenido3);
        return;
      }

      document.body.innerHTML = contenido2;

      ul.appendChild(liDashboard);
      ul.appendChild(liVenta);
      ul.appendChild(liCerrarSesion);

      div.appendChild(divSidebarToggleBtn);
      div.appendChild(h5);
      div.appendChild(ul);

      document.body.appendChild(div);

      fnSidebar();

      fn();
        
    }

    if (rol === "administrador"){
      
      if (slugUrl !== slug) {
        const text = `La página ${slugUrl} no existe`;
        const contenido1 = createContenido1(text);
        document.body.appendChild(contenido1);
        return;
      }

      //document.body.appendChild(contenido3);

      document.body.innerHTML = contenido2;

      ul.appendChild(liDashboard);
      ul.appendChild(liProductos);
      ul.appendChild(liReportes);
      ul.appendChild(liVenta);
      ul.appendChild(liCerrarSesion);

      div.appendChild(divSidebarToggleBtn);
      div.appendChild(h5);
      div.appendChild(ul);

      document.body.appendChild(div);

      fnSidebar();

      fn();
        
    }

  }

  async function app(fn) {  

    if (!token_tienda) redirigirALogin();

    try {

      const res = await fetch("<?= BASE_URL . '/api' ?>", {
        method: "POST",
        headers: {
          "Content-Type": "application/json",
          "Authorization": "Bearer " + token_tienda
        }
      });

      const data = await res.json();
      console.log(data);

      //const rol = data.rol;
      const rol = "vendedor";
      //const rol = "administrador";

      console.log("rol: ", rol);

      const slug = data.slug;
      console.log("slug: ", slug);
      
      main(rol, slug, fn);

    }catch (err) {
      console.error("Error: ", err);
    }
  }

</script>