liriaApp.factory('Tratamentos', function($http) {

	return {

		get : function() {

			return $http({
				method: 'GET',
				url: 'http://dranathaly.app:8000/api/treatments'
			});
		},

		submitTreatment : function(treatmentData, clientId){

			return $http({
				method: 'POST',
				url: 'http://dranathaly.app:8000/api/treatments/create/' + clientId,
				headers: { 'Content-Type' : 'application/x-www-form-urlencoded' },
				data: $.param(treatmentData)
			})
		}
	}

});