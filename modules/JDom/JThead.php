<script>

class JTHead extends Component {
    constructor() {
        super('thead');
    }

    setHeaders(headers) {
        const tr = document.createElement('tr');
        headers.forEach(text => {
            const th = document.createElement('th');
            th.textContent = text;
            tr.appendChild(th);
        });
        this.element.innerHTML = '';
        this.element.appendChild(tr);
        return this;
    }
}

</script>