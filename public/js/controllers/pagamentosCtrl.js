/**
 * Created by root on 12/02/17.
 */
liriaApp.controller('pagamentosController', function($rootScope, $scope, $http, Clients, $location, Login, $routeParams) {

    $rootScope.logged = true;
    $scope.logged = true;

    Login.checkLogin()

        .success(function (data) {

            if (data.error) {
                $location.path('/login');
                $rootScope.logged = false;
                $scope.logged = false;
            }
        });

    $scope.treatmentSelected = false;

    Clients.getClient($routeParams.clienteId)

        .success(function(data) {

            if(data.error){

                $rootScope.logged = false;
                $scope.logged = false;
                $location.path('/login');

            }else{
                $scope.cliente = data.result;
            }
        })

        .error(function(data) {
            $scope.errorMessage = true;
        });

    $scope.showPayments = function(treatmentId){

        var treatmentTmp;
        for(index = 0; index < $scope.cliente.treatments.length; ++index){

            treatmentTmp = $scope.cliente.treatments[index];

            if(treatmentId == treatmentTmp.id){
                $scope.treatment = treatmentTmp;
                $scope.treatmentSelected = true;
            }
        }
    }
});