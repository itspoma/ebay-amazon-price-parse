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
        if (response.data && response.data.records) {

          response.data.records.forEach(function (record) {
            record.added_date = new Date(record.added_date);
            record.updated_date = new Date(record.updated_date);

            record.title = (function () {
              for (var k in record) {
                var v = record[k];
                if (0 == k.indexOf('title-') && v) {
                  return v;
                }
              }

              return null
            })();


            record.max_price = 0;

            for (var k in record) {
              var v = record[k];
              if (0 == k.indexOf('price-') && v) {
                var _price = v.match(/[\d\.]+/);
                    _price = _price ? _price[0] : null;

                if (_price && _price > record.max_price) {
                  record.max_price = parseFloat(_price);
                }
              }
            }

          });
        }

        return response;
      }
    },

    'deleteAll': {
      method: 'GET',
      url: baseUrl + '?action=delete-all'
    }
  });
});

// main app controller
app.controller('pageCtrl', function($scope, $timeout, $mdToast, Results, Search) {
  $scope.results = [];
  $scope.searchInProgress = false;
  $scope.searchIntermediateResults = {};

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
    $scope.query = '';

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
    var query = $scope.query
      , sourcesParsed = 0
      , sources = [
        'amazon',
        'ebaylowest',
        'ebaylowestsold',
        'musicmagpie',
        // 'zapper',
        'webuybooks',
        'ziffit'
      ];

    $scope.searchInProgress = true;
    $scope.searchIntermediateResults[query] = {};

    $scope.focusQueryField();

    sources.forEach(function (source) {
      var searchParams = {
        'source': source,
        'query': query
      };

      Search.search(searchParams, function (response) {
        // analyze the response
        if (response.error) {
          var _message = 'the request to ""' + response.data.source + '"" was failed due to - ' + response.error;
          $scope.toastMessage(_message);
        }
        else {
          $scope.searchIntermediateResults[query] = response.data;
        }

        // all sources are parsed
        if (++sourcesParsed == sources.length) {
          // remove current query-obj
          delete $scope.searchIntermediateResults[query];

          // if is last
          if (0 == Object.keys($scope.searchIntermediateResults).length) {
            $scope.searchInProgress = false;
            $scope.focusQueryField();
          }

          $scope.refreshResults();
        }

        //
      });
    });

  }

  // delete action
  $scope.deleteAll = function () {
    Results.deleteAll({}, function (response) {
      // analyze the response
      if (response.error) {
        var _message = 'the request to delete record was failed due to - ' + response.error;
        $scope.toastMessage(_message);
      }

      $scope.refreshResults();;
    });
  }

});

app.filter('truncate', function () {
  return function (text, length, end) {
    if (isNaN(length)) {
      length = 10;
    }

    if (end === undefined) {
      end = "...";
    }

    if (!text || text.length <= length || text.length - end.length <= length) {
      return text;
    }
    else {
      return String(text).substring(0, length-end.length) + end;
    }
  };
});
