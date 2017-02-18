liriaApp.factory('Pagamentos', function($http) {

	return {

		updatePaymentDate : function(paymentData) {

			return $http({
                method: 'POST',
                url: 'http://localhost:80/api/payments/updatePaymentDate',
                headers: { 'Content-Type' : 'application/x-www-form-urlencoded' },
                data: $.param(paymentData)
			});
		}
	}

});