<?php
/**
 * Generates the feed for fullcalendar of CM events.
 *
 * @param string $cmApiServer API server to use
 * @return array array of calendar event objects (@see http://fullcalendar.io/docs/event_data/Event_Object/)
 */
require_once('functions.php');

use GuzzleHttp\Psr7\Request;

if (empty($cmApiServer)) {
	throw new \Exception('$cmApiServer is required for this script.');
}

foreach (['start', 'end'] as $required) {
	if (empty($_REQUEST[$required])) {
		throw new \Exception("\${$required} is a required parameter.");
	}
}

$events = makeServiceCall('GET', '/api/event', [
	'start' => $_REQUEST['start'],
	'end' => $_REQUEST['end'],
	'club_ID' => isset($_REQUEST['club_ID']) ? $_REQUEST['club_ID'] : null,
	'category' => isset($_REQUEST['category']) ? $_REQUEST['category'] : null,
	'pageSize' => 0 
]);

$calendarEvents = [];
foreach ($events as $event)
{
	$i = 0;
	foreach ($event->schedule as $schedule)
	{
		if (strtotime($schedule->start) < strtotime($_REQUEST['start']))
		{
			continue;
		}

		$calendarEvent = new stdClass; 
		
		$calendarEvent->id = $event->event_ID . '_' . $i;
		$calendarEvent->title = htmlspecialchars($event->name);

		$calendarEvent->allDay = false;
		$calendarEvent->start = $schedule->start;
		$calendarEvent->end = $schedule->end;
		$calendarEvent->url = $cmApiServer . '/event/' . $event->event_ID; 
		$calendarEvent->color = isset($event->subcategory->category->color) ? $event->subcategory->category->color : '#543232';

		$calendarEvents[] = $calendarEvent;
		$i++;
	}
}

return $calendarEvents;
