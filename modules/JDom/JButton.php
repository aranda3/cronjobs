<script>

class JButton extends Component {
    constructor() {
        super('button');
    }

    setBackground(color) {
        return this.setStyle('backgroundColor', color);
    }

    setColor(color) {
        return this.setStyle('color', color);
    }

    setFont(size, family = 'sans-serif') {
        this.setStyle('fontSize', size);
        this.setStyle('fontFamily', family);
        return this;
    }

    onClick(callback) {
        return this.on('click', callback);
    }

}

</script>