liriaApp.factory('Login', function($http) {

	return {

		login : function(loginData) {

			var json = '{"email": "' +  loginData.email + '", "password": "' + loginData.password + '"}';

			return $http({
				method: 'POST',
				dataType: 'json',
				url: 'http://localhost:80/api/login',
				headers: { 'Content-Type' : 'application/json' },
				data: json
			});
		},

		home : function(token) {

			return $http({
				method: 'GET',
				url: 'http://localhost:80/api/isLogged',
			});
		}

	}

});