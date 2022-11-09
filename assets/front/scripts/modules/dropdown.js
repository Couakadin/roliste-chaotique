const trigger = document.querySelectorAll('.dropdown-trigger');

trigger.forEach(function (item) {
    item.addEventListener('mouseover', function () {
        this.parentElement.querySelector('.dropdown').classList.add('active');
    });
    item.addEventListener('mouseleave', function () {
        this.parentElement.querySelector('.dropdown').classList.remove('active');
    });
});