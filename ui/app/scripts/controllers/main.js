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

        $scope.paging = {
            page: 0,
            limit: 10,
            total: 0
        };

        $scope.getData = function (page, limit) {

            $http.jsonp(appConfig.apiRootUrl + '/view/all/' + limit + '/' + page + '?callback=JSON_CALLBACK').
                success(function(data, status, headers, config){
                    $scope.data = data.data;
                    $scope.paging.total = data.total_pages;
                }).
                error(function(data, status, headers, config) {
                    //$scope.gridOptions = { data: 'myData' };
                    $log.error(data);
                });
        };
        
        $scope.getData($scope.paging.page, $scope.paging.limit);

        $scope.pageNewer = function() {
            if($scope.paging.page == 0) {
                return;
            }
            $scope.paging.page -= 1;
            $scope.getData($scope.paging.page, $scope.paging.limit);
        };

        $scope.pageOlder = function() {
            if($scope.paging.page == ($scope.paging.total - 1)){
                return;
            }
            $scope.paging.page += 1;
            $scope.getData($scope.paging.page, $scope.paging.limit);
        };

        $scope.hasNewer = function(){
            return ($scope.paging.page > 0)?'':'disabled';
        }

        $scope.hasOlder = function(){
            return ($scope.paging.page < ($scope.paging.total - 1))?'':'disabled';
        }

        $scope.gridOptions = {
            data: 'data',
            enableCellSelection: false,
            enableRowSelection: true,
            multiSelect: false,
            jqueryUITheme: true,
            enableHighlighting: true,
            enablePaging: false,
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
                displayName: 'Avg. Wait'
            }],
            showFooter: true,
            footerTemplate: '<div ng-show="showFooter"><ul class="pager"><li class="previous" ng-class="[hasNewer()]" ng-click="pageNewer()"><a href="">&larr; Newer</a></li><li class="next" ng-class="[hasOlder()]" ng-click="pageOlder()"><a href="">Older &rarr;</a></li></ul></div>'
        };

  }]);
