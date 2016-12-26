var myApp = angular.module( 'myApp', ['ngRoute']);

myApp.factory('tokenInterceptor',['$q', function ($q) {

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
}]);


// create the controller and inject Angular's $scope
myApp.controller('mainController', function($scope) {
    // create a message to display in our view
    $scope.message = 'Everyone come and see how good I look!';
});

myApp.controller('aboutController', function($scope) {
    $scope.message = 'Look! I am an about page.';
});

myApp.controller('contactController', function($scope) {
    $scope.message = 'Contact us! JK. This is just a demo.';
});

myApp.config(function($routeProvider) {
    $routeProvider

    // route for the home page
        .when('/', {
            templateUrl : '/pages/home.html',
            controller  : 'mainController'
        })

        // route for the about page
        .when('/about', {
            templateUrl : '/pages/about.html',
            controller  : 'aboutController'
        })

        // route for the contact page
        .when('/contact', {
            templateUrl : '/pages/contact.html',
            controller  : 'contactController'
        });
});

