var mangroveApp = angular.module("mangroveApp", ['ui.compat','mangroveServices']);

mangroveApp
.config(
	['$stateProvider',
	function ($stateProvider) {
	$stateProvider
		.state('repository', {
			abstract:true,
			templateUrl:"templates/repositories.html",
			})
		.state('repository.list', {
			url:'/repositories'
		})
		.state('repository.detail', {
			url:'/repository/:repositoryId',
			templateUrl:"templates/repository.html"
		})
		.state('package', {
			abstract:true,
			templateUrl:"templates/packages.html",
			data:{
				search:''
			}
		})
		.state('package.start', {
			url:'/start'
		})
		.state('package.list', {
			url:'/packages'
		})
		.state('package.detail', {
			url:'/package/:packageId',
			templateUrl:"templates/package.html"
		})
	}
	]
)

.controller( "MenuCtrl",
	function ($scope, $location) {
		$scope.panes = [
			{ title:"packages", content:"templates/packages.html" },
			{ title:"repositories", content:"templates/repositories.html" }
		];

		angular.forEach($scope.panes, function(pane) {
			pane.selected = '/'+pane.title == $location.$$path;
		});

		$scope.select = function selectPane(pane) {
			angular.forEach($scope.panes, function(pane) {
				pane.selected = false;
			});
			pane.selected = true;
		};
	}
)

.controller('PackageListCtrl',
	['$scope', 'Package',
	function ($scope, Package) {
		$scope.search = '';

		$scope.searchStart = true;

		angular.element("#mangrove-header").hide();

		$scope.$watch('search', function(newVal, oldVal) {
			if ( angular.element("#mangrove-starter input").hasClass('ng-pristine') ) {
			} else if ( (newVal.length == 0) && !$scope.searchStart ) {
				$scope.searchStart = true;

				angular.element("#mangrove-starter input").focus();

				angular.element("#mangrove-starter").animate({opacity: 1, height: '100%'}, 300, 'swing', function(){
					angular.element("#mangrove-header").show().animate({opacity: 0, height: 0}, 100, 'swing', function(){
						angular.element("#mangrove-starter input").focus();
					});
				});
			} else if ( newVal.length > 0 && $scope.searchStart ) {
				$scope.searchStart = false;

				angular.element("#mangrove-header input").focus();

				angular.element("#mangrove-header").show().animate({opacity: 1, height: '100%'}, 300, 'swing', function(){
					angular.element("#mangrove-starter").animate({opacity: 0, height: 0}, 100, 'swing', function(){
						angular.element("#mangrove-header input").focus();
					});
				});
				;
			}
		});

		$scope.repositories = Package.query();
		
		$scope.switchup = function() {
			
		};
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
		return Repository.get({task:'repository'});
	}
	]
);

angular.module('mangroveServices', ['ngResource'])
.factory('Repository',
	function ($resource) {
		return $resource('index.php?option=com_mangrove&task=repository', {}, {
			query: {method:'GET', params:{}, isArray:true}
		});
	}
)
.factory('Package',
	function ($resource) {
		return $resource('index.php?option=com_mangrove&task=package', {}, {
			query: {method:'GET', params:{}, isArray:true}
		});
	}
)
;



function repositoryDetailCtrl ($scope, $routeParams, Repository) {
	$scope.repository = Repository.get({}, function(repository) {
		$scope.mainImageUrl = repository.images[0];
	});
}

function AlertCtrl ($scope) {
	$scope.alerts = [];

	$scope.addAlert = function() {
		$scope.alerts.push({msg: "Another alert!"});
	};

	$scope.closeAlert = function(index) {
		$scope.alerts.splice(index, 1);
	};
}