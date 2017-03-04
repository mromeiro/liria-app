liriaApp.factory('Login', function($http) {

	return {

		login : function(loginData) {

			var json = '{"email": "' +  loginData.email + '", "password": "' + loginData.password + '"}';

			return $http({
				method: 'POST',
				dataType: 'json',
				url: 'http://dranathaly.ddns.net:1989/api/login',
				headers: { 'Content-Type' : 'application/json' },
				data: json
			});
		},

		checkLogin : function() {

			return $http({
				method: 'GET',
				url: 'http://dranathaly.ddns.net:1989/api/isLogged',
			});
		}

	}

});