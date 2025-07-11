<script>

class TbVenta extends JTable {
    constructor() {
        super();

        this.setClass("table table-hover")
        .setId("tablaVenta")
        .append(new JTHead().setClass("table-light").setHeaders(["Nombre", "Cantidad", "Precio", "Subtotal", ""]))
        .append(new JTBody());

    }
}

class TbProductos extends JTable {
    constructor() {
        super();

        this.setClass("table table-bordered table-striped")
        .setId("tablaProductos").append(new JTHead().setClass("table-dark").setHeaders(["Nombre", "Precio", "Stock", ""]))
        .append(new JTBody());

    }
}

class BtnProcesar extends JButton {
    constructor() {
        super();

        this.setText("Procesar Venta").setId("procesarVenta").setClass("btn btn-success");
    }
}

class BtnRefrescar extends JButton {
    constructor() {
        super();

        this.setText("Refrescar").setId("btn-refrescar").setClass("btn btn-success");
    }
}

class VentaUI extends JPanel {

    constructor() {
        super();

        this.setId("contenido-2").setClass("container");

        this.components();
      
    }

    components(){

        this.btnRefrescar = new BtnRefrescar();
        this.btnProcesar = new BtnProcesar();

        this.tbProductos = new TbProductos();
        this.tbVenta = new TbVenta();

        this.spanTotal = new JSpan().setId("totalVenta").setText("0.00");

    }

    render() {

        this.append(new JH2().setText("Realizar Venta"));
        this.append(new JH4().setText("Buscar Productos"));
        this.append(this.btnRefrescar);
        this.append(this.tbProductos);
        this.append(new JHR());
        this.append(new JH4().setText("Productos en la Venta"));
        this.append(this.tbVenta);
        this.append(new JP().setClass("venta-total").setHTML(`Total: S/ ${this.spanTotal.toHTML()}`));
        this.append(this.btnProcesar);

        return this.toHTML();
    }

}

/*
const ventaUI = new VentaUI();
document.getElementById("app").innerHTML = ventaUI.render();
ventaUI.initEvents(); // configurar eventos, como botones

*/

</script>