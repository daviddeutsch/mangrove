var mangroveApp = angular.module(
	"mangroveApp",
	[
		'ngAnimate', 'ui.router', 'ui.bootstrap', 'mangroveBase'
	]
);

jurl = function (name) {
	return "components/com_mangrove/templates/" + name + ".html";
};

mangroveApp
	.config(
	[
	'$stateProvider', '$urlRouterProvider',
	function ($stateProvider, $urlRouterProvider) {
		$urlRouterProvider
			.otherwise('/applications');

		$stateProvider
			.state('sources', {
				abstract: true,
				templateUrl: jurl('sources'),
				views: {
					'footer': { templateUrl: jurl('footer') }
				}
			})
			.state('sources.list', {
				url: '/sources'
			})
			.state('sources.detail', {
				url: '/source/:sourceId',
				templateUrl: jurl('sources')
			})
			.state('applications', {
				abstract: true,
				views: {
					'footer': { templateUrl: jurl('footer') },
					'main': { templateUrl: jurl('applications') }
				}
			})
			.state('applications.list', {
				url: '/applications'
			})
			.state('credits', {
				url: '/credits',
				views: {
					'footer': { templateUrl: jurl('footer') },
					'main': { templateUrl: jurl('credits') }
				}
			})
	}
	]
);

mangroveApp
	.directive('mgSource', function() {
		return {
			restrict: 'E',
			scope: {
				source: '=source'
			},
			templateUrl: jurl('source'),
			controller: [
				'$scope', '$http',
				function($scope, $http) {
					$scope.authenticate = function() {
						// Post passphrase to server, attempting to authenticate
					};
				}
			]
		};
	});

mangroveApp
	.directive('mgApplication', function() {
		return {
			restrict: 'E',
			scope: {
				source: '=application'
			},
			templateUrl: jurl('application'),
			controller: [
				'$scope', '$http',
				function($scope, $http) {
					$scope.select = function() {
						// Make server call to mark application for later install
						// This may already trigger download actions to expedite
						// later calls
					};

					$scope.install = function() {
						// Actually install the applications main package
						// And associated packages
					};
				}
			]
		};
	});

mangroveApp
	.controller('mgAppCtrl',
	[
		'mgAppSession',
		function(mgAppSession) {
			mgAppSession.init('com_mangrove');
		}
	]
);

mangroveApp
	.controller('ApplicationListCtrl',
	[
		'$scope', '$timeout', '$filter',
		function ($scope, $timeout, $filter) {
			var spinner = Spinners.create('#spinner', {
				radius: 2,
				height: 4,
				width: 14,
				dashes: 3,
				opacity: 0.49,
				padding: 0,
				rotation: 250,
				color: '#080'
			}).play();

			$scope.search = '';

			//allpackages = Restangular.all("packages").getList();

			$scope.packages = [];

			$scope.installList = [];

			filter = $filter('filter');

			$scope.filter = function(q) {
				$scope.inprogress = true;
				$scope.packages = filter(allpackages, q);
				$timeout(function(){
					$scope.inprogress = false;
				}, 100);
			};
		}
	]
);

mangroveApp
	.controller('InstallListCtrl',
	[
		'$scope',
		function ($scope) {
			$scope.install = [];
		}
	]
);

mangroveApp
	.controller('SourceListCtrl',
	[
		'$scope', 'Source',
		function ($scope, Source) {
			$scope.sources = Source.query();
		}
	]
);

mangroveApp
	.controller('SourceDetailCtrl',
	[
		'$scope', 'Source',
		function ($scope, Source) {
			return Source.get({task: 'source'});
		}
	]
);

// Fix for Joomla 2.5 language modal
jQuery(document).ready(function (jQuery) {
	jQuery('#module-status span.multilanguage a').removeClass('modal');
});
