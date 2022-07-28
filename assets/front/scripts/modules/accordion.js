const openAccordion = (accordion) => {
    const content = accordion.nextElementSibling;
    content.style.maxHeight = content.scrollHeight + 'px';
    accordion.setAttribute('data-accordion', 'false');
};

const closeAccordion = (accordion) => {
    const content = accordion.nextElementSibling;
    content.style.maxHeight = null;
    accordion.setAttribute('data-accordion', 'true');
};

const accordions = document.querySelectorAll('[data-accordion]');

accordions.forEach((accordion) => {
    const content = accordion.nextElementSibling;

    accordion.onclick = () => {
        if (content.style.maxHeight) {
            closeAccordion(accordion);
        } else {
            accordions.forEach((accordion) => closeAccordion(accordion));
            openAccordion(accordion);
        }
    };
});