import Choices from "choices.js";

const eventZone = document.getElementById('event_zone');

if (eventZone) {
    new Choices(eventZone, {
        allowHTML : false,
        noResultsText: 'Aucun résultat trouvé',
        noChoicesText: 'Aucune zone à charger',
        classNames: {
            containerInner  : 'choices-inner',
            listDropdown    : 'choices-list_dropdown',
            input           : 'choices-input',
            inputCloned     : 'choices-input_cloned',
            highlightedState: 'is-highlight',
        }
    });
}