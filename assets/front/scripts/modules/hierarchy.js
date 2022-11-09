const hierarchies = document.querySelectorAll('.hierarchy-link');

hierarchies.forEach(function (hierarchy) {
    hierarchy.addEventListener('dblclick', function (e) {
        e.preventDefault();
        location.href = this.getAttribute('href');
    })

    hierarchy.addEventListener('click', function (e) {
        e.preventDefault();
        if (hierarchy.parentElement.classList.contains('hierarchy-parent')) {
            this.parentElement.querySelector('.hierarchy-root').classList.toggle('active');
            this.classList.toggle('down');
        }
    });
});