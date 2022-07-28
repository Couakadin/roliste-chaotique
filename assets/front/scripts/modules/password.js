const iconPassword = document.getElementsByClassName('i-password');

Array.from(iconPassword).forEach((item) => {
    item.addEventListener('click', (e) => {
        e.preventDefault();

        const iconPasswordImg = item.querySelector('img')
        const parentContainer = item.closest('.form-container');
        const inputPasswordChild = parentContainer.querySelector('input');

        if (inputPasswordChild.getAttribute('type') === 'password') {
            inputPasswordChild.focus();
            inputPasswordChild.setAttribute('type', 'text');
            iconPasswordImg.setAttribute('src', '/build/front/tools/password_eye_close.svg');
        } else {
            inputPasswordChild.focus()
            inputPasswordChild.setAttribute('type', 'password');
            iconPasswordImg.setAttribute('src', '/build/front/tools/password_eye_open.svg');
        }
    });
});