const dragon = document.querySelector('#badgeDragon');

if (dragon)
    dragon.addEventListener('click', function (e) {
        e.preventDefault();

        document.badgeDragon.submit()
    });
