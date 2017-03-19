liriaApp.factory('Expenses', function($http, Utils, Upload) {

		return {

            getMonthlyExpenses: function(expenseData) {

                return $http({
                    method: 'GET',
                    url: Utils.apiUrl() + 'api/expenses/mes',
                    headers: { 'Content-Type' : 'application/x-www-form-urlencoded' }
                });
            },

            submitMonthlylExpense: function(expenseData) {

                return $http({
                    method: 'POST',
                    url: Utils.apiUrl() + 'api/expenses/new',
                    headers: { 'Content-Type' : 'application/x-www-form-urlencoded' },
                    data: $.param(expenseData)
                });
            },

            submitExpenseReceipt: function(file, expenseId) {

                return Upload.upload({
                    url: Utils.apiUrl() + 'api/expenses/receipt/' + expenseId,
                    data: {file: file},
                });
            }

		}

	});