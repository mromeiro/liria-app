angular.module('loginApp', ['loginCtrl', 'loginService'])

    .factory('tokenInterceptor',['$q', function ($q) {
        return {
            'request': function (config) {

                config.headers = config.headers || {};

                if (window.localStorage.getItem('token')) {
                    config.headers['token'] = window.localStorage.getItem('token');

                }else{

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

    .config(['$httpProvider', function($httpProvider) {
        $httpProvider.interceptors.push('tokenInterceptor');
    }]);