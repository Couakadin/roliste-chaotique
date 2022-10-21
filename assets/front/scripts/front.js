// CSS
import '../styles/front.scss';

// JS
import './modules/accordion';
import './modules/carousel';
import './modules/choices';
import './modules/cookie';
import './modules/flash';
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
        initialView  : 'dayGridWeek',
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

if (CKEDITOR) {
    CKEDITOR.config.uiColor = '#eeebe2';
}
