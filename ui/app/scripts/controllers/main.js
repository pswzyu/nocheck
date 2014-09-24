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

        $http.jsonp(appConfig.apiRootUrl + '/view/all/' + limit + '/' + page + '?callback=JSON_CALLBACK').

            success(function(data, status, headers, config){
                $scope.data = data.data;
            }).
            error(function(data, status, headers, config) {
                //$scope.gridOptions = { data: 'myData' };
                $log.error(data);
            });

        $scope.gridOptions = {
            data: 'data',
            enableCellSelection: false,
            enableRowSelection: false,
            columnDefs: [{
                field: 'yearmonth',
                displayName: 'Date',
                cellTemplate: '<div class="ngCellText" ng-class="col.colIndex()"><a href="{{row.getProperty(col.field)}}">{{row.getProperty(col.field)}}</a></div>'
            }, {
                field: 'clear',
                displayName: 'Cleared'
            },{
                field: 'pending',
                displayName: 'Pending'
            }, {
                field: 'rejected',
                displayName: 'Rejected'
            }, {
                field: 'total',
                displayName: 'Total'
            }, {
                field: 'avg_wait',
                displayName: 'Average Wait'
            }]
        };

  }]);
