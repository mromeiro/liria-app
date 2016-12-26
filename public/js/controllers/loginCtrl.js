liriaApp.controller('loginController', function($scope, $http, Login, $location) {

	// function to handle submitting the form
	$scope.submitLogin = function() {

		Login.login($scope.loginData)

			.success(function(data) {

				if(data.result == "wrong email or password."){
					$scope.errorMessage = true;
				}else{
					window.localStorage.setItem('token',data.result);
					$location.path('/home');
				}
			})
			.error(function(data) {
				$scope.errorMessage = true;
			});
	};
});