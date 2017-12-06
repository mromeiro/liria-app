liriaApp.factory('Relatorio', function($http, Utils) {

	return {

        generateExpenseReport : function(reportData) {

			return $http({
                method: 'POST',
                url: Utils.apiUrl() + 'api/reports/monthlyBalance',
                headers: { 'Content-Type' : 'application/x-www-form-urlencoded' },
                data: $.param(reportData)
			});
		},

        getSumupCode : function() {

            var sumupData = new Object();
            sumupData.grant_type = 'client_credentials';
            sumupData.client_id = 'R_69vB8yqDztDH1abtTTM-ii8zSp';
            sumupData.client_secret = '1e274c91-9473-4ec2-b372-8802d52c4ed2';
            sumupData.scope = 'transactions.history';

            return $http({
                method: 'POST',
                url: 'https://api.sumup.com/token',
                headers: { 'Content-Type' : 'application/x-www-form-urlencoded' },
                data: $.param(sumupData)
            });
        },

        generateConciliationReport : function(searchString) {

            return $http({
                method: 'POST',
                url: 'api/finances/conciliation',
                headers: { 'Content-Type' : 'application/x-www-form-urlencoded' },
                data: $.param(searchString)
            });
        }
	}

});