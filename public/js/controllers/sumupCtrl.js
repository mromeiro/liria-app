/**
 * Created by root on 12/02/17.
 */
liriaApp.controller('sumupController', function($rootScope, $scope, $http, $location, Login, Relatorio, $routeParams, Sumup) {

    $rootScope.userName = window.localStorage.getItem('name');

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

    $scope.authorizeSumup = function(code, config){

        Sumup.authorizeSumup(code, config)
    }

    $scope.generateConciliationReport = function(){

        $scope.searchString.sumupCode = $routeParams.code;

        Relatorio.generateConciliationReport($scope.searchString)

            .success(function(data) {

                if(data.error){

                    $rootScope.logged = false;
                    $location.path('/login');

                }else{

                    $scope.conciliationList = data.result;

                }
            });
    }


});