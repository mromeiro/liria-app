liriaApp.factory('Calendar', function($http, Utils) {

	return {

		createCalendarEvent : function(scheduleEvent) {

			return $http({
                method: 'POST',
                url: Utils.apiUrl() + 'api/schedule/createEvent',
                headers: { 'Content-Type' : 'application/x-www-form-urlencoded' },
                data: $.param(scheduleEvent)
			});
		},

        updateCalendarEvent : function(scheduleEvent) {

            return $http({
                method: 'POST',
                url: Utils.apiUrl() + 'api/schedule/updateEvent',
                headers: { 'Content-Type' : 'application/x-www-form-urlencoded' },
                data: $.param(scheduleEvent)
            });
        },

        removeCalendarEvent : function(scheduleEvent) {

            return $http({
                method: 'POST',
                url: Utils.apiUrl() + 'api/schedule/removeEvent',
                headers: { 'Content-Type' : 'application/x-www-form-urlencoded' },
                data: $.param(scheduleEvent)
            });
        },

        getCalendarEvents : function(scheduleEvent) {

            return $http({
                method: 'POST',
                url: Utils.apiUrl() + 'api/schedule/getEvents',
                headers: { 'Content-Type' : 'application/x-www-form-urlencoded' },
                data: $.param(scheduleEvent)
            });
        },

	}

});