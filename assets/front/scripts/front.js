// CSS
import '../styles/front.scss';

// JS
import './modules/accordion';
import './modules/carousel';
import './modules/choices';
import './modules/cookie';
import './modules/dragon';
import './modules/flash';
import './modules/konami';
import './modules/navbar';
import './modules/modal';
import './modules/password';
import './modules/tooltip';

import {Calendar} from '@fullcalendar/core';
import dayGridPlugin from '@fullcalendar/daygrid';

document.addEventListener('DOMContentLoaded', () => {
    const calendarEl = document.getElementById('calendar-holder');

    if (!calendarEl) {
        return;
    }

    calendarEl.innerHTML = '';

    const calendar = new Calendar(calendarEl, {
        plugins      : [dayGridPlugin],
        initialView  : 'dayGridMonth',
        timeZone     : 'Europe/paris',
        locale       : 'fr',
        headerToolbar: {
            left  : 'prev next today',
            center: 'title',
            right : 'dayGridDay dayGridWeek dayGridMonth'
        },
        eventSources : [{
            url        : '/fc-load-events',
            method     : 'POST',
            extraParams: {
                filters: JSON.stringify({})
            },
            failure    : () => {
                console.error('There was an error while fetching FullCalendar!');
            }
        }],
        buttonText: {
            today: 'aujourd\'hui',
            month: 'mois',
            week: 'semaine',
            day: 'jour'
        }
    });
    calendar.render();
});

if (typeof(CKEDITOR) !== 'undefined') {
    CKEDITOR.config.uiColor = '#eeebe2';
}

setInterval(lastTimeSeen, 300000);

function lastTimeSeen() {
    const xhttp = new XMLHttpRequest();
    xhttp.open('POST', '/online');
    xhttp.send();
}
