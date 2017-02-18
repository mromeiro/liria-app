/**
 * Created by root on 12/02/17.
 */
liriaApp.controller('pagamentosController', function($rootScope, $scope, $http, Clients, $location, Login, Pagamentos, $routeParams) {

    $rootScope.userName = window.localStorage.getItem('name');
    $rootScope.logged = true;

    Login.checkLogin()

        .success(function (data) {

            if (data.error) {
                $location.path('/login');
                $rootScope.logged = false;
                $scope.logged = false;
            }
        });

    //Hider the table containing the payments for the treatment. The table will be displayed
    //once a treatment is selected
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

    $scope.showPaymentModal = function(payment){

        $('#myModalPaymentDate').modal({
            show: 'true'
        });

        $scope.payment = payment;
    }

    $scope.recordPaymentDate = function(){

        $scope.paymentData.paymentId = $scope.payment.id;

        Pagamentos.updatePaymentDate($scope.paymentData)

            .success(function(data) {

                if(data.error){

                    $('#myModalPaymentDate').modal('hide');
                    $('body').removeClass('modal-open');
                    $('.modal-backdrop').remove();

                    $rootScope.logged = false;
                    $location.path('/login');

                }else{

                    //update payment date
                    $scope.payment.data_pagamento = data.result.data_pagamento;

                }
            });
    }
});