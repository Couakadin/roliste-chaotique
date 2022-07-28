const cookieBanner = document.getElementById('cookie-banner');
const cookieBtn = document.getElementById('cookie-btn');

if (cookieBtn) {
    cookieBtn.addEventListener('click', () => {
        ajaxCookie();

        cookieBanner.style.cssText = 'transform: translateY(0); transition: transform .6s ease-out;';
        cookieBanner.style.transform = 'translateY(200%)';
    });

    function ajaxCookie() {
        const xhttp = new XMLHttpRequest();

        xhttp.open('GET', '/cookie-policy?cookie=true');
        xhttp.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
        xhttp.send();
    }
}