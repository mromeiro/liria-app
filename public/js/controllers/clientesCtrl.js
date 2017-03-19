liriaApp.controller('clientesController', function($rootScope, $scope, $http, Clients, $location, Upload, Login, Utils, $routeParams) {

    //$wamp.open();
    $rootScope.userName = window.localStorage.getItem('name');
    $scope.clientData = {};

    $rootScope.logged = true;
    $scope.logged = true;

    $scope.noPhoto = true;
    $scope.uploadedPhoto = 'not_found.png'; //Default image

    Login.checkLogin()

        .success(function (data) {

            if (data.error) {
                $location.path('/login');
            }
        });


 	//Client is being updated
	if($location.url().lastIndexOf('clientes/alterar/')>0){

        Clients.getClient($routeParams.clienteId)

			.success(function(data) {

					if(data.error){

						$rootScope.logged = false;
						$scope.logged = false;
						$location.path('/login');

					}else{
						$scope.clientData = data.result;
                        $scope.clientData.photoName = $scope.clientData.foto;
                        $scope.uploadedPhoto = $scope.clientData.foto;
					}
				})

			.error(function(data) {
				$scope.errorMessage = true;
			});
	}

	//When a photo is selected this modal will open for the image to be cropped
	$scope.$watch('uploadFiles', function () {

		if ($scope.uploadFiles != null){
			$('#myModalPhoto').modal({
				show: 'true'
			});
		}
	});

	//Searchs the client for the busca_clientes.html page
	$scope.searchClientByNameOrCpf = function(){

        $scope.loading = true;

		Clients.searchClientByNameOrCpf($scope.search)

			.success(function(data){

				$scope.clientList = data;
			})

        $scope.loading = false;
	}

	//Upload an image for the new client
	$scope.uploadImage = function (dataUrl, name){

		Upload.upload({
			url: Utils.apiUrl() + 'api/upload',
			data: {
				file: Upload.dataUrltoBlob(dataUrl, name)
			},
		})

			.success(function(data){
				$scope.uploadedPhoto = data.result;
                $scope.uploadedPhoto = data.result;
				$scope.noPhoto = false;
			})
	}

	//Saves new client
	$scope.submitClient = function() {

		$scope.loading = true;

		$scope.clientData.photoName = $scope.uploadedPhoto;
		$scope.clientData.usuario = window.localStorage.getItem('name')
		Clients.save($scope.clientData)

			.success(function(data) {

				if(data.error){
					$rootScope.logged = false;
					$scope.logged = true;
					$location.path('/login');
				}else{

					$scope.clientData = {};
					$('#myModalSucesso').modal({
						show: 'true'
					});

					$scope.clienteCadastrado = true;
					$rootScope.clienteCadastrado = true;

					//Return the client so that we can redirect the use to create their treatments
					$scope.novoCliente = data.result;

				}
			})
			.error(function(data) {
				$('#myModalErro').modal({
					show: 'true'
				});
				console.log(data);
			});
	};

    //Saves new client
    $scope.updateClient = function() {

        $scope.loading = true;

        $scope.clientData.photoName = $scope.uploadedPhoto;
        $scope.clientData.usuario = window.localStorage.getItem('name');

        Clients.update($scope.clientData)

            .success(function(data) {

                if(data.error){
                    $rootScope.logged = false;
                    $scope.logged = true;
                    $location.path('/login');
                }else{

                    $scope.clientData = {};
                    $('#myModalSucesso').modal({
                        show: 'true'
                    });

                    $scope.clienteCadastrado = true;
                    $rootScope.clienteCadastrado = true;

                    //Return the client so that we can redirect the use to create their treatments
                    $scope.clientData = data.result;

                }
            })
            .error(function(data) {
                $('#myModalErro').modal({
                    show: 'true'
                });
                console.log(data);
            });
    };

	//This functions is called after a client is created if the used wishes to create treatments for the client afterwards.
	$scope.redirectToTreatment = function() {

		$('#myModalSucesso').modal('hide');
		$('body').removeClass('modal-open');
		$('.modal-backdrop').remove();
		$location.path('/clientes/tratamentos/' + $scope.novoCliente.id);

	};

	//This functions is called in all search pages. It defines where to go after the client is selected
	$scope.redirectSearchPage = function(customerId){

		var action = $location.search().action;

       if(action == 'registrarPagamento'){
       		$location.path('/financeiro/pagamentos/' + customerId);
       }else if (action == 'novoTratamento'){
           $location.path('/clientes/tratamentos/' + customerId);
       }else if (action == 'alterarCliente'){
           $location.path('/clientes/alterar/' + customerId);
	   }else if (action == 'incluirFormaPagamento'){
       		$location.path('/clientes/tratamentos/incluirFormaPagamento/' + customerId)
	   }
	}

});