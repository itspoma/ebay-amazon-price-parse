"use strict";

var app = angular.module('app', [
  'ngMaterial',
  'ngResource'
]);

// config
app.config(function ($mdThemingProvider, $resourceProvider) {
  $mdThemingProvider.theme('default').primaryPalette('teal');

  $resourceProvider.defaults.stripTrailingSlashes = false;
});

// Search factory
app.factory('Search', function ($resource, $http) {
  var baseUrl = '/api.php';

  return $resource(baseUrl, {}, {
    'search': {
      method: 'GET',
      url: baseUrl + '?action=search'
    }
  });
});

// Results factory
app.factory('Results', function ($resource, $http) {
  var baseUrl = '/api.php';

  return $resource(baseUrl, {}, {
    'list': {
      method: 'GET',
      url: baseUrl + '?action=results',
      responseType: 'json',
      transformResponse: function(response, headers) {
        response.data.records.forEach(function (record) {
          record.added_date = new Date(record.added_date);
          record.updated_date = new Date(record.updated_date);
        });

        return response;
      }
    },

    'delete': {
      method: 'GET',
      url: baseUrl + '?action=delete'
    }
  });
});

// main app controller
app.controller('pageCtrl', function($scope, $timeout, $mdToast, Results, Search) {
  $scope.results = [];
  $scope.searchInProgress = false;

  // quick message on top-right position
  $scope.toastMessage = function (message) {
    $mdToast.show(
      $mdToast.simple()
      .position('top right')
      .textContent(message)
      .action('OK')
      .theme("info-toast")
    );
  }

  // focus the input
  $scope.focusQueryField = function () {
    $timeout(function () {
      document.getElementById('query-field').focus();
    }, 100);
  }

  // refresh the list of results
  $scope.refreshResults = function () {
    $scope.refreshingTheResults = true;
    $scope.results = [];

    Results.list({}, function (response) {
      $scope.refreshingTheResults = false;

      // analyze the response
      if (response.error) {
        var _message = 'the request to get list of results failed due to - ' + response.error;
        $scope.toastMessage(_message);
      }
      else {
        $scope.results = response.data.records;
      }
    });
  }

  $scope.focusQueryField();
  $scope.refreshResults();

  // proceed search by submitting the form
  $scope.search = function () {
    $scope.searchInProgress = true;

    $scope.searchShowIntermediateResults = true;
    $scope.searchIntermediateResults = [];

    var query = $scope.query
      , sourcesParsed = 0
      , sources = [
        'amazon',
        'ebay',
        'musicmagpie',
        'zapper',
        'ziffit'
      ];

    sources.forEach(function (source) {
      var searchParams = {
        'source': source,
        'query': query
      };

      Search.search(searchParams, function (response) {
        // all sources are parsed
        if (++sourcesParsed == sources.length) {
          $scope.searchInProgress = false;
          $scope.focusQueryField();

          $scope.refreshResults();

          $timeout(function () {
            $scope.searchShowIntermediateResults = false;
          }, 2000);
        }

        // analyze the response
        if (response.error) {
          var _message = 'the request to ""' + response.data.source + '"" was failed due to - ' + response.error;
          $scope.toastMessage(_message);
        }
        else {
          $scope.searchIntermediateResults.push({
            'source': response.data.source,
            'price': response.data.price
          })
        }

        //
      });
    });

  }

  // delete action
  $scope.delete = function (record) {
    Results.delete({'query': record.query}, function (response) {
      // analyze the response
      if (response.error) {
        var _message = 'the request to delete record was failed due to - ' + response.error;
        $scope.toastMessage(_message);
      }

      $scope.refreshResults();;
    });
  }

});
