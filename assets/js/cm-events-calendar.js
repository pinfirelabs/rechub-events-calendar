(function($)
 {
	 if (typeof(clubmanager_timezone) == 'undefined')
	 {
		 clubmanger_timezone = 'local';
	 }

	 var arraysEqual = function(a1, a2)
	 {
		 return a1 === a2 || 
			 (
				 a1 !== null && a2 !== null &&
				 a1.length === a2.length &&
				 a1
					 .map(function (val, idx) { return val === a2[idx]; })
					 .reduce(function (prev, cur) { return prev && cur; }, true)
			 );
	 }

	 var updateEvents = function(start, end, timezone, callback)
	 {
		 $.ajax({
			 url: clubmanager_feed_url,
			 dataType: 'json',
			 data: {
				 start: start.toISOString(),
				 end: end.toISOString(),
				 'category[]': $('#cm-events-category-selector').val(),
				 'club_ID[]': $('#cm-events-club-selector').val()
			 },
			 success: function(data) {
				 callback(data);
			 }
		 });
	 };

	 $('#cm-events-calendar').fullCalendar({
		 events: updateEvents,
		 timezone: clubmanager_timezone,
		 header: {
			 left: 'prev,next today',
			 center: 'title',
			 right: 'month,agendaWeek,agendaDay'
		 },
		 defaultView: 'month',
		 height: "auto"
	 });

	 $('#cm-events-calendar-wrapper select').select2({
		 minimumResultsForSearch: 0,
		 closeOnSelect: false,
		 placeholder: 'All',
		 allowClear: true
	 });

	 var updateCategories = function(e)
	 {
		 var categories = [];
		 var selectedClubs = $('#cm-events-club-selector').val();
		 if (!Array.isArray(selectedClubs) || selectedClubs.length == 0)
		 {
			 selectedClubs = Object.keys(clubmanager_event_categories);
		 }
		 
		 for (i = 0; i < selectedClubs.length; i++)
		 {
			 var club_ID = selectedClubs[i];
			 var clubCategories = clubmanager_event_categories[club_ID];
			 if (Array.isArray(clubCategories) && clubCategories.length > 0)
			 {
				 categories.push({
					 text: clubmanager_clubs[club_ID],
					 children: clubmanager_event_categories[club_ID]
				 });
			 }
		 }

		 $('#cm-events-category-selector').find('option, optgroup').remove();
		 $('#cm-events-category-selector').select2({
			 data: categories,
			 placeholder: 'All'
		 });

		 if (e && e.target.id == 'cm-events-club-selector')
		 {
			 $('#cm-events-calendar').fullCalendar('refetchEvents');
		 }
	 };

	 updateCategories();

	 var oldSelectedClubs = $('#cm-events-club-selector').val();
	 $('#cm-events-club-selector').on('select2:close', function(e)
	 {
		 var selectedClubs = $('#cm-events-club-selector').val();
		 if (!arraysEqual(oldSelectedClubs, selectedClubs))
		 {
			 oldSelectedClubs = selectedClubs;

			 updateCategories(e);
		 }
	 });

	 var oldSelectedCategories = [];
	 $('#cm-events-category-selector').on('select2:close', function()
	 {
		 var selectedCategories = $('#cm-events-category-selector').val();
		 if (!arraysEqual(oldSelectedCategories, selectedCategories))
		 {
			 oldSelectedCategories = selectedCategories;
			 $('#cm-events-calendar').fullCalendar('refetchEvents');
		 }
	 });
})(jQuery);
