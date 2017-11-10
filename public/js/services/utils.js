liriaApp.factory('Utils', function($http) {

	return {

	    scope : null,

        apiUrl : function(){

            return 'http://localhost:80/';
        },

        getAppConfig : function(scope){

            return $http({
                method: 'GET',
                url: 'api/config',
                headers: { 'Content-Type' : 'application/x-www-form-urlencoded' }
            })

        },


        getConfig : function(config, configName){

            for(var i = 0; i < config.length ; i++){
                if(config[i].nome = configName)
                    return config[i].valor
            }

            return null;
        },

        //input: DD/MM/YYYY HH:MI
        //output: YYYY-MM-DD HH:MI:00
        formatDateToCalendar : function(date){

            //2017-06-04T15:20:00
            var split = date.split("/");
            var splitTime = split[2].split(" ");

            if(splitTime[1] == 'undefined' || splitTime[1] == ''){
                splitTime[1] = '00:00';
            }

            var formattedDate = splitTime[0] + '-' + split[1] + '-' + split[0] + 'T' + splitTime[1] + ':00';
            return formattedDate;
        },

        //input: YYYY-MM-DD
        //output: DD/MM/YYYY
        formatCalendarToDate : function(date){

            var split = date.split("-");
            var splitMinute = split[2].split(" ");
            var minute = splitMinute[0];

            var formattedDate = minute + '/' + split[1] + '/' + split[0];
            return formattedDate;
        },

        //input: YYYY-MM-DDTHH:MI:SS
        //output: DD/MM/YYYY HH:MI
        formatCalendarTimestampToDate : function(date, exportInfo){

            //2017-06-04T15:20:00
            var split = date.split("-");
            var splitTime = split[2].split("T");

            var time = splitTime[1].split(":");

            var formattedDate = splitTime[0] + '/' + split[1] + '/' + split[0];
            var formattedHour = time[0] + ':' + time[1];

            if(exportInfo == 'date'){
                return formattedDate;
            }else if(exportInfo == 'hour'){
                return formattedHour;
            }
        },

        //Bind the scope from the auto complete app
        bindAutoCompleteScope : function(scope){
            this.scope = scope;
        },

        //Displays the search param in the input field of the auto complete
        setAutoCompleteSearchParam : function(searchParam){
            this.scope.searchParam = searchParam;
        }

	}

});