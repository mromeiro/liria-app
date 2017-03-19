liriaApp.factory('Login', function($http, Utils) {

	return {

		login : function(loginData) {

			var json = '{"email": "' +  loginData.email + '", "password": "' + loginData.password + '"}';

			return $http({
				method: 'POST',
				dataType: 'json',
				url: Utils.apiUrl() + 'api/login',
				headers: { 'Content-Type' : 'application/json' },
				data: json
			});
		},

		checkLogin : function() {

			return $http({
				method: 'GET',
				url: Utils.apiUrl() + 'api/isLogged',
			});
		},

        logout : function() {

            return $http({
                method: 'POST',
                url: Utils.apiUrl() + 'api/logout',
            });
        }

	}

});