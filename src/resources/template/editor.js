class Editor {
	constructor() {
		this.navButtons = document.querySelectorAll(".invoice-template-nav-button");
		this.containerElement = document.querySelector(".single-field-row");
		this.pageOrder = [];

		this.init();
	}

	init() {
		this._registerNavButtons();
	}
	
	handleButtonClick = (event) => {
		const className = event.target.className;
		
		if (className.includes("selected")) return;
		
		const index = this.pageOrder.indexOf(className);
		const previousSelectedElement = document.querySelector(
			".invoice-template-nav-button.selected"
		);
		
		if (index > -1) {
			const translate = index * 100;
			
			previousSelectedElement.classList.remove("selected");
			event.target.classList.add("selected");
			
			this.containerElement.style.transform = `translateX(-${translate}%)`;
		}
	};
	
	_registerNavButtons() {
		this.navButtons.forEach((button) => {
			this.pageOrder.push(button.classList.toString());
			
			button.addEventListener("click", this.handleButtonClick);
		});

		this.navButtons[0].classList.add("selected");
	}
}
