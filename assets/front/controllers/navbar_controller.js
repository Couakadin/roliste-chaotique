import { Controller } from '@hotwired/stimulus';

export default class extends Controller {
    connect() {
        const navbar = document.getElementById('navbar');
        const burger = document.getElementById('burger');
        const body = document.querySelector('body');
        const burgerMenu = document.getElementById('burger-menu');

        document.addEventListener('turbo:before-cache', () => {
            if (burgerMenu.classList.contains('menu-on')) {
                burgerMenu.classList.remove('menu-on')
                navbar.classList.remove('overlay');
                body.classList.remove('overlay');
                burger.classList.remove('overlay');
            }
        });

        this.element.addEventListener('click', function () {
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
        });
    }
}