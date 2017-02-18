liriaApp.controller('tratamentosController', function($scope, $rootScope, $http, Tratamentos, Clients, Login, $location, $routeParams) {

    $rootScope.userName = window.localStorage.getItem('name');
	$rootScope.logged = true;
	$scope.logged = true;

	$scope.loading = true;

	$scope.sortType     = 'data_inicio'; // set the default sort type
	$scope.searchClient   = '';     // set the default search/filter term

	//Check if the user is logged
    Login.checkLogin()

        .success(function (data) {

            if (data.error) {
                $location.path('/login');
            }
        });

	//If the URL contains a client ID then the customer is recovered here
	if($routeParams.clienteId != null){

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

    }

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
				$scope.dataTratamento = data;
			}
		})
		.error(function(data) {
			$scope.errorMessage = true;
		});

	$scope.loading = false;

	//Updates the price field depending on the chosen treatment
	$scope.fillPrice = function() {

		var tratamento_json = JSON.parse($scope.tratamentoData.tratamento);

		var preco = $('#preco');
		preco.val(tratamento_json['preco']);
		preco.trigger('input');

	}

	//Saves the new treatment
	$scope.submitTreatment = function(){

		Tratamentos.submitTreatment($scope.tratamentoData, $routeParams.clienteId)

			.success(function(data) {

				if(data.error){

					$rootScope.logged = false;
					$scope.logged = false;
					$location.path('/login');

				}else{

					//Update Treatments list
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
				}
			})

			.error(function(data) {
				$scope.errorMessage = true;
			});
	}
});