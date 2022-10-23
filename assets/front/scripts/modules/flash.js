const flash = document.querySelectorAll('.flash');
const badge = document.querySelectorAll('.badge');

Array.from(flash).forEach((item) => {
    const flashCross = item.querySelector('.flash-cross');

    setTimeout(() => {
        item.classList.add('show');
    }, 800);

    setTimeout(() => {
        item.classList.remove('show');
    }, 8000);

    flashCross.addEventListener('click', () => {
        item.classList.remove('show');
    });
});

Array.from(badge).forEach((item) => {
    const badgeDot = item.querySelector('.badge-dot');

    setTimeout(() => {
        item.classList.add('show');
    }, 800);

    setTimeout(() => {
        item.classList.remove('show');
    }, 8000);

    badgeDot.addEventListener('click', () => {
        item.classList.remove('show');
    });
});
