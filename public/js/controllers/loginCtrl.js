liriaApp.controller('loginController', function($rootScope, $scope, $http, Login, $location, Utils) {

	//Hide menu
    $("#page-wrapper").css("margin", "0 0 0 0px");
    $rootScope.logged = false;

    if($location.url().lastIndexOf('logout')>0) {

		Login.logout()
            .success(function (data) {
                window.localStorage.removeItem('token');
                $rootScope.userName = null;
            });
    }

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

					$scope.config = null;
                    Utils.getAppConfig($scope)

                        .success(function(data){

                            window.localStorage.setItem('config',data.result);

                            //Checks if sumup was already authorized for the application
                        	if(data.result.refresh_token == null){

                                window.location = 'https://api.sumup.com/authorize?' +
								'scope=transactions.history' +
								'&response_type=code' +
								'&client_id=' + data.result.client_id +
								'&client_secret=' + data.result.client_secret +
								'&redirect_uri=' + data.result.redirect_uri;

							}
                        })

					//Once the client is connected it subscribes to the publisher for warnings
                    /*var socket = io.connect('http://localhost:8890');

                    socket.on('message', function (data) {
                        console.log(data);

                    });

                    socket.emit('disconnect','teste');
                    alert('teste');*/

				}
			})
			.error(function(data) {
				$scope.errorMessage = true;
			});
	};
});