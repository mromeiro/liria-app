liriaApp.controller('tratamentosController', function($scope, $rootScope, $http, Tratamentos, Clients, Login, $location, Utils, $routeParams) {

    $rootScope.userName = window.localStorage.getItem('name');
	$rootScope.logged = true;
	$scope.logged = true;


	$scope.loading = true;

	$scope.sortType  = 'data_inicio'; // set the default sort type
	$scope.searchClient = '';     // set the default search/filter term

	$scope.showTreatmentDiv = false;

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

	/*Updates the price field depending on the chosen treatment
	$scope.fillPrice = function() {

		var tratamento_json = JSON.parse($scope.tratamentoData.tratamento);

        var preco = $('#preco');
        preco.val(tratamento_json['preco']);
        preco.trigger('input');

        $('#nro_parcelas').disabled = false;
        $('#nro_sessoes').disabled = false;

	}*/

	//Saves the new treatment
	$scope.submitTreatment = function(){


        $scope.tratamentoData.usuario = window.localStorage.getItem('name');
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

    $scope.showPaymentData = function(treatment){

		$scope.treatmentSelected = treatment;
        $scope.selectedTreatmentToUpdate = angular.copy(treatment);
        $('#myModalUpdateTreatment').modal({
            show: 'true'
        });
    }

    $scope.updateTreatment = function(){

        $scope.selectedTreatmentToUpdate.usuario = window.localStorage.getItem('name');
        Tratamentos.updateTreatment($scope.selectedTreatmentToUpdate)

            .success(function(data) {

                if(data.error){

                    $rootScope.logged = false;
                    $scope.logged = false;
                    $location.path('/login');

                }else{

                	//Treatment updated in the treatment table
                    $scope.treatmentSelected.data_inicio = data.result.data_inicio;
                    $scope.treatmentSelected.preco = data.result.preco;
                    $scope.treatmentSelected.desconto = data.result.desconto;
                    $scope.treatmentSelected.forma_pagamento = data.result.forma_pagamento;
                    $scope.treatmentSelected.nro_parcelas = data.result.nro_parcelas;
                    $scope.treatmentSelected.nro_sessoes = data.result.nro_sessoes;
                }
            })

            .error(function(data) {
                $scope.errorMessage = true;
            });
    }

    //Displays the first payment date if the payments method is Cheque or Dinheiro
    $scope.evaluateFirstPaymentDate = function(){

    	if($scope.tratamentoData.forma_pagamento == 'Cheque' || $scope.tratamentoData.forma_pagamento == 'Dinheiro')
            $scope.showFirstPaymentDate = true;
		else
            $scope.showFirstPaymentDate = false;
	}

	$scope.renderTreatment = function(tratamento){
    	return tratamento.nome;
	}

	$scope.setPrice = function(tratamento){

        var preco = $('#preco');
        preco.val(tratamento.preco);
        preco.trigger('input');

	}

});