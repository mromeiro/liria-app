liriaApp.factory('Tratamentos', function($http) {

	return {

		get : function() {

			return $http({
				method: 'GET',
				url: 'http://localhost:80/api/treatments'
			});
		},

		submitTreatment : function(treatmentData, clientId){

			return $http({
				method: 'POST',
				url: 'http://localhost:80/api/treatments/create/' + clientId,
				headers: { 'Content-Type' : 'application/x-www-form-urlencoded' },
				data: $.param(treatmentData)
			})
		}
	}

});