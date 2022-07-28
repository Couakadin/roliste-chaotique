const flash = document.querySelectorAll('.flash');

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
