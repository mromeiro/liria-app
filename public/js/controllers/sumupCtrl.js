/**
 * Created by root on 12/02/17.
 */
liriaApp.controller('sumupController', function($rootScope, $scope, $http, $location, Login, Relatorio, $routeParams) {

    $rootScope.userName = window.localStorage.getItem('name');
    $rootScope.client_id = window.localStorage.getItem('client_id');
    $rootScope.client_secret = window.localStorage.getItem('client_secret');
    $rootScope.redirect_uri = window.localStorage.getItem('redirect_uri');

    $rootScope.logged = true;
    $scope.relatorioPronto = false;
    $scope.showPaymentsToConfirm = false;
    $scope.showExpensesToConfirm = false;

    Login.checkLogin()

        .success(function (data) {

            if (data.error) {
                $location.path('/login');
                $rootScope.logged = false;
                $scope.logged = false;
            }
        });

    $scope.generateConciliationReport = function(){

        $scope.searchString.sumupCode = $routeParams.code;

        Relatorio.generateConciliationReport($scope.searchString)

            .success(function(data) {

                if(data.error){

                    $rootScope.logged = false;
                    $location.path('/login');

                }else{



                }
            });
    }


});