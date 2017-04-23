liriaApp.factory('Expenses', function($http, Utils, Upload) {

		return {

		    //Get all expensed recorded as recurrent
            getMonthlyExpenses: function(expenseData) {

                return $http({
                    method: 'GET',
                    url: Utils.apiUrl() + 'api/expenses/monthlyExpenses',
                    headers: { 'Content-Type' : 'application/x-www-form-urlencoded' }
                });
            },

            //Submit a recurrent expense
            submitMonthlylExpense: function(expenseData) {

                return $http({
                    method: 'POST',
                    url: Utils.apiUrl() + 'api/expenses/monthly/new',
                    headers: { 'Content-Type' : 'application/x-www-form-urlencoded' },
                    data: $.param(expenseData)
                });
            },

            //Get all expenses for a given month
            getExpenses: function(expenseData){

                return $http({
                    method: 'POST',
                    url: Utils.apiUrl() + 'api/expenses/month',
                    headers: { 'Content-Type' : 'application/x-www-form-urlencoded' },
                    data: $.param(expenseData)
                })
            },

            //Submit a new expense
            submitExpense: function(expenseData) {

                return $http({
                    method: 'POST',
                    url: Utils.apiUrl() + 'api/expenses/new',
                    headers: { 'Content-Type' : 'application/x-www-form-urlencoded' },
                    data: $.param(expenseData)
                });
            },

            //Upload expense receipt
            submitExpenseReceipt: function(file, expenseId) {

                return Upload.upload({
                    url: Utils.apiUrl() + 'api/expenses/receipt/' + expenseId,
                    data: {file: file},
                });
            }

		}

	});