liriaApp.factory('Pagamentos', function($http) {

	return {

		updatePaymentDate : function(paymentData) {

			return $http({
                method: 'POST',
                url: 'http://dranathaly.ddns.net:1989/api/payments/updatePaymentDate',
                headers: { 'Content-Type' : 'application/x-www-form-urlencoded' },
                data: $.param(paymentData)
			});
		},

        searchPayments : function(searchString) {

            return $http({
                method: 'POST',
                url: 'http://dranathaly.ddns.net:1989/api/payments/searchByDate',
                headers: { 'Content-Type' : 'application/x-www-form-urlencoded' },
                data: $.param(searchString)
            });
        }
	}

});