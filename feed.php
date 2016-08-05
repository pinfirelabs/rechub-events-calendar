<?php
/**
 * Generates the feed for fullcalendar of CM events.
 *
 * @param string $cmServer Club Manager server to use
 * @param callable $eventFetchFunction optional callback to get event data instead of using API.  Definition:
 * 		function($searchParams) { return $events; }
 * @return array array of calendar event objects (@see http://fullcalendar.io/docs/event_data/Event_Object/)
 */
if (empty($cmServer)) {
	throw new \Exception('$cmServer is required for this script.');
}

foreach (['start', 'end'] as $required) {
	if (empty($_REQUEST[$required])) {
		throw new \Exception("\${$required} is a required parameter.");
	}
}

$params = [
	'start' => $_REQUEST['start'],
	'end' => $_REQUEST['end'],
	'club_ID' => isset($_REQUEST['club_ID']) ? $_REQUEST['club_ID'] : null,
	'category' => isset($_REQUEST['category']) ? $_REQUEST['category'] : null,
	'pageSize' => 0 
];

if (is_callable($eventFetchFunction))
{
	$events = call_user_func($eventFetchFunction, $params);
}
else
{
	require_once('functions.php');
	
	$events = makeServiceCall('GET', '/api/event', $params);
}

$calendarEvents = [];
foreach ($events as $event)
{
	$i = 0;
	foreach ($event->schedule as $schedule)
	{
		if (
			strtotime($schedule->end) < strtotime($_REQUEST['start']) || 
			strtotime($schedule->start) > strtotime($_REQUEST['end'])
		)
		{
			continue;
		}

		$calendarEvent = new stdClass; 
		
		$calendarEvent->id = $event->event_ID . '_' . $i;
		$calendarEvent->title = $event->name;

		$calendarEvent->allDay = false;
		$calendarEvent->start = $schedule->start;
		$calendarEvent->end = $schedule->end;
		$calendarEvent->url = $cmServer . '/event/' . $event->event_ID; 
		$color = isset($event->category) ? $event->category->effectiveColor : null;
		$calendarEvent->color = isset($color) ? $color : '#543232';

		$calendarEvents[] = $calendarEvent;
		$i++;
	}
}

return $calendarEvents;
