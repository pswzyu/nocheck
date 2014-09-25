'use strict';

/**
 * @ngdoc overview
 * @name issuemyvisaApp
 * @description
 * # issuemyvisaApp
 *
 * Main module of the application.
 */
var issuemyvisaApp = angular
  .module('issuemyvisaApp', [
    'ngAnimate',
    'ngCookies',
    'ngResource',
    'ngRoute',
    'ngSanitize',
    'ngTouch',
    'ngGrid'
  ]);

issuemyvisaApp.config(function ($httpProvider, $routeProvider) {
    $httpProvider.interceptors.push(function($q, $rootScope) {
      return {
          'request': function(config) {
              $rootScope.$broadcast('loading-started');
              return config || $q.when(config);
          },
          'response': function(response) {
              $rootScope.$broadcast('loading-complete');
              return response || $q.when(response);
          }
      };
    });

    $routeProvider
      .when('/', {
        templateUrl: 'views/main.html',
        controller: 'MainCtrl'
      })
      .when('/about', {
        templateUrl: 'views/about.html',
        controller: 'AboutCtrl'
      })
      .otherwise({
        redirectTo: '/'
      });
  });

issuemyvisaApp.directive("loadingIndicator", function() {
    return {
        restrict : "A",
        template: '<i class="fa fa-cog fa-spin"></i> Loading...',
        link : function(scope, element, attrs) {
            scope.$on("loading-started", function(e) {
                element.css({"display" : ""});
            });

            scope.$on("loading-complete", function(e) {
                element.css({"display" : "none"});
            });

        }
    };
});

issuemyvisaApp.factory('appConfig', function(){

    return {
       apiRootUrl: 'http://issuemyvisa.today/api/v1'
    };
});
