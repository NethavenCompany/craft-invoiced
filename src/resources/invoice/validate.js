class InvoiceValidate {
    constructor() {
        this.fields = [
            {
                key: "invoiceNumber",
                id: "invoiceNumber",
                element: document.getElementById('invoiceNumber'),
                errorContainer: null,
                insert: { parent: false, position: "afterend" },
            },
            {
                key: "invoiceDate",
                id: "invoiceDate-date",
                element: document.getElementById('invoiceDate-date'),
                errorContainer: null,
                insert: { parent: true, position: "afterend" },
            },
            {
                key: "expirationDate",
                id: "expirationDate-date",
                element: document.getElementById('expirationDate-date'),
                errorContainer: null,
                insert: { parent: true, position: "afterend" },
            },
        ];

        this.init();
    }

    init() {
        this._setErrorContainers();
        this._registerFieldListeners();
    }

    // Methods
    // =========================================================================

    getValidationUrl() {
        let url = "/admin/invoiced/invoices/validate?";

        this.fields.forEach((field, i) => {
            url += `${field.key}=${field.element.value}`;
            if(i < this.fields.length - 1) url += "&";
        })

        return url;
    }

    getField(target) {
        return this.fields.filter(field => field.element === target)[0];
    }

    async validate() {
        return fetch(this.getValidationUrl(), {
            method: 'GET',
            headers: { 'Accept': 'application/json' }
        })
        .then(response => response.json())
        .then(data => { return data; })
        .catch(error => {
            console.error('Error fetching invoice preview:', error);
        });
    }

    async errorStatus(target) {
        const field = this.getField(target);

        if(!field) return;
        if(target.value === target.dataset.value) return;
        
        const errorContainer = this.getField(target).errorContainer;
        const submitButton = document.querySelector(`form input[type='submit']`);
        const validation = await this.validate();
        
        if(target.classList.contains("-error")) {            
            target.classList.remove("-error");
            errorContainer.classList.add("-hidden");
            errorContainer.innerHTML = ""
            submitButton.disabled = false;
        }
        
        if(validation.success) return;
        
        validation.errors.forEach(error => {
            if(target.id !== error.id) return;
            if(target.value === target.dataset.value) return;

            target.classList.add("-error");
            errorContainer.classList.remove("-hidden")
            errorContainer.innerHTML = error.message;
            submitButton.disabled = true;
        })
    }

    // Setup Methods
    // =========================================================================

    _setErrorContainers() {
        this.fields.forEach(field => {
            const container = document.createElement("p");
            container.className = "invoiced-form-error -hidden"
            
            if(field.insert.parent) {
                field.element.parentElement.parentElement.insertAdjacentElement(field.insert.position, container);
            } else {
                field.element.insertAdjacentElement(field.insert.position, container);
            }

            field.errorContainer = container;
        })
    }

    _registerFieldListeners() { 
        this.fields.forEach(field => {
            field.element.addEventListener('change', (e) => this.errorStatus(e.target));
            field.element.addEventListener('input', (e) => this.errorStatus(e.target));
        })
    }
}

new InvoiceValidate();