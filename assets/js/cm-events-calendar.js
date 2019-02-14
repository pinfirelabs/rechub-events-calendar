var PcmCalendar = function($)
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

	var isBasic = false;
	var buttons = 'month,agendaWeek,agendaDay';
	if (window.innerWidth < 720 || (typeof(clubmanager_force_basic_views) != 'undefined' && clubmanager_force_basic_views))
	{
		buttons = 'month,basicWeek,basicDay';
		isBasic = true;
	}

	// Returns a function, that, as long as it continues to be invoked, will not
	// be triggered. The function will be called after it stops being called for
	// N milliseconds. If `immediate` is passed, trigger the function on the
	// leading edge, instead of the trailing.
	function debounce(func, wait, immediate) {
		var timeout;
		return function() {
			var context = this, args = arguments;
			var later = function() {
				timeout = null;
				if (!immediate) func.apply(context, args);
			};
			var callNow = immediate && !timeout;
			clearTimeout(timeout);
			timeout = setTimeout(later, wait);
			if (callNow) func.apply(context, args);
		};
	};

	function renderFunc(event, element) {
		if (typeof(clubmanager_disable_status_overlay) == 'undefined' || !clubmanager_disable_status_overlay)
		{
			icon = $('<span class="status-icon"></span>');
			icon.addClass('status-' + event.signupStatus);
			icon.css('background-color', element.css('background-color'));

			element
				.find('.fc-content')
				.addClass('has-icon')
				.append(icon);
		}

		switch (event.signupStatus)
		{
			case "started":
				element.prop('title', "Event has started");
				break;
			case "cancelled":
				element.prop('title', "Event has been cancelled");
				break;
			case "full":
				element.prop('title', "Event is full.");
				break;
		}
	}

	if (typeof(clubmanager_force_basic_views) == 'undefined' || !clubmanager_force_basic_views)
	{
		$(window).on('resize', debounce(function()
		{
			var newIsBasic = (window.innerWidth < 720);

			if (newIsBasic != isBasic)
			{
				var buttons = newIsBasic ? 'month,basicWeek,basicDay' : 'month,agendaWeek,agendaDay';
				var currentView = calendar.fullCalendar('getView').name;
				calendar.fullCalendar('destroy');

				if (newIsBasic)
				{
					currentView = currentView.replace('agenda', 'basic');
				}
				else
				{
					currentView = currentView.replace('basic', 'agenda');
				}

				calendar.fullCalendar({
					events: updateEvents,
					timezone: clubmanager_timezone,
					header: {
						left: 'prev,next today',
						center: 'title',
						right: buttons 
					},
					defaultView: currentView,
					height: "auto",
					fixedWeekCount: false,
					eventRender: renderFunc
				});

				isBasic = newIsBasic;
			}
		}, 500));
	}

	var updateEvents = function(start, end, timezone, callback)
	{
		var duration = end.unix() - start.unix();

		$.ajax({
			url: clubmanager_feed_url,
			dataType: 'json',
			data: {
				start: start.toISOString(),
				end: end.toISOString(),
				'category[]': $('#cm-events-category-selector').val(),
				'club_ID[]': $('#cm-events-club-selector').val()
			},
			success: callback
		});
	};

	/**
	 * @param Object state {
	 * 		@type int[] clubs
	 * 		@type int[] categories
	 * }
	 */
	var refreshEventsFromState = function(state) {
		if (!(state instanceof Object) || state == null)
		{
			return;
		}

		if (state.hasOwnProperty("clubs"))
		{
			$('#cm-events-club-selector')
				.val(state.clubs)
				.trigger('change');
		}

		if (state.hasOwnProperty("categories"))
		{
			$('#cm-events-category-selector')
				.val(state.categories)
				.trigger('change');
		}

		$('#cm-events-calendar').fullCalendar('refetchEvents');
	};

	window.onpopstate = function(e)
	{
		refreshEventsFromState(e.state);
	};

	var saveState = function() {
		var stateObj = {
			'clubs': $('#cm-events-club-selector').val(),
			'categories': $('#cm-events-category-selector').val()
		};

		window.history.pushState(stateObj, '', '?calendar-state=' + window.btoa(JSON.stringify(stateObj)));
	}

	// Parse current query params
	var getParameterByName = function (name) {
		url = window.location.href;
		name = name.replace(/[\[\]]/g, "\\$&").toLowerCase();// This is just to avoid case sensitiveness for query parameter name
		var regex = new RegExp("[?&]" + name + "(=([^&#]*)|&|#|$)"),
			results = regex.exec(url);
		if (!results) return null;
		if (!results[2]) return '';
		return decodeURIComponent(results[2].replace(/\+/g, " "));
	};

	var calendar = $('#cm-events-calendar').fullCalendar({
		events: updateEvents,
		timezone: clubmanager_timezone,
		header: {
			left: 'prev,next today',
			center: 'title',
			right: buttons
		},
		defaultView: 'month',
		height: "auto",
		fixedWeekCount: false,
		eventRender: renderFunc
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
					children: clubCategories,
				});
			}
		}

		$('#cm-events-category-selector').find('option, optgroup').remove();
		$('#cm-events-category-selector').select2({
			data: categories,
			placeholder: 'All'
		});

		var legend = $('#category-legend');
		legend.empty();

		categories.forEach(function(club)
		{
			if (!Array.isArray(club.children) && club.children.length == 0)
			{
				return;
			}

			club.children.forEach(function(category)
			{
				var li = $('<li class="category-legend-item"></li>');
				var colorSpan = $('<span class="category-legend-color"></span>');
				colorSpan.css('background-color', category.color);
				li.append(colorSpan);

				var nameSpan = $('<span class="category-legend-name"></span>');
				nameSpan.text(category.text);
				li.append(nameSpan);

				legend.append(li);
			});
		});


		if (e && e.target.id == 'cm-events-club-selector')
		{
			$('#cm-events-calendar').fullCalendar('refetchEvents');
		}
	};

	updateCategories();

	// This needs to be after we've loaded the categories we need
	var currentState = getParameterByName('calendar-state');
	if (currentState != undefined && currentState != '')
	{
		try
		{
			currentState = JSON.parse(window.atob(currentState));
			if (currentState)
			{
				refreshEventsFromState(currentState);
			}
		}
		catch (e)
		{
			console.error("Invalid state: " + e);
		}
	}

	var oldSelectedClubs = $('#cm-events-club-selector').val();
	$('#cm-events-club-selector').on('select2:close', function(e)
	{
		var selectedClubs = $('#cm-events-club-selector').val();
		if (!arraysEqual(oldSelectedClubs, selectedClubs))
		{
			oldSelectedClubs = selectedClubs;
			updateCategories(e);

			saveState();
		}
	});

	var oldSelectedCategories = [];
	$('#cm-events-category-selector').on('select2:close', function()
	{
		var selectedCategories = $('#cm-events-category-selector').val();
		if (!arraysEqual(oldSelectedCategories, selectedCategories))
		{
			oldSelectedCategories = selectedCategories;
			saveState();

			$('#cm-events-calendar').fullCalendar('refetchEvents');
		}
	});
};

// Old IE polyfills
// From https://developer.mozilla.org/en-US/docs/Web/JavaScript/Reference/Global_Objects/Object/keys
if (!Object.keys) {
  Object.keys = (function() {
    'use strict';
    var hasOwnProperty = Object.prototype.hasOwnProperty,
        hasDontEnumBug = !({ toString: null }).propertyIsEnumerable('toString'),
        dontEnums = [
          'toString',
          'toLocaleString',
          'valueOf',
          'hasOwnProperty',
          'isPrototypeOf',
          'propertyIsEnumerable',
          'constructor'
        ],
        dontEnumsLength = dontEnums.length;

    return function(obj) {
      if (typeof obj !== 'function' && (typeof obj !== 'object' || obj === null)) {
        throw new TypeError('Object.keys called on non-object');
      }

      var result = [], prop, i;

      for (prop in obj) {
        if (hasOwnProperty.call(obj, prop)) {
          result.push(prop);
        }
      }

      if (hasDontEnumBug) {
        for (i = 0; i < dontEnumsLength; i++) {
          if (hasOwnProperty.call(obj, dontEnums[i])) {
            result.push(dontEnums[i]);
          }
        }
      }
      return result;
    };
  }());
}

if (!Array.isArray) {
	Array.isArray = function(v) {
		return v instanceof Array;
	}
}


// Now that we have those covered, initialize the calendar now.
PcmCalendar(jQuery);
