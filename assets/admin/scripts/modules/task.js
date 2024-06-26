const addTask = document.querySelector('#addTask');
const countTask = document.querySelector('#taskCount');
const countTaskString = document.querySelector('#taskCountString');
const listTask = document.querySelector('#taskList');
const errorTask = document.querySelector('#taskError');

addTask?.addEventListener('keyup', (evt) => {
    evt.preventDefault();
    if (evt.key === 'Enter') {
        if (addTask.value.length > 255) {
            errorTask.textContent = '';
            errorTask.textContent = `Limite de caractère à 255. Actuelle : ${addTask.value.length}`;
            return;
        }
        addTaskAction();
    }
});

document.addEventListener('click', function (e) {
    if (e.target.matches('.task-status')) {
        removeTaskAction(e.target.value);
    }
});

function removeTaskAction(target) {
    let fetch_status;
    fetch('/oversight/ajax/remove-task', {
        method: 'POST',
        // Set the headers
        headers: {
            'Accept': 'application/json',
            'Content-Type': 'application/json'
        },
        // Set the post data
        body: JSON.stringify({todo: target})
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
                // Use the converted JSON
                const oldTask = document.getElementById('taskItem-' + target);
                oldTask.parentNode.removeChild(oldTask);

                countTask.innerHTML = +parseInt(countTask.innerHTML) - 1;

                if (parseInt(countTask.innerHTML) > 1) {
                    countTaskString.innerHTML = 'tâches';
                } else {
                    countTaskString.innerHTML = 'tâche';
                }
            }
        })
        .catch(function (error) {
            // Catch errors
            console.error(error);
        });
}

function addTaskAction() {
    let fetch_status;
    fetch('/oversight/ajax/add-task', {
        method: 'POST',
        // Set the headers
        headers: {
            'Accept': 'application/json',
            'Content-Type': 'application/json'
        },
        // Set the post data
        body: JSON.stringify({todo: addTask.value})
    })
        .then(function (response) {
            // Save the response status in a variable to use later.
            fetch_status = response.status;
            // Handle success
            return response.json();
        })
        .then(function (json) {
            // Check if the response were success
            if (fetch_status === 200) {
                // Use the converted JSON
                const newTaskItem = document.createElement('div');
                newTaskItem.id = `taskItem-${json.id}`;
                newTaskItem.classList.add('task-item');

                const newTaskInput = document.createElement('input');
                newTaskInput.id = `task-${json.id}`;
                newTaskInput.classList.add('task-status');
                newTaskInput.type = 'checkbox'
                newTaskInput.value = `${json.id}`;

                const newTaskLabel = document.createElement('label');
                newTaskLabel.classList.add('task-name');
                newTaskLabel.setAttribute('for', `task-${json.id}`);
                newTaskLabel.innerHTML = `${json.todo}`;

                newTaskItem.appendChild(newTaskInput);
                newTaskItem.appendChild(newTaskLabel);

                listTask.appendChild(newTaskItem);

                addTask.value = '';
                countTask.innerHTML = +parseInt(countTask.innerHTML) + 1;

                if (parseInt(countTask.innerHTML) > 1) {
                    countTaskString.innerHTML = 'tâches';
                } else {
                    countTaskString.innerHTML = 'tâche';
                }
            }
        })
        .catch(function (error) {
            // Catch errors
            console.error(error);
        });
}
