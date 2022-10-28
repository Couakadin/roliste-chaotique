class Display {
    constructor(open) {
        this.open = open;
    }

    bind() {
        const buttons = document.querySelectorAll(`[${this.open}]`);

        buttons.forEach((button) => {
            button.addEventListener('click', (event) => {
                const display = document.querySelector(`#${event.target.getAttribute(`${this.open}`)}`);

                if ('none' === display.style.display)
                    display.removeAttribute('style');
                else
                    display.style.display = 'none';
            })
        });
    }
}

new Display('data-display').bind();