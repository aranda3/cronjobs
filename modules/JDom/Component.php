<script>

class Component {
    constructor(tagName) {
        this.element = document.createElement(tagName);
    }

    setText(text) {
        this.element.textContent = text;
        return this;
    }

    setId(id) {
        this.element.id = id;
        return this;
    }

    setClass(classString) {
        this.element.className = classString;
        return this;
    }

    setStyle(property, value) {
        this.element.style[property] = value;
        return this;
    }

    on(event, callback) {
        this.element.addEventListener(event, callback);
        return this;
    }

    appendTo(parent) {
        parent.appendChild(this.element);
        return this;
    }

    build() {
        return this.element;
    }

    append(childComponent) {
        this.element.appendChild(childComponent.build());
        return this;
    }

    setHTML(html) {
        this.element.innerHTML = html;
        return this;
    }

    toHTML() { 
        return this.element.outerHTML; 
    }

    static getById(id) {
        return document.getElementById(id);
    }

    static onClickClass(className, fn) {
        document.addEventListener('click', event => {
            const target = event.target.closest('.' + className);
            if (target) {
                fn.call(target, event);
            }
        });
    }
   
    
}

</script>