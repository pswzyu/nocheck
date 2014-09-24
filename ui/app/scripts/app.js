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
  ])
  .config(function ($routeProvider) {
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

issuemyvisaApp.factory('appConfig', function(){

    return {
       apiRootUrl: 'http://issuemyvisa.today/api/v1'
    };
});
