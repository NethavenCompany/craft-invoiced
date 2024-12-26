class InvoiceValidate {
    constructor() {
        this.fields = {
            invoiceNumber: document.getElementById('invoiceNumber'),
        }

        this.init();
    }

    init() {
        this._setErrorContainers();
        this._registerFieldListeners();
    }

    // Methods
    // =========================================================================

    async validate() {
        return fetch(`/admin/invoiced/invoices/validate?invoiceNumber=${this.fields.invoiceNumber.value}`, {
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
        if(!target) return;
        if(target !== this.fields.invoiceNumber) return;

        const invoiceNumberTaken = await this.validate();
        const errorContainer = document.querySelector(`#${target.id} + .invoiced-form-error`);
        const submitButton = document.querySelector(`form input[type='submit']`);

        if(!invoiceNumberTaken || target.value === target.dataset.value) {
            target.classList.remove("-error");
            errorContainer.innerHTML = ""
            submitButton.disabled = false;
        } else {
            target.classList.add("-error");
            errorContainer.innerHTML = "Invoice number already taken"
            submitButton.disabled = true;
        }
    }

    // Setup Methods
    // =========================================================================

    _setErrorContainers() {
        Object.values(this.fields).forEach(field => {
            const container = document.createElement("p");
            container.className = "invoiced-form-error"

            field.parentElement.appendChild(container);
        })
    }

    _registerFieldListeners() { 
        Object.values(this.fields).forEach(field => {
            field.addEventListener('change', (e) => this.errorStatus(e.target));
            field.addEventListener('input', (e) => this.errorStatus(e.target));
        })
    }
}

new InvoiceValidate();