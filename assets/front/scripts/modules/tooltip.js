const changeTooltipPosition = (event) => {
    const tooltipModal = document.querySelector('.tooltip-modal');
    let tooltipX = event.pageX - 8;
    let tooltipY = event.pageY + 8;

    const mobile = window.innerWidth < 768;

    if (mobile) {
        tooltipModal.style.top = tooltipY + 'px';
        tooltipModal.style.right = '0';
    } else {
        tooltipModal.style.top = tooltipY + 'px';
        tooltipModal.style.left = tooltipX + 'px';
    }
};

const showTooltip = (event) => {
    const tooltipModal = document.querySelector('.tooltip-modal');
    const body = document.querySelector('body');

    if (tooltipModal) {tooltipModal.remove();}

    const target = document.createElement('div');
    target.classList.add('tooltip-modal');
    target.innerHTML = event.currentTarget.getAttribute('data-tooltip');
    body.appendChild(target);

    changeTooltipPosition(event);
};

const hideTooltip = () => {
    const tooltipModal = document.querySelector('.tooltip-modal');

    tooltipModal.remove();
};

const dataTooltip = document.querySelectorAll('[data-tooltip]');

Array.from(dataTooltip).forEach((item) => {
    item.addEventListener('mouseenter', showTooltip);
    item.addEventListener('mousemove', changeTooltipPosition);
    item.addEventListener('mouseleave', hideTooltip);
});
