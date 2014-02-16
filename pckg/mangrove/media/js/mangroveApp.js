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
			//RestangularProvider.setBaseUrl('index.php?option=com_mangrove');

			$urlRouterProvider
				.otherwise('/packages');

			$stateProvider
				.state('repositories', {
					abstract: true,
					templateUrl: jurl('repositories'),
					views: {
						'footer': { templateUrl: jurl('footer') }
					}
				})
				.state('repositories.list', {
					url: '/repositories'
				})
				.state('repositories.detail', {
					url: '/repository/:repositoryId',
					templateUrl: jurl('repositories')
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
	.directive('mgRepository', function() {
		return {
			restrict: 'E',
			scope: {
				repository: '=repository'
			},
			templateUrl: jurl('repository'),
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
				repository: '=application'
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
	.controller('RepositoryListCtrl',
		[
			'$scope', 'Repository',
			function ($scope, Repository) {
				$scope.repositories = Repository.query();
			}
		]
	);

mangroveApp
	.controller('RepositoryDetailCtrl',
		[
			'$scope', 'Repository',
			function ($scope, Repository) {
				return Repository.get({task: 'repository'});
			}
		]
	);

// Fix for Joomla 2.5 language modal
jQuery(document).ready(function (jQuery) {
	jQuery('#module-status span.multilanguage a').removeClass('modal');
});
