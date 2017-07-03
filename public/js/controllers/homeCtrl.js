liriaApp.controller('homeController', function($rootScope, $scope, $http, Clients, $location, Upload, Login, Utils, $routeParams) {

    //Show menu
    $("#page-wrapper").css("margin", "0 0 0 250px");

    $scope.tab = 1;

    $rootScope.userName = window.localStorage.getItem('name');

    $rootScope.logged = true;
    $scope.logged = true;

    Login.checkLogin()

        .success(function (data) {

            if (data.error) {
                $location.path('/login');
            }
        });

    var date = new Date();
    $scope.searchString = new Object();
    $scope.searchString.mes = date.getMonth()+1;
	Clients.searchBirthdays($scope.searchString)

		.success(function(data){
			$scope.listaAniversariantes = data.result;
		})
});