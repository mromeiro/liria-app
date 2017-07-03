liriaApp.factory('Relatorio', function($http, Utils) {

	return {

        generateExpenseReport : function(reportData) {

			return $http({
                method: 'POST',
                url: Utils.apiUrl() + 'api/reports/monthlyBalance',
                headers: { 'Content-Type' : 'application/x-www-form-urlencoded' },
                data: $.param(reportData)
			});
		}
	}

});