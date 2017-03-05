/**
 * Created by root on 12/02/17.
 */
liriaApp.controller('pagamentosController', function($rootScope, $scope, $http, Clients, $location, Login, Pagamentos, $routeParams) {

    $rootScope.userName = window.localStorage.getItem('name');
    $rootScope.logged = true;
    $scope.paymentsFound = false;

    Login.checkLogin()

        .success(function (data) {

            if (data.error) {
                $location.path('/login');
                $rootScope.logged = false;
                $scope.logged = false;
            }
        });

    //If the URL contains a client ID then the customer is recovered here
    if($routeParams.clienteId != null) {

        Clients.getClient($routeParams.clienteId)

            .success(function (data) {

                if (data.error) {

                    $rootScope.logged = false;
                    $scope.logged = false;
                    $location.path('/login');

                } else {
                    $scope.cliente = data.result;
                }
            });
    }

    $scope.showPayments = function(treatmentId){

        var treatmentTmp;
        for(index = 0; index < $scope.cliente.treatments.length; ++index){

            treatmentTmp = $scope.cliente.treatments[index];

            if(treatmentId == treatmentTmp.id){
                $scope.treatment = treatmentTmp;
                $scope.treatmentSelected = true;
            }
        }

        $('#myModalPaymentDate').modal({
            show: 'true'
        });
    }

    $scope.recordPaymentDate = function(payment){

        Pagamentos.updatePaymentDate(payment)

            .success(function(data) {

                if(data.error){

                    $('#myModalPaymentDate').modal('hide');
                    $('body').removeClass('modal-open');
                    $('.modal-backdrop').remove();

                    $rootScope.logged = false;
                    $location.path('/login');

                }else{

                    //update payment date
                    payment.data_pagamento = data.result.data_pagamento;
                    payment.pago = data.result.pago;

                }
            });
    }

    $scope.searchPayments = function(){

        Pagamentos.searchPayments($scope.searchString)

            .success(function(data) {

                if(data.error){

                    $rootScope.logged = false;
                    $location.path('/login');

                }else{

                    $scope.payments = data.result;
                    $scope.paymentsFound = true;
                }
            });
    }
});