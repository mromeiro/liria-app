/**
 * Created by root on 12/02/17.
 */
liriaApp.controller('sumupController', function($rootScope, $scope, $http, $location, Login, Relatorio, $routeParams, Sumup) {

    $rootScope.logged = true;

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