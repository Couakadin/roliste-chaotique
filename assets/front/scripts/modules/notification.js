const notifLink = document.querySelectorAll('.notification-link');
const notifItem = document.querySelectorAll('.notification-item');
const notifAll = document.getElementById('notificationReadAll');
const notifDot = document.getElementById('notificationDot');

notifAll?.addEventListener('click', function (e) {
    e.preventDefault();

    let fetch_status;
    fetch('/notifications/all', {
        method: 'POST',
        // Set the headers
        headers: {
            'Accept'      : 'application/json',
            'Content-Type': 'application/json'
        },
    })
        .then(function (response) {
            // Save the response status in a variable to use later.
            fetch_status = response.status;
            // Handle success
            return response.json();
        })
        .then(function () {
            // Check if the response were success
            if (fetch_status === 200) {
                notifItem.forEach(function (item) {
                    item.classList.remove('unread');
                })
                notifDot.classList.remove('notification-dot');
            }
        })
        .catch(function (error) {
            // Catch errors
            console.error(error);
        });
});

notifLink?.forEach(function (item) {
    item.addEventListener('click', function (e) {
        e.preventDefault();

        let fetch_status;
        fetch('/notifications', {
            method: 'POST',
            // Set the headers
            headers: {
                'Accept'      : 'application/json',
                'Content-Type': 'application/json'
            },
            // Set the post data
            body: JSON.stringify({notification: parseInt(item.getAttribute('data-notification'))})
        })
            .then(function (response) {
                // Save the response status in a variable to use later.
                fetch_status = response.status;
                // Handle success
                return response.json();
            })
            .then(function () {
                // Check if the response were success
                if (fetch_status === 200) {
                    location.href = item.getAttribute('href');
                }
            })
            .catch(function (error) {
                // Catch errors
                console.error(error);
            });
    });
});