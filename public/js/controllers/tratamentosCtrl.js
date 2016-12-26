liriaApp.controller('tratamentosController', function($scope, $rootScope, $http, Tratamentos, $location, $routeParams) {

	$rootScope.logged = true;
	$scope.logged = true;

	$scope.loading = true;

	$scope.sortType     = 'data_inicio'; // set the default sort type
	$scope.searchClient   = '';     // set the default search/filter term


	Tratamentos.getClient($routeParams.clienteId)

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

	$scope.fillPrice = function() {

		var tratamento_json = JSON.parse($scope.tratamentoData.tratamento);

		var preco = $('#preco');
		preco.val(tratamento_json['preco']);
		preco.trigger('input');

	}

	$scope.submitTreatment = function(){

		Tratamentos.submitTreatment($scope.tratamentoData, $routeParams.clienteId)

			.success(function(data) {

				if(data.error){

					$rootScope.logged = false;
					$scope.logged = false;
					$location.path('/login');

				}else{

					//Update Treatments list
					Tratamentos.getClient($routeParams.clienteId)

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