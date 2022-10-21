document.addEventListener('DOMContentLoaded', () => {
    const burger = document.getElementById('burger');
    const navbar = document.getElementById('navbar');
    const body = document.querySelector('body');

    burger.addEventListener('click', toggleMenu);

    function toggleMenu() {
        const burgerMenu = document.getElementById('burger-menu');
        burgerMenu.classList.toggle('menu-on');

        if (burgerMenu.classList.contains('menu-on')) {
            navbar.classList.add('overlay');
            body.classList.add('overlay');
            burger.classList.add('overlay');
        } else {
            navbar.classList.remove('overlay');
            body.classList.remove('overlay');
            burger.classList.remove('overlay');
        }
    }
});