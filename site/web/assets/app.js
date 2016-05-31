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
      url: baseUrl + '?action=results'
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

});




////Loading Start
//angular.module('progressLinearDemo1', ['ngMaterial'])
//    .config(function($mdThemingProvider) {
//    })
//    .controller('AppCtrl', ['$scope', '$interval', function($scope, $interval) {
//      var self = this, j= 0, counter = 0;
//      self.mode = 'query';
//      self.activated = true;
//      self.determinateValue = 30;
//      self.determinateValue2 = 30;
//      self.modes = [ ];
//      /**
//       * Turn off or on the 5 themed loaders
//       */
//      self.toggleActivation = function() {
//        if ( !self.activated ) self.modes = [ ];
//        if (  self.activated ) {
//          j = counter = 0;
//          self.determinateValue = 30;
//          self.determinateValue2 = 30;
//        }
//      };
//      $interval(function() {
//        self.determinateValue += 1;
//        self.determinateValue2 += 1.5;
//        if (self.determinateValue > 100) self.determinateValue = 30;
//        if (self.determinateValue2 > 100) self.determinateValue2 = 30;
//        // Incrementally start animation the five (5) Indeterminate,
//        // themed progress circular bars
//        if ( (j < 2) && !self.modes[j] && self.activated ) {
//          self.modes[j] = (j==0) ? 'buffer' : 'query';
//        }
//        if ( counter++ % 4 == 0 ) j++;
//        // Show the indicator in the "Used within Containers" after 200ms delay
//        if ( j == 2 ) self.contained = "indeterminate";
//      }, 100, 0, true);
//      $interval(function() {
//        self.mode = (self.mode == 'query' ? 'determinate' : 'query');
//      }, 7200, 0, true);
//    }]);
////Loading End
