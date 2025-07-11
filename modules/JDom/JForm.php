<script>

class JForm extends Component {
    constructor() {
        super('form');
    }

    setAction(action) {
        this.element.action = action;
        return this;
    }

    setMethod(method) {
        this.element.method = method;
        return this;
    }

    setAutocomplete(value = "on") {
        this.element.autocomplete = value;
        return this;
    }

    onSubmit(callback) {
        return this.on('submit', e => {
            e.preventDefault(); // prevenir comportamiento por defecto
            callback(e);
        });
    }


}

</script>