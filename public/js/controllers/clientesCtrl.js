liriaApp.controller('clientesController', function($rootScope, $scope, $http, Clients, Tratamentos, $location, Upload) {

	$scope.clientData= {};

	$rootScope.logged = true;
	$scope.logged = true;
	// function to handle submitting the form

	$scope.noPhoto = true;
	$scope.uploadedPhoto = 'not_found.png';

	//When a photo is selected this moal will open for the image to be cropped
	$scope.$watch('uploadFiles', function () {

		if ($scope.uploadFiles != null){
			$('#myModalPhoto').modal({
				show: 'true'
			});
		}
	});

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

	$scope.redirectToTreatment = function() {

		$('#myModalSucesso').modal('hide');
		$('body').removeClass('modal-open');
		$('.modal-backdrop').remove();
		$location.path('/clientes/tratamentos/' + $scope.novoCliente.id);

	}
});