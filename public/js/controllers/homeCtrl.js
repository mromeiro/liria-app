liriaApp.controller('homeController', function($rootScope, $scope, $http, Clients, $location, Upload, Login, Utils, Sumup, $routeParams) {

    //Show menu
    //$("#page-wrapper").css("margin", "0 0 0 250px");

    $scope.tab = 1;
    $scope.birthdaysFound = false;

    $rootScope.userName = window.localStorage.getItem('name');

    $rootScope.logged = true;
    $scope.logged = true;

    var dateObj = new Date();
    $scope.day = dateObj.getUTCDate();

    Login.checkLogin()

        .success(function (data) {

            if (data.error) {
                $location.path('/login');
            }
        });

    if ($routeParams.code != null) {
        Sumup.authorizeSumup($routeParams.code, window.localStorage.getItem('config'))
        $location.url($location.path());
    }

    var date = new Date();
    $scope.searchString = new Object();
    $scope.searchString.mes = date.getMonth()+1;
	Clients.searchBirthdays($scope.searchString)

		.success(function(data){
			$scope.listaAniversariantes = data.result;
			$scope.birthdaysFound = true;
		})

});