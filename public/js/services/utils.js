liriaApp.factory('Utils', function($http) {

	return {

        formatDateForPage : function(date){

            var split = date.split("-");
            var splitMinute = split[2].split(" ");
            var minute = splitMinute[0];

            var formattedDate = minute + '/' + split[1] + '/' + split[0];
            return formattedDate;
        },

        apiUrl : function(date){

            return 'http://dranathaly.ddns.net:1989/';
        }

	}

});