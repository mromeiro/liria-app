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
                    url: Utils.apiUrl() + 'api/expenses/get',
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

            updateExpense: function(expenseData) {

                return $http({
                    method: 'POST',
                    url: Utils.apiUrl() + 'api/expenses/update',
                    headers: { 'Content-Type' : 'application/x-www-form-urlencoded' },
                    data: $.param(expenseData)
                });
            },

            //Upload expense receipt
            submitExpenseReceipt: function(file) {

                return Upload.upload({
                    url: Utils.apiUrl() + 'api/expenses/receipt',
                    data: {file: file},
                });
            },

            //Submit a new expense
            searchExpenseForecast: function(expenseData) {

                return $http({
                    method: 'POST',
                    url: Utils.apiUrl() + 'api/expenses/forecast/searchByDate',
                    headers: { 'Content-Type' : 'application/x-www-form-urlencoded' },
                    data: $.param(expenseData)
                });
            },

		}

	});