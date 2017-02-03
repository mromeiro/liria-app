liriaApp.factory('Clients', function($http) {

		return {
			get : function() {
				return $http.get('http://localhost:8000/api/clients');
			},

			save : function(clientsData) {
				return $http({
					method: 'POST',
					url: 'http://localhost:8000/api/clients',
					headers: { 'Content-Type' : 'application/x-www-form-urlencoded' },
					data: $.param(clientsData)
				});
			},

            searchClientByNameOrCpf : function(search) {

				if (search == null)
					search = "";

                return $http({
                    method: 'POST',
                    url: 'http://localhost:8000/api/clients/search/nameOrCpf',
                    headers: { 'Content-Type' : 'application/x-www-form-urlencoded' },
                    data: $.param(search)
                });
            }

		}

	});