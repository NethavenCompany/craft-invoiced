class InvoicePreview {
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
        this.previewContainer = document.querySelector("#invoice-preview");

        document.head.appendChild(this.styleTag);

        this.init();
    }

    init() {
        this._registerResizeListener();
        this._registerFieldListeners();
        this.update();
    }

    // Helper Methods
    // =========================================================================
    
    /**
     * Registers a listener for a given field based on its type.
     * @param {Element} field - The field element to register a listener for.
     * @param {Function} callback - The callback function to execute when the field changes.
     */
    fieldListenerHelper(field, callback) {
        if(field === this.fields.items) {
            field.addEventListener('click', (tableEvent) => {
                if(tableEvent.target.getAttribute("invoiceListener")) return;
                tableEvent.target.addEventListener("input", (e) => callback(e));
                tableEvent.target.setAttribute("invoiceListener", true);
            })

            this.editableTableToArray().forEach((value, index) => {
                const row = index;
                const cols = [
                    document.querySelector(`textarea[name='items[${row}][0]']`),
                    document.querySelector(`textarea[name='items[${row}][1]']`),
                    document.querySelector(`textarea[name='items[${row}][2]']`)
                ]

                cols.forEach((col, i) => {
                    col.dataset.value = value[i];
                    col.addEventListener("input", (e) => callback(e));
                    col.setAttribute("invoiceListener", true);
                });
            })

            return;
        }

        
        if(field.type === "number") {
            field.addEventListener("input", (e) => callback(e));
            field.addEventListener("change", (e) => callback(e));
        }
        if(field.type === "text") {
            field.addEventListener("input", (e) => callback(e));
            field.addEventListener("blur", (e) => callback(e));
        }
        if(field.tagName === "TEXTAREA") field.addEventListener("input", (e) => callback(e));
        if(field.tagName === "SELECT") field.addEventListener("change", (e) => callback(e));
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
        // css = css.replaceAll(/font-size:.*?;?/g, "")
        css = `#invoice-preview { ${css} }`

        console.log(css)

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


    // Methods
    // =========================================================================

    /**
     * Updates the invoice preview based on the current field values.
     */
    async update(e) {
        const view = await this.getView();
        this.updateView(view);
        this.editStatus(e);
    }

    async getView() {
        return fetch(this.getPreviewControllerUrl(), {
            method: 'GET',
            headers: { 'Accept': 'application/json' }
        })
        .then(response => response.json())
        .then(data => { return data; })
        .catch(error => {
            console.error('Error fetching invoice preview:', error);
        });
    }
    
    updateView(view) {
        this.previewContainer.innerHTML = view.html;
        this.styleTag.innerHTML = this.cleanCss(view.css);
    }

    editStatus(event) {
        if(!event) return;

        const target = event.target;
        const value = target.value;
        const valueInDb = target.dataset.value;
        
        if(!value || !valueInDb) return;
        
        if(value !== valueInDb) {
            target.classList.add("-edited")
        } else {
            target.classList.remove("-edited")
        }
    }

    // Setup Methods
    // =========================================================================

    /**
     * Registers update listeners for all fields.
     */
    _registerFieldListeners() {
        Object.values(this.fields).forEach(field => {
            this.fieldListenerHelper(field, (e) => this.update(e))
        })
    }

    _registerResizeListener() {
        const invoicePreview = this.previewContainer;
        
        window.addEventListener('resize', () => {
            let scale = Math.max(Math.min(window.innerWidth / invoicePreview.offsetWidth, window.innerHeight / invoicePreview.offsetHeight));
            scale = Math.min(scale, 0.8);
            invoicePreview.style.transform = `scale(${scale})`;
        });
    }
}
