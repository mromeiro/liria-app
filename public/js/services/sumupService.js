liriaApp.factory('Sumup', function($http, Utils) {

	return {

        authorizeSumup : function(code) {

            var sumupData = new Object();
            sumupData.code = code;

            return $http({
                method: 'POST',
                url: 'api/sumup/authorize',
                headers: { 'Content-Type' : 'application/x-www-form-urlencoded' },
                data: $.param(sumupData)
            });
		}
	}

});