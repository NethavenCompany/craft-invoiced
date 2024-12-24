class InvoicePreview {
    /**
     * Initializes the InvoicePreview class.
     * This constructor sets up the fields, style tag, and registers field listeners.
     */
    constructor() {
        this.fields = {
            invoiceNumber: document.getElementById('invoiceNumber'),
            templateId: document.getElementById('templateId'),
            invoiceDate: document.getElementById('invoiceDate-date'),
            expirationDate: document.getElementById('expirationDate-date'),
            phone: document.getElementById('phone'),
            email: document.getElementById('email'),
            vat: document.getElementById('vat'),
            items: document.getElementById('invoice-items'),
        }
        
        this.styleTag = document.createElement('style');

        document.head.appendChild(this.styleTag);

        this.init();
    }

    /**
     * Initializes the InvoicePreview instance.
     * Registers field listeners and updates the preview.
     */
    init() {
        this._registerFieldListeners();

        this.update();
    }

    // Helper Methods
    // ========================================
    /**
     * Registers a listener for a given field based on its type.
     * @param {Element} field - The field element to register a listener for.
     * @param {Function} callback - The callback function to execute when the field changes.
     */
    fieldListenerHelper(field, callback) {
        if(field.type === "number") field.addEventListener("change", callback);
        if(field.type === "text") {
            field.addEventListener("input", callback);
            field.addEventListener("blur", callback);
        }
        if(field.tagName === "TEXTAREA") field.addEventListener("input", callback);
        if(field.tagName === "SELECT") field.addEventListener("change", callback);
    }

    /**
     * Combines the values of all textarea fields within the items element into an array.
     * Converts the first two columns in every row to numbers.
     * @returns {Array} - An array of combined textarea values with first two columns converted to numbers.
     */
    editableTableToArray() {
        const items = this.fields.items;
        const combinedValues = [];

        items.querySelectorAll('textarea').forEach((textarea, index) => {
            const row = textarea.getAttribute('name').split('[')[1].split(']')[0];
            const col = textarea.getAttribute('name').split('[')[2].split(']')[0];
            if (!combinedValues[row]) combinedValues[row] = [];
            if (col === '0' || col === '1') {
                combinedValues[row][col] = textarea.value === '' ? 0 : parseFloat(textarea.value);
            } else {
                combinedValues[row][col] = textarea.value;
            }
        });

        return combinedValues;
    }

    /**
     * Cleans the CSS by replacing 'body' and 'html' with '&' and adding a wrapper for invoice-preview.
     * @param {string} css - The CSS to be cleaned.
     * @returns {string} - The cleaned CSS.
     */
    cleanCss(css) {
        css = css.replace("body", "&")
        css = css.replace("html", "&")
        css = `#invoice-preview { ${css} }`

        return css;
    }

    /**
     * Generates the URL for the preview controller based on the current field values.
     * @returns {string} - The URL for the preview controller.
     */
    getPreviewControllerUrl() {
        const templateId = this.fields.templateId.value,
            invoiceNumber = this.fields.invoiceNumber.value,
            invoiceDate = this.fields.invoiceDate.value,
            expirationDate = this.fields.expirationDate.value,
            phone = this.fields.phone.value,
            email = this.fields.email.value,
            items = JSON.stringify(this.editableTableToArray()),
            vat = this.fields.vat.value

        return `
            /admin/invoiced/invoices/preview?templateId=${templateId}&vat=${vat}&invoiceNumber=${invoiceNumber}&phone=${phone}&email=${email}&invoiceDate=${invoiceDate}&expirationDate=${expirationDate}&items=${items}
        `.trim();
    }


    // Public Methods
    // ========================================

    /**
     * Updates the invoice preview based on the current field values.
     */
    update() {
        const element = document.querySelector("#invoice-preview");

        fetch(this.getPreviewControllerUrl(), {
            method: 'GET',
            headers: {
                'Accept': 'application/json',
            }
        })
        .then(response => response.json())
        .then(data => {
            element.innerHTML = data.html;
            this.styleTag.innerHTML = this.cleanCss(data.css);
        })
        .catch(error => {
            console.error('Error fetching invoice preview:', error);
        });
    }


    // Setup Methods
    // ========================================

    /**
     * Registers listeners for all fields and their child elements.
     */
    _registerFieldListeners() {
        Object.values(this.fields).forEach(field => {
            this.fieldListenerHelper(field, () => this.update())

            if(field.tagName === "TABLE") field.addEventListener('click', (tableEvent) => {
                this.fieldListenerHelper(tableEvent.target, () => this.update())
            })
        })
    }
}

new InvoicePreview();