document.addEventListener("DOMContentLoaded", function () {
	const navButtons = document.querySelectorAll(".invoice-template-nav-button");
	const editorContainer = document.querySelector(".single-field-row");

	const order = [];

	const handleButtonClick = (event) => {
		const className = event.target.className;

		if (className.includes("selected")) return;

		const index = order.indexOf(className);
		const previousSelectedElement = document.querySelector(
			".invoice-template-nav-button.selected"
		);

		if (index > -1) {
			const translate = index * 100;

			previousSelectedElement.classList.remove("selected");
			event.target.classList.add("selected");

			editorContainer.style.transform = `translateX(-${translate}%)`;
		}
	};

	navButtons.forEach((button) => {
		order.push(button.classList.toString());

		button.addEventListener("click", handleButtonClick);
	});

	navButtons[0].classList.add("selected");
});
