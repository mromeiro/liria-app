liriaApp.factory('Tratamentos', function($http, Utils) {

	return {

		scope: null,

		get : function() {

			return $http({
				method: 'GET',
				url: Utils.apiUrl() + 'api/treatments'
			});
		},

		submitTreatment : function(treatmentData, clientId){

			return $http({
				method: 'POST',
				url: Utils.apiUrl() + 'api/treatments/create/' +  clientId,
				headers: { 'Content-Type' : 'application/x-www-form-urlencoded' },
				data: $.param(treatmentData)
			})
		},

        updateTreatment : function(treatmentData){

            return $http({
                method: 'POST',
                url: Utils.apiUrl() + 'api/treatments/update/' + treatmentData.cliente_id,
                headers: { 'Content-Type' : 'application/x-www-form-urlencoded' },
                data: $.param(treatmentData)
            })
        }

	}

});