liriaApp.controller('despesasController', function($rootScope, $scope, $http, $location, Upload, Login, Expenses) {

    $rootScope.logged = true;
    $scope.logged = true;
    $scope.expenseData = {};
    $scope.expenseDataList = {};
    $rootScope.userName = window.localStorage.getItem('name');

    Login.checkLogin()

        .success(function (data) {

            if (data.error) {
                $location.path('/login');
                $rootScope.logged = false;
                $scope.logged = false;
            }
        });

    Expenses.getMonthlyExpenses()

        .success(function(data) {
            $scope.expenseDataList = data.result;
        });

	$scope.submitMonthlylExpense = function(file){

	    $scope.expenseData.usuario = $rootScope.userName;

        Expenses.submitMonthlylExpense($scope.expenseData)

            .success(function(data){

                $scope.expense = data.result;
                $scope.expenseDataList.push($scope.expense);

                /*Expenses.submitExpenseReceipt(file, data.result.id)

                    .success(function(data){
                        $scope.expense.recibo = data.result.recibo;
                    });*/
            });
	}

});