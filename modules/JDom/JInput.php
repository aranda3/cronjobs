<script>

/*class JInput extends Component {
    constructor() {
        super('input');
    }

    setType(type) {
        this.element.type = type;
        return this;
    }

    setValue(value) {
        this.element.value = value;
        return this;
    }

    setPlaceholder(placeholder) {
        this.element.placeholder = placeholder;
        return this;
    }

    setMaxLength(length) {
        this.element.maxLength = length;
        return this;
    }

    setRequired(isRequired = true) {
        this.element.required = isRequired;
        return this;
    }

    onInput(callback) {
        return this.on('input', callback);
    }

    onChange(callback) {
        return this.on('change', callback);
    }
}*/

class JInput extends Component {
    constructor(type = 'text') {
        super('input');
        this.element.type = type;
    }

    setValue(value) {
        this.element.value = value;
        return this;
    }

    getValue() {
        return this.element.value;
    }
}

class JTextField extends JInput {
    constructor() {
        super('text');
    }
}

class JPasswordField extends JInput {
    constructor() {
        super('password');
    }
}

class JDatePicker extends JInput {
    constructor() {
        super('date');
    }
}

class JColorPicker extends JInput {
    constructor() {
        super('color');
    }
}


</script>
