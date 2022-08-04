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
    calendarEl.innerHTML = '';

    const calendar = new Calendar(calendarEl, {
        plugins      : [dayGridPlugin],
        initialView  : 'dayGridWeek',
        timeZone     : 'Europe/paris',
        locale       : 'fr',
        headerToolbar: {
            left  : 'prev',
            center: 'title',
            right : 'next'
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
        }]
    });
    calendar.render();
});

//CKEDITOR.config.uiColor = '#eeebe2';