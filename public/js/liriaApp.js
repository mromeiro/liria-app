// create the module and name it scotchApp
var liriaApp = angular.module('liriaApp', ['ngRoute','ngFileUpload','ngImgCrop']);

// configure our routes
liriaApp

    .config(function($routeProvider) {
    $routeProvider

    // route for the home page
        .when('/home', {
            templateUrl : 'pages/home.html',
            controller  : 'mainController'
        })
        .when('/login', {
            templateUrl : 'pages/login.html',
            controller  : 'loginController'
        })
        .when('/clientes/novo', {
            templateUrl : 'pages/cadastro_cliente.html',
            controller  : 'clientesController'
        })
        .when('/clientes/tratamentos/:clienteId', {
            templateUrl : 'pages/cadastro_tratamentos_cliente.html',
            controller  : 'tratamentosController'
        })
        .when('/clientes/busca', {
            templateUrl : 'pages/busca_clientes.html',
            controller  : 'clientesController'
        })
        .when('/clientes/alterar/:clienteId', {
            templateUrl : 'pages/alteracao_cliente.html',
            controller  : 'clientesController'
        })
    })

    .config(['$httpProvider', function($httpProvider) {
        $httpProvider.interceptors.push('tokenInterceptor');
    }]);

// create the controller and inject Angular's $scope
liriaApp.controller('mainController', function($rootScope, $scope, $location) {


   if($location.path() == "/login"){
       $rootScope.logged = false;
   }else{
       $rootScope.logged = true;
   }

});

liriaApp.controller('homeController', function($scope) {
    $scope.logged = true;
});

liriaApp.factory('tokenInterceptor',['$q', function ($q, $location, $http) {

    return {

        'request': function (config) {

            config.headers = config.headers || {};

            if (window.localStorage.getItem('token')) {
                config.headers['token'] = window.localStorage.getItem('token');
            }

            return config;
        },


        'responseError': function (response) {
            if (response.status === 401 || response.status === 403) {

            }
            return $q.reject(response);
        }
    }
}])



