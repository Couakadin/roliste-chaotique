// CSS
import '../styles/front.scss';

// JS
import './modules/accordion';
import './modules/carousel';
import './modules/choices';
import './modules/cookie';
import './modules/display';
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
        plugins: [dayGridPlugin],
        initialView: 'dayGridMonth',
        timeZone: 'Europe/paris',
        locale: 'fr',
        dayMaxEventRows: true,
        height: '100vh',
        views: {
            timeGrid: {
                dayMaxEventRows: 6 // adjust to 6 only for timeGridWeek/timeGridDay
            }
        },
        headerToolbar: {
            left: 'prev next today',
            center: 'title',
            right: 'dayGridDay dayGridWeek dayGridMonth'
        },
        eventSources: [{
            url: '/fc-load-events',
            method: 'POST',
            extraParams: {
                filters: JSON.stringify({})
            },
            failure: () => {
                console.error('There was an error while fetching FullCalendar!');
            }
        }],
        eventTimeFormat: {
            hour: '2-digit',
            minute: '2-digit',
            meridiem: false
        },
        buttonText: {
            today: 'aujourd\'hui',
            month: 'mois',
            week: 'semaine',
            day: 'jour',
        },
        moreLinkText: 'autre'
    });
    calendar.render();
});

if (typeof (CKEDITOR) !== 'undefined') {
    CKEDITOR.config.uiColor = '#eeebe2';
    CKEDITOR.addCss('.cke_editable { background-color: #eeebe2; color: #241F1E }');
}
