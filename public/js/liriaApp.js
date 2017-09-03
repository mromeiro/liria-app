// create the module and name it scotchApp
var liriaApp = angular.module('liriaApp', ['ngRoute','ngFileUpload','ngImgCrop','ui.mask','autocomplete'/*,'vxWamp'*/]);

// configure our routes
liriaApp

    .config(function($routeProvider) {
        $routeProvider

        // route for the home page
            .when('/', {
                templateUrl : 'pages/cadastro_cliente.html',
                controller  : 'clientesController'
            })
            .when('/home', {
                templateUrl : 'pages/home.html',
                controller  : 'homeController'
            })
            .when('/login', {
                templateUrl : 'pages/login.html',
                controller  : 'loginController'
            })
            .when('/logout', {
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
            .when('/financeiro/pagamentos/mes', {
                templateUrl : 'pages/confirmar_pagamento_mes.html',
                controller  : 'pagamentosController'
            })
            .when('/clientes/tratamentos/incluirFormaPagamento/:clienteId', {
                templateUrl : 'pages/incluir_forma_pagamento.html',
                controller  : 'tratamentosController'
            })
            .when('/financeiro/cadastro/despesas/mes', {
                templateUrl : 'pages/cadastro_despesas_recorrentes.html',
                controller  : 'despesasMensaisController'
            })
            .when('/financeiro/despesas', {
                templateUrl : 'pages/despesas_mensais.html',
                controller  : 'despesasController'
            })
            .when('/financeiro/despesas/consulta', {
                templateUrl : 'pages/consultar_despesas_mensais.html',
                controller  : 'despesasController'
            })
            .when('/calendar', {
                templateUrl : 'pages/calendar.html',
                controller  : 'calendarController'
            })
            .when('/relatorios/fluxo', {
                templateUrl : 'pages/relatorio_fluxo_de_caixa.html',
                controller  : 'relatorioController'
            })
            .when('/financeiro/pagamentos/registrar', {
                templateUrl : 'pages/registrar_pagamento.html',
                controller  : 'pagamentosController'
            })
            .when('/financeiro/pagamentos/previsao/atualizar', {
                templateUrl : 'pages/atualizar_previsao_pagamentos.html',
                controller  : 'pagamentosController'
            })
            .when('/financeiro/despesas/previsao/atualizar', {
                templateUrl : 'pages/atualizar_previsao_despesas.html',
                controller  : 'despesasController'
            })
    })

    .config(['$httpProvider', function($httpProvider) {
        $httpProvider.interceptors.push('tokenInterceptor');
    }])


// create the controller and inject Angular's $scope
liriaApp.controller('mainController', function($rootScope, $scope, $location) {

    //Show menu
    $("#page-wrapper").css("margin", "0 0 0 0px");
    alert('test');
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



