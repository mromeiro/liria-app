/**
 * Created by root on 12/02/17.
 */
liriaApp.controller('relatorioController', function($rootScope, $scope, $http, $location, Login, Relatorio) {

    $rootScope.userName = window.localStorage.getItem('name');
    $rootScope.client_id = window.localStorage.getItem('client_id');
    $rootScope.client_secret = window.localStorage.getItem('client_secret');
    $rootScope.redirect_uri = window.localStorage.getItem('redirect_uri');
    $rootScope.logged = true;
    $scope.relatorioPronto = false;
    $scope.showPaymentsToConfirm = false;
    $scope.showExpensesToConfirm = false;
    $scope.showPaymentsForcast = false;

    Login.checkLogin()

        .success(function (data) {

            if (data.error) {
                $location.path('/login');
                $rootScope.logged = false;
                $scope.logged = false;
            }
        });


    $scope.generateExpenseReport = function(){

        Relatorio.generateExpenseReport($scope.searchString)

            .success(function(data) {

                if(data.error){

                    $rootScope.logged = false;
                    $location.path('/login');

                }else{

                    $scope.report = data.result;
                    $scope.relatorioPronto = true;
                    $scope.tab = "Entrada Confirmada";

                    $scope.totalValue = parseFloat($scope.report.totalConfirmedPayments)
                                                            - parseFloat($scope.report.totalExpenses);
                }
            });
    }

    $scope.updateTotalValue = function(){

        $scope.totalValue = parseFloat($scope.report.totalConfirmedPayments)
            - parseFloat($scope.report.totalExpenses);

        if($scope.showPaymentsToConfirm){
            $scope.totalValue += parseFloat($scope.report.totalPaymentsToConfirm);
        }
        if($scope.showPaymentsForcast){
            $scope.totalValue += parseFloat($scope.report.totalForcastPayments);
        }
        if($scope.showExpensesToConfirm){
            $scope.totalValue -= parseFloat($scope.report.totalExpensesWaitingToConfirm);
        }

    }


});