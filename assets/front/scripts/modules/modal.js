class Modal {
    constructor(open, close) {
        this.open = open;
        this.close = close;
    }

    bind() {
        const buttons = document.querySelectorAll(`[${this.open}]`);
        const closes = document.querySelectorAll(`[${this.close}]`);

        buttons.forEach((button) => {
            button.addEventListener('click', (event) => {
                const modal = document.querySelector(`#${event.target.getAttribute(`${this.open}`)}`);

                document.body.style.overflow = 'hidden';
                modal.style.display = 'block';
            })
        });

        closes.forEach((close) => {
            close.addEventListener('click', (event) => {
                const modal = document.querySelector(`#${event.target.getAttribute(`${this.close}`)}`);

                document.body.style.overflow = 'initial';
                modal.style.display = 'none';
            })
        });
    }
}

new Modal('data-modal-open', 'data-modal-close').bind();