var mangroveApp = angular.module("mangroveApp", ['ui.compat', 'ui.bootstrap', 'Restangular']);

jurl = function (name) {
	return "components/com_mangrove/templates/" + name + ".html";
};

mangroveApp
	.config(
		['$stateProvider', '$urlRouterProvider', 'RestangularProvider', '$httpProvider',
			function ($stateProvider, $urlRouterProvider, RestangularProvider, $httpProvider) {
				RestangularProvider.setBaseUrl('index.php?option=com_mangrove');

				$urlRouterProvider
					.otherwise('/packages');

				$stateProvider
					.state('repository', {
						abstract: true,
						templateUrl: jurl('repositories'),
						views: {
							'footer': { templateUrl: jurl('footer') }
						}
					})
					.state('repository.list', {
						url: '/repositories'
					})
					.state('repository.detail', {
						url: '/repository/:repositoryId',
						templateUrl: jurl('repository')
					})
					.state('package', {
						abstract: true,
						views: {
							'footer': { templateUrl: jurl('footer') },
							'main': { templateUrl: jurl('packages') }
						}
					})
					.state('package.list', {
						url: '/packages'
					})
					.state('package.search', {
						url: '/packages/:search'
					})
					.state('package.detail', {
						url: '/package/:packageId',
						templateUrl: jurl('package')
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
	)

	.controller("MenuCtrl",
	['$scope', '$location',
		function ($scope, $location) {
			$scope.panes = [
				{ title: "packages", content: "components/com_mangrove/templates/packages.html" },
				{ title: "repositories", content: "components/com_mangrove/templates/repositories.html" },
				{ title: "settings", content: "components/com_mangrove/templates/settings.html" },
				{ title: "credits", content: "components/com_mangrove/templates/credits.html" }
			];

			angular.forEach($scope.panes, function (pane) {
				pane.selected = '/' + pane.title == $location.$$path;
			});

			$scope.select = function selectPane(pane) {
				angular.forEach($scope.panes, function (pane) {
					pane.selected = false;
				});
				pane.selected = true;
			};
		}
	]
)

	.controller('PackageListCtrl',
		['$scope', '$timeout', '$filter', 'Restangular',
			function ($scope, $timeout, $filter, Restangular) {
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

				allpackages = Restangular.all("packages").getList();

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
	)

	.controller('InstallListCtrl',
		['$scope',
			function ($scope) {
				$scope.install = [];
			}
		]
	)

	.controller('RepositoryListCtrl',
		['$scope', 'Repository',
			function ($scope, Repository) {
				$scope.repositories = Repository.query();
			}
		]
	)

	.controller('RepositoryDetailCtrl',
		['$scope', 'Repository',
			function ($scope, Repository) {
				return Repository.get({task: 'repository'});
			}
		]
	);

// Fix for Joomla 2.5 language modal
jQuery(document).ready(function (jQuery) {
	jQuery('#module-status span.multilanguage a').removeClass('modal');
});
