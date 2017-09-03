liriaApp.factory('Pagamentos', function($http, Utils) {

	return {

		updatePayments : function(paymentData) {

			return $http({
                method: 'POST',
                url: Utils.apiUrl() + 'api/payments/updatePayment',
                headers: { 'Content-Type' : 'application/x-www-form-urlencoded' },
                data: $.param(paymentData)
			});
		},

        updatePaymentDate : function(paymentData) {

            return $http({
                method: 'POST',
                url: Utils.apiUrl() + 'api/payments/updatePaymentDate',
                headers: { 'Content-Type' : 'application/x-www-form-urlencoded' },
                data: $.param(paymentData)
            });
        },


        removePayments : function(paymentData) {

            return $http({
                method: 'POST',
                url: Utils.apiUrl() + 'api/payments/removePayment',
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
        },

        searchPaymentsForecast : function(searchString) {

            return $http({
                method: 'POST',
                url: Utils.apiUrl() + 'api/payments/forecast/searchByDate',
                headers: { 'Content-Type' : 'application/x-www-form-urlencoded' },
                data: $.param(searchString)
            });
        },

        submitPayments : function(dadosPagamento) {

            return $http({
                method: 'POST',
                url: Utils.apiUrl() + 'api/payments/submit',
                headers: { 'Content-Type' : 'application/x-www-form-urlencoded' },
                data: $.param(dadosPagamento)
            });
        }
	}

});