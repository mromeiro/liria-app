/**
 * Created by root on 12/02/17.
 */
liriaApp.controller('pagamentosController', function($rootScope, $scope, $http, Clients, $location, Login, Pagamentos, $routeParams, Tratamentos) {

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

    //Recover the list of available treatments
    Tratamentos.get()

        .success(function(data) {

            if(data.error){

                $rootScope.logged = false;
                $scope.logged = false;
                $location.path('/login');

            }else{
                $rootScope.logged = true;
                $scope.logged = true;
                $scope.dadosPagamento = data;
            }
        })
        .error(function(data) {
            $scope.errorMessage = true;
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

    $scope.renderTreatment = function(tratamento){
        return tratamento.nome;
    }

    $scope.setPrice = function(tratamento){

        var preco = $('#valor_bruto');
        preco.val(tratamento.preco);
        preco.trigger('input');

    }

    $scope.showPayments = function(payment){

        $scope.selectedPayment = payment;

        $('#myModalPaymentUpdate').modal({
            show: 'true'
        });
    }

    $scope.removePayments = function(payment){

        Pagamentos.removePayments(payment)

            .success(function(data){

                if(data.error){

                    $('#myModalPaymentUpdate').modal('hide');
                    $('body').removeClass('modal-open');
                    $('.modal-backdrop').remove();

                    $rootScope.logged = false;
                    $location.path('/login');

                }else{

                    $('#myModalPaymentUpdate').modal('hide');
                    $('body').removeClass('modal-open');
                    $('.modal-backdrop').remove();

                    for(var i = 0; i < $scope.payments.length; i++){

                        if($scope.payments[i].id = $scope.selectedPayment.id)
                            $scope.payments.splice(i,1);
                    }

                    $scope.selectedPayment = null;
                }

            });
    }

    //Change payment data for forecast payments
    $scope.updatePayments = function(payment){

        Pagamentos.updatePayments(payment)

            .success(function(data) {

                if(data.error){

                    $('#myModalPaymentUpdate').modal('hide');
                    $('body').removeClass('modal-open');
                    $('.modal-backdrop').remove();

                    $rootScope.logged = false;
                    $location.path('/login');

                }else{

                    //updates the payment in the model and the original table behind
                    $scope.selectedPayment.cliente = data.result.cliente;
                    $scope.selectedPayment.data_pagamento_confirmado = data.result.data_pagamento_confirmado;
                    $scope.selectedPayment.data_pagamento_efetuado = data.result.data_pagamento_efetuado;
                    $scope.selectedPayment.data_prevista = data.result.data_prevista;
                    $scope.selectedPayment.descricao = data.result.descricao;
                    $scope.selectedPayment.forma_pagamento = data.result.forma_pagamento;
                    $scope.selectedPayment.id = data.result.id;
                    $scope.selectedPayment.nro_parcela = data.result.nro_parcela;
                    $scope.selectedPayment.nro_parcelas = data.result.nro_parcelas;
                    $scope.selectedPayment.taxa_cartao_utilizada = data.result.taxa_cartao_utilizada;
                    $scope.selectedPayment.valor_bruto = data.result.valor_bruto;
                    $scope.selectedPayment.valor_depois_taxa = data.result.valor_depois_taxa;
                    $scope.selectedPayment.valor_parcela = data.result.valor_parcela;
                    $scope.selectedPayment.remainingPayments = data.result.remainingPayments;
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

    $scope.searchPaymentsForecast = function(){

        Pagamentos.searchPaymentsForecast($scope.searchString)

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

    $scope.submitPayments = function(){

        Pagamentos.submitPayments($scope.dadosPagamento)

            .success(function(data) {

                if(data.error){

                    $rootScope.logged = false;
                    $location.path('/login');

                }else{

                    $scope.paymentsList = data.result;
                    $scope.paymentsFound = true;
                }
            });
    }

    //Confirms the date of a payment
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


});