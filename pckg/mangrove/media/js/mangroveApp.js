var mangroveApp = angular.module("mangroveApp", ['ui.compat','mangroveServices']);

mangroveApp
.config(
	['$stateProvider', '$urlRouterProvider',
	function ($stateProvider, $urlRouterProvider) {
	 $urlRouterProvider
	 	.otherwise('/packages');

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
		.state('package.search', {
			url:'/packages/:search'
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
	['$scope', '$stateParams', 'Package',
	function ($scope, $stateParams, Package) {
		$scope.search = '';

		$scope.searchStart = true;

		angular.element("#mangrove-header").hide();

		$scope.$watch('search', function(newVal, oldVal) {
			if ( angular.element("#mangrove-starter input").hasClass('ng-pristine') ) {
			} else if ( (newVal.length == 0) && !$scope.searchStart ) {
				$scope.searchStart = true;

				angular.element("#mangrove-starter")
					.animate({opacity: 'show', height: 'show'}, 200, 'swing', function(){
						angular.element("#mangrove-header")
							.css('display', 'block')
							.animate({opacity: 'hide', height: 'hide'}, 300, 'swing', function(){
							angular.element("#mangrove-starter input").focus();
						});
					});
			} else if ( newVal.length > 0 && $scope.searchStart ) {
				$scope.searchStart = false;

				angular.element("#mangrove-header")
					.css('display', 'block')
					.animate({opacity: 'show', height: 'show'}, 200, 'swing', function(){
						angular.element("#mangrove-starter")
							.animate({opacity: 'hide', height: 'hide'}, 300, 'swing', function(){
								angular.element("#mangrove-header input").focus();
							});
					});
			}
		});

		if ( ($stateParams.search != null) && ($scope.search == '') ) {
			$scope.search = $stateParams.search;
			
			angular.element("#mangrove-starter input").val($scope.search);
			angular.element("#mangrove-header input").val($scope.search);
			
			$scope.emit('search');

		}

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