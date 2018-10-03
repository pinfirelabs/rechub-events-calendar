<?php

namespace threelakessoftware\sharedEventsCalendar;

class CalendarMaker {
    public function __construct($api, $searchStr = '') 
    {
        global $cmServer;
        global $cmApiServer;
        global $search;

        $cmServer = $cmApiServer = $api;

        $search = $searchStr;
    }

    public $scripts = [
        'bower-asset/moment/min/moment.min.js',
        'bower-asset/fullcalendar/dist/fullcalendar.js',
        'bower-asset/select2/dist/js/select2.min.js',
    ];

    public $styles = [
        'bower-asset/fullcalendar/dist/fullcalendar.css',
        'bower-asset/select2/dist/css/select2.min.css',
    ];

    public function getConScripts()
    {
        ob_start();
        include(__DIR__ . '/../parts/con-scripts.php');
        return ob_get_clean();
    }

    public function getFilterRow() 
    {
        ob_start();
        include(__DIR__ . '/../parts/filter-row.php');
        return ob_get_clean();
    }

    public function getCalendarRow() 
    {
        ob_start();
        include(__DIR__ . '/../parts/cal.php');
        return ob_get_clean();
    }
}