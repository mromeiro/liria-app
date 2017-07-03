/**
 * Created by root on 12/02/17.
 */
liriaApp.controller('relatorioController', function($rootScope, $scope, $http, $location, Login, Relatorio) {

    $rootScope.userName = window.localStorage.getItem('name');
    $rootScope.logged = true;
    $scope.relatorioPronto = false;

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

                    $scope.totalWithPaymentsToConfirm = parseFloat($scope.report.totalConfirmedPayments) + parseFloat($scope.report.totalPaymentsToConfirm)
                                                            - parseFloat($scope.report.totalExpenses);

                    $scope.totalForecast = parseFloat($scope.report.totalConfirmedPayments) + parseFloat($scope.report.totalPaymentsToConfirm)
                        + parseFloat($scope.report.totalTreatmentsNotConfirmed) - parseFloat($scope.report.totalExpenses);

                    $scope.totalWithoutPaymentsToConfirm = parseFloat($scope.report.totalConfirmedPayments).toFixed(2) - parseFloat($scope.report.totalExpenses).toFixed(2);
                }
            });
    }


});