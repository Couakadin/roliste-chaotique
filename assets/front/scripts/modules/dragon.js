const dragon = document.querySelector('#badgeDragon');

if (dragon)
dragon.addEventListener('click', function (e) {
    e.preventDefault();

    const xhttp = new XMLHttpRequest();
    xhttp.open('POST', '/badge/unlock/dragon', false);
    xhttp.send();
    location.reload();
});
