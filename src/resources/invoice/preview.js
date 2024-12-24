class InvoicePreview {
    constructor() {
        this.fields = document.querySelectorAll("#invoice-editor .field input:not([type='hidden']), #invoice-editor .field select , #invoice-editor .field table");

        this.init();
    }

    init() {
        this._registerFieldListeners();
    }

    // Helper Methods
    // ========================================
    fieldListenerHelper(field, callback) {
        if(field.type === "number") field.addEventListener("change", callback);
        if(field.type === "text") field.addEventListener("input", callback);
        if(field.tagName === "TEXTAREA") field.addEventListener("input", callback);
    }


    // Public Methods
    // ========================================

    update() {
        const element = document.querySelector("#invoice-preview");

        fetch(`/admin/invoiced/invoices/preview?templateId=${1}&vat=${21}`, {
            method: 'GET',
            headers: {
                'Accept': 'application/json',
            }
        })
        .then(response => response.json())
        .then(data => {
            element.innerHTML = data.html;
        })
        .catch(error => {
            console.error('Error fetching invoice preview:', error);
        });
    }


    // Setup Methods
    // ========================================

    _registerFieldListeners() {
        this.fields.forEach(field => {
            this.fieldListenerHelper(field, () => this.update())

            if(field.tagName === "TABLE") field.addEventListener('click', (tableEvent) => {
                this.fieldListenerHelper(tableEvent.target, () => this.update())
            })
        })
    }
}

new InvoicePreview();