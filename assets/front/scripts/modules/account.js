const modalAccount = document.querySelector('#modalDeleteAccount');
const actionAccount = document.querySelector('#actionDeleteAccount');

document.addEventListener('click', function (e) {
    if (e.target.matches('#triggerDeleteAccount')) {
        modalAccount.classList.add('show');
        actionAccount.removeAttribute('disabled');
    } else if (e.target.matches('#modalDeleteAccount') || e.target.matches('#crossDeleteAccount')) {
        modalAccount.classList.remove('show');
        actionAccount.setAttribute('disabled', 'disabled');
    }
});