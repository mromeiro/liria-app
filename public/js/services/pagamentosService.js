liriaApp.factory('Pagamentos', function($http, Utils) {

	return {

		updatePaymentDate : function(paymentData) {

			return $http({
                method: 'POST',
                url: Utils.apiUrl() + 'api/payments/updatePaymentDate',
                headers: { 'Content-Type' : 'application/x-www-form-urlencoded' },
                data: $.param(paymentData)
			});
		},

        searchPayments : function(searchString) {

            return $http({
                method: 'POST',
                url: Utils.apiUrl() + 'api/payments/searchByDate',
                headers: { 'Content-Type' : 'application/x-www-form-urlencoded' },
                data: $.param(searchString)
            });
        }
	}

});