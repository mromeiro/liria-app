liriaApp.factory('Clients', function($http) {

		return {
			get : function() {
				return $http.get('http://dranathaly.ddns.net:1989/api/clients');
			},

			save : function(clientsData) {
				return $http({
					method: 'POST',
					url: 'http://dranathaly.ddns.net:1989/api/clients',
					headers: { 'Content-Type' : 'application/x-www-form-urlencoded' },
					data: $.param(clientsData)
				});
			},

            update : function(clientsData) {
                return $http({
                    method: 'POST',
                    url: 'http://dranathaly.ddns.net:1989/api/clients/update',
                    headers: { 'Content-Type' : 'application/x-www-form-urlencoded' },
                    data: $.param(clientsData)
                });
            },

            searchClientByNameOrCpf : function(search) {

				if (search == null)
					search = "";

                return $http({
                    method: 'POST',
                    url: 'http://dranathaly.ddns.net:1989/api/clients/search/nameOrCpf',
                    headers: { 'Content-Type' : 'application/x-www-form-urlencoded' },
                    data: $.param(search)
                });
            },

            getClient : function(clienteId){

                return $http({
                    method: 'GET',
                    url: 'http://dranathaly.ddns.net:1989/api/clients/' + clienteId
                })
            },

		}

	});