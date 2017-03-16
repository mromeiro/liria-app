liriaApp.factory('Tratamentos', function($http) {

	return {

		get : function() {

			return $http({
				method: 'GET',
				url: 'http://dranathaly.ddns.net:1989/api/treatments'
			});
		},

		submitTreatment : function(treatmentData, clientId){

			return $http({
				method: 'POST',
				url: 'http://dranathaly.ddns.net:1989/api/treatments/create/' + clientId,
				headers: { 'Content-Type' : 'application/x-www-form-urlencoded' },
				data: $.param(treatmentData)
			})
		},

        updateTreatment : function(treatmentData){

            return $http({
                method: 'POST',
                url: 'http://dranathaly.ddns.net:1989/api/treatments/update/' + treatmentData.cliente_id,
                headers: { 'Content-Type' : 'application/x-www-form-urlencoded' },
                data: $.param(treatmentData)
            })
        }
	}

});