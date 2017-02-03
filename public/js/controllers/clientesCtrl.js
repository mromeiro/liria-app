liriaApp.controller('clientesController', function($rootScope, $scope, $http, Clients, Tratamentos, $location, Upload) {

	$scope.clientData= {};

	$rootScope.logged = true;
	$scope.logged = true;

	$scope.noPhoto = true;
	$scope.uploadedPhoto = 'not_found.png'; //Default image

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
			url: 'http://localhost:8000/api/upload',
			data: {
				file: Upload.dataUrltoBlob(dataUrl, name)
			},
		})

			.success(function(data){
				$scope.uploadedPhoto = data.result;
				$scope.noPhoto = false;
			})
	}

	//Saves new client
	$scope.submitClient = function() {

		$scope.loading = true;

		// save the comment. pass in comment data from the form
		$scope.clientData.photoName = $scope.uploadedPhoto;
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
       		$location.path('/clientes/pagamentos/' + customerId);
       }else if (action == 'novoTratamento')
           $location.path('/clientes/tratamentos/' + customerId);
	}

});