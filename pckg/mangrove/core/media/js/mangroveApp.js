// Fix for Joomla 2.5 language modal
jQuery(document).ready(function (jQuery) {
	jQuery('#module-status span.multilanguage a').removeClass('modal');
});

var mangroveApp = angular.module("mangroveApp", ['ui.compat', 'mangroveServices']);

jurl = function (name) {
	return "components/com_mangrove/templates/" + name + ".html";
};

mangroveApp
	.config(
		['$stateProvider', '$urlRouterProvider',
			function ($stateProvider, $urlRouterProvider) {
				$urlRouterProvider
					.otherwise('/packages');

				$stateProvider
					.state('base', {
						abstract: true,
						views: {
							'footer': { templateUrl: jurl('footer') },
							'header': { templateUrl: jurl('header') }
						}
					})
					.state('repository', {
						abstract: true,
						parent: 'base',
						templateUrl: jurl('repositories')
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
						parent: 'base',
						data: {
							search: '',
							ready: true
						}
					})
					.state('package.list', {
						url: '/packages',
						templateUrl: jurl('packages')
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
						templateUrl: jurl('credits'),
						views: {
							'footer': { templateUrl: jurl('footer') },
							'main': { templateUrl: jurl('credits') }
						}
					})
			}
		]
	)

	.controller("MenuCtrl",
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
)

	.controller('PackageListCtrl',
		['$scope', '$stateParams', 'Package',
			function ($scope, $stateParams, Package) {
				$scope.search = '';

				$scope.repositories = Package.query();

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

angular.module('mangroveServices', ['ngResource'])
	.factory('Repository',
	function ($resource) {
		return $resource('index.php?option=com_mangrove&task=repository', {}, {
			query: {method: 'GET', params: {}, isArray: true}
		});
	}
)
	.factory('Package',
	function ($resource) {
		return $resource('index.php?option=com_mangrove&task=package', {}, {
			query: {method: 'GET', params: {}, isArray: true}
		});
	}
)
;


function repositoryDetailCtrl($scope, $routeParams, Repository) {
	$scope.repository = Repository.get({}, function (repository) {
		$scope.mainImageUrl = repository.images[0];
	});
}

function AlertCtrl($scope) {
	$scope.alerts = [];

	$scope.addAlert = function () {
		$scope.alerts.push({msg: "Another alert!"});
	};

	$scope.closeAlert = function (index) {
		$scope.alerts.splice(index, 1);
	};
}
