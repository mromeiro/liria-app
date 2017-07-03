liriaApp.factory('Clients', function($http, Utils) {

		return {
			get : function() {
				return $http.get('http://dranathaly.ddns.net:1989/api/clients');
			},

			save : function(clientsData) {
				return $http({
					method: 'POST',
					url: Utils.apiUrl() + 'api/clients',
					headers: { 'Content-Type' : 'application/x-www-form-urlencoded' },
					data: $.param(clientsData)
				});
			},

            update : function(clientsData) {
                return $http({
                    method: 'POST',
                    url: Utils.apiUrl() + 'api/clients/update',
                    headers: { 'Content-Type' : 'application/x-www-form-urlencoded' },
                    data: $.param(clientsData)
                });
            },

            searchClientByNameOrCpf : function(search) {

				if (search == null)
					search = "";

                return $http({
                    method: 'POST',
                    url: Utils.apiUrl() + 'api/clients/search/nameOrCpf',
                    headers: { 'Content-Type' : 'application/x-www-form-urlencoded' },
                    data: $.param(search)
                });
            },

            getClient : function(clienteId){

                return $http({
                    method: 'GET',
                    url: Utils.apiUrl() + 'api/clients/' + clienteId
                })
            },

            searchBirthdays : function(mes){
                return $http({
                    method: 'POST',
                    url: Utils.apiUrl() + 'api/clients/search/birthdays',
                    headers: { 'Content-Type' : 'application/x-www-form-urlencoded' },
                    data: $.param(mes)
                });
            }

		}

	});