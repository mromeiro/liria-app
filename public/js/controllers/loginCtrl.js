liriaApp.controller('loginController', function($rootScope, $scope, $http, Login, $location) {

	//Hide menu
    $("#page-wrapper").css("margin", "0 0 0 0px");
    $rootScope.logged = false;
	// function to handle submitting the form
	$scope.submitLogin = function() {

		Login.login($scope.loginData)

			.success(function(data) {

				if(data.result == "wrong email or password."){
					$scope.errorMessage = true;
				}else{

					$rootScope.userName = data.result.name;
					window.localStorage.setItem('token',data.result.token);
                    window.localStorage.setItem('name',data.result.name);
                    window.localStorage.setItem('role',data.result.role);
					$location.path('/home');
				}
			})
			.error(function(data) {
				$scope.errorMessage = true;
			});
	};
});