liriaApp.controller('despesasController', function($rootScope, $scope, $http, $location, Upload, Login, Expenses) {

    $rootScope.userName = window.localStorage.getItem('name');

    $rootScope.logged = true;
    $scope.logged = true;
    $scope.expenseData = {};
    $scope.expenses = [];
    $scope.photo = null;
    $scope.expenseDataList = [];

    $scope.recibo = 'Recibo';
    $scope.expenseData.previsao = false;
    $scope.expensesFound = false;

    //Button to upload a file
    ;( function ( document, window, index )
    {
        var inputs = document.querySelectorAll( '.inputfile' );
        Array.prototype.forEach.call( inputs, function( input )
        {
            var label	 = input.nextElementSibling,
                labelVal = label.innerHTML;

            input.addEventListener( 'change', function( e )
            {
                var fileName = '';
                if( this.files && this.files.length > 1 )
                    fileName = ( this.getAttribute( 'data-multiple-caption' ) || '' ).replace( '{count}', this.files.length );
                else
                    fileName = e.target.value.split( '\\' ).pop();
                if( fileName )
                    label.querySelector( 'span' ).innerHTML = fileName;
                else
                    label.innerHTML = labelVal;
            });

            // Firefox bug fix
            input.addEventListener( 'focus', function(){ input.classList.add( 'has-focus' ); });
            input.addEventListener( 'blur', function(){ input.classList.remove( 'has-focus' ); });
        });
    }( document, window, 0 ));

    Login.checkLogin()

        .success(function (data) {

            if (data.error) {
                $location.path('/login');
                $rootScope.logged = false;
                $scope.logged = false;
            }
        });

    Date.prototype.ddmmyyyy = function() {
        var mm = this.getMonth() + 1; // getMonth() is zero-based
        var dd = this.getDate();

        return [(dd>9 ? '' : '0') + dd,
            (mm>9 ? '' : '0') + mm,
            this.getFullYear()
         ].join('/');
    };

    var date = new Date();
    $scope.expenseData.date = date.ddmmyyyy();

    //Records expenses in the database
	$scope.checkClosedInvoice = function(){

	    if($scope.expenseData.metodo_pagamento == 'Crédito'){
            $('#myModalFaturaFechada').modal({
                show: 'true'
            });
        }else{
            $scope.submitExpense('false');
        }

	}

    $scope.checkClosedInvoiceToUpdate = function(){

        if($scope.selectedExpense.metodo_pagamento == 'Crédito'){
            $('#myModalFaturaFechada').modal({
                show: 'true'
            });
        }else{
            $scope.updateExpense('false');
        }

    }

    $scope.submitExpense = function(fatura_fechada){

        var expense;
        $scope.expenseData.usuario = $rootScope.userName;

        $scope.expenseData.fatura_fechada = fatura_fechada;

        if($scope.photo == null){

            Expenses.submitExpense($scope.expenseData)

                .success(function(data){

                    $scope.expensePayments = data.result;

                    for(i = 0; i < $scope.expensePayments.length ; i++){
                        $scope.expenseDataList.push($scope.expensePayments[i]);
                    }

                });

        }else{

            Expenses.submitExpenseReceipt($scope.photo.picFile)

                .success(function(data){

                    if (data.error) {
                        $location.path('/login');
                        $rootScope.logged = false;
                        $scope.logged = false;
                    }

                    $scope.expenseData.recibo = data.result;
                    Expenses.submitExpense($scope.expenseData)

                        .success(function(data){

                            $scope.expensePayments = data.result;

                            for(i = 0; i < $scope.expensePayments.length ; i++){
                                $scope.expenseDataList.push($scope.expensePayments[i]);
                            }

                        });

                });
        }



    }

    $scope.redirectToPage = function (page){
        $location.path(page);
    }

    $scope.searchExpenses = function(){

        Expenses.getExpenses($scope.searchString)

            .success(function(data){

                $scope.expenseDataList = data.result;
            });
    }

    $scope.searchExpenseForecast = function(){

        $scope.expenses = [];

        Expenses.searchExpenseForecast($scope.searchString)

            .success(function(data) {

                if(data.error){

                    $rootScope.logged = false;
                    $location.path('/login');

                }else{

                    for(i = 0; i < data.result.length ; i++){

                        if(data.result[i].previsao == 'false')
                            data.result[i].previsao = false;
                        else if(data.result[i].previsao == 'true')
                            data.result[i].previsao = true;

                        $scope.expenses.push(data.result[i]);
                    }

                    $scope.expensesFound = true;
                }
            });
    }

    $scope.showExpenses = function(expense){

        $scope.selectedExpense = expense;

        if($scope.selectedExpense.previsao == 'false')
            $scope.selectedExpense.previsao = false
        else if($scope.selectedExpense.previsao == 'true')
            $scope.selectedExpense.previsao = true;

        $('#myModalExpenseUpdate').modal({
            show: 'true'
        });
    }

    $scope.updateExpense = function(fatura_fechada) {

        var expense;
        $scope.selectedExpense.usuario = $rootScope.userName;

        $scope.selectedExpense.fatura_fechada = fatura_fechada;

        if ($scope.photo == null) {

            Expenses.updateExpense($scope.selectedExpense)

                .success(function (data) {

                    if (data.error) {

                        $('#myModalExpenseUpdate').modal('hide');
                        $('body').removeClass('modal-open');
                        $('.modal-backdrop').remove();

                        $rootScope.logged = false;
                        $location.path('/login');

                    } else {

                        $scope.selectedExpense.data_despesa = data.result.data_despesa;
                        $scope.selectedExpense.data_parcela = data.result.data_parcela;
                        $scope.selectedExpense.descricao = data.result.descricao;
                        $scope.selectedExpense.id = data.result.id;
                        $scope.selectedExpense.metodo_pagamento = data.result.metodo_pagamento;
                        $scope.selectedExpense.parcela = data.result.parcela;
                        $scope.selectedExpense.remainingExpenses = data.result.remainingExpenses;
                        $scope.selectedExpense.tipo = data.result.tipo;
                        $scope.selectedExpense.total_parcelas = data.result.total_parcelas;
                        $scope.selectedExpense.valor_parcela = data.result.valor_parcela;
                        $scope.selectedExpense.valor_total = data.result.valor_total;

                        if(data.result.previsao == 'false')
                            $scope.selectedExpense.previsao = false
                        else if(data.result.previsao == 'true')
                            $scope.selectedExpense.previsao = true;

                    }
                });

        } else {

            Expenses.submitExpenseReceipt($scope.photo.picFile)

                .success(function (data) {

                    if (data.error) {
                        $location.path('/login');
                        $rootScope.logged = false;
                        $scope.logged = false;
                    }

                    $scope.selectedExpense.recibo = data.result;
                    Expenses.updateExpense($scope.selectedExpense)

                        .success(function (data) {

                            if (data.error) {

                                $('#myModalExpenseUpdate').modal('hide');
                                $('body').removeClass('modal-open');
                                $('.modal-backdrop').remove();

                                $rootScope.logged = false;
                                $location.path('/login');

                            } else {

                                $scope.selectedExpense.data_despesa = data.result.data_despesa;
                                $scope.selectedExpense.data_parcela = data.result.data_parcela;
                                $scope.selectedExpense.descricao = data.result.descricao;
                                $scope.selectedExpense.id = data.result.id;
                                $scope.selectedExpense.metodo_pagamento = data.result.metodo_pagamento;
                                $scope.selectedExpense.parcela = data.result.parcela;
                                $scope.selectedExpense.remainingExpenses = data.result.remainingExpenses;
                                $scope.selectedExpense.tipo = data.result.tipo;
                                $scope.selectedExpense.total_parcelas = data.result.total_parcelas;
                                $scope.selectedExpense.valor_parcela = data.result.valor_parcela;
                                $scope.selectedExpense.valor_total = data.result.valor_total;

                                if(data.result.previsao == 'false')
                                    $scope.selectedExpense.previsao = false
                                else if(data.result.previsao == 'true')
                                    $scope.selectedExpense.previsao = true;
                            }
                        });

                });
        }

    }

});

