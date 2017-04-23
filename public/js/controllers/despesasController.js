liriaApp.controller('despesasController', function($rootScope, $scope, $http, $location, Upload, Login, Expenses) {

    $rootScope.logged = true;
    $scope.logged = true;
    $scope.expenseData = {};
    $scope.expenseDataList = {};
    $rootScope.userName = window.localStorage.getItem('name');
    $scope.recibo = 'Recibo';

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

    Expenses.getExpenses($scope.expenseData)

        .success(function(data) {
            $scope.expenseDataList = data.result;
        });

    //Records expenses in the database
	$scope.submitExpense = function(file){

	    $scope.expenseData.usuario = $rootScope.userName;

        Expenses.submitExpense($scope.expenseData)

            .success(function(data){

                $scope.expense = data.result;
                $scope.expenseDataList.push($scope.expense);

                Expenses.submitExpenseReceipt(file, data.result.id)

                    .success(function(data){
                        $scope.expense.recibo = data.result.recibo;
                    });
            });
	}

});