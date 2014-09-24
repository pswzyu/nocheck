'use strict';

/**
 * @ngdoc function
 * @name issuemyvisaApp.controller:MainCtrl
 * @description
 * # MainCtrl
 * Controller of the issuemyvisaApp
 */
angular.module('issuemyvisaApp')
  .controller('MainCtrl', ['$scope', '$log', '$http', 'appConfig', function ($scope, $log, $http, appConfig) {
        var page = 1, limit = 10;


        $http({
            url: appConfig.apiRootUrl + '/view/all/' + limit + '/' + page + "?callback=JSON_CALLBACK",
            method: 'JSONP',
            success: function(data, status, headers, httpConfig){
                //$scope.gridOptions = { data: 'myData' };
                $log.log(data);
            },
            error: function(data, status, headers, config) {
                $log.log(data);
            }
        });

  }]);
