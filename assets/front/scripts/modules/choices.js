import Choices from "choices.js";

const arrayChoices = [
    document.getElementById('event_zone'),
    document.getElementById('storage_folder')
]

arrayChoices.forEach(function (choice) {
    if (choice) {
        new Choices(choice, {
            allowHTML    : false,
            noResultsText: 'Aucun résultat trouvé',
            noChoicesText: 'Aucune zone à charger',
            classNames   : {
                containerInner  : 'choices-inner',
                listDropdown    : 'choices-list_dropdown',
                input           : 'choices-input',
                inputCloned     : 'choices-input_cloned',
                highlightedState: 'is-highlight',
            }
        });
    }
})