var mangroveApp = angular.module(
	"mangroveApp",
	[
		'ngAnimate', 'ui.router', 'angularMoment',
		'ui.bootstrap', 'swamp'
	]
);

jurl = function (name) {
	return "partials/" + name + ".html";
};

mangroveApp
	.config(
	[
	'$stateProvider', '$urlRouterProvider',
	function ($stateProvider, $urlRouterProvider)
	{
		$urlRouterProvider
			.otherwise('/application');

		$stateProvider

			.state('source', {
				abstract: true,
				url: '/source',
				views: {
					'footer': { templateUrl: jurl('footer') },
					"main": { templateUrl: jurl('source') }
				}
			})

			.state('source.list', {
				url: '',
				views: {
					"source-main": { templateUrl: jurl('source.list') }
				}
			})

			.state('source.detail', {
				url: '/:itemId',
				views: {
					"source-main": { templateUrl: jurl('source.detail') }
				}
			})


			.state('application', {
				abstract: true,
				views: {
					'footer': { templateUrl: jurl('footer') },
					'main': { templateUrl: jurl('application') }
				}
			})

			.state('application.list', {
				url: '/application'
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
	.directive('mgConfirmClick',
	function()
	{
		return {
			link: function (scope, element, attr) {
				var msg = attr.mgConfirmText || "Are you sure?";
				var clickAction = attr.mgConfirmClick;
				element.bind('click',function () {
					if ( window.confirm(msg) ) {
						scope.$eval(clickAction)
					}
				});
			}
		};
	}
);

mangroveApp
	.controller('mgAppCtrl',
	[
		'swSession',
		function(swSession)
		{
			swSession.init('com_mangrove');

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
		}
	]
);

mangroveApp
	.controller('ApplicationListCtrl',
	[
		'$scope', 'dataPersist',
		function ($scope, dataPersist)
		{
			$scope.loading = true;

			dataPersist.bindResource(
				$scope,
				{
					res: 'application'
				}
			)
			.then(function() {
				$scope.loading = false;
			});
		}
	]
);

mangroveApp
	.controller('InstallListCtrl',
	[
		'$scope',
		function ($scope)
		{
			$scope.install = [];
		}
	]
);

mangroveApp
	.controller('SourceListCtrl',
	[
		'$scope', 'dataPersist',
		function ($scope, dataPersist)
		{
			$scope.loading = true;

			dataPersist.bindResource(
				$scope,
				{
					res: 'source',
					callback: {
						add: function(id) { $state.go('source.detail',{itemId:id}); },
						remove: function(id) {
							if ($stateParams.itemId == id) {
								$state.transitionTo('source.list');
							}
						}
					}
				}
			)
			.then(function() {
				$scope.loading = false;
			});
		}
	]
);

mangroveApp
	.controller('SourceCtrl',
	[
		'$scope', '$state', '$stateParams', 'dataPersist',
		function ($scope, $state, $stateParams, dataPersist)
		{
			$scope.editmode = false;

			dataPersist.bindResource(
				$scope,
				{
					res: 'source',
					callback: {
						add: function(id) { $state.go('source.detail',{itemId:id}); },
						remove: function(id) {
							if ($stateParams.itemId == id) {
								$state.transitionTo('source.list');
							}
						}
					}
				}
			)
			.then(function() {
				$scope.loading = false;

					if ( $stateParams.itemId == 0 ) {
						$scope.editmode = true;

						return;
					} else if ( typeof $scope.source == 'undefined' ) {
						$state.transitionTo('source.list');
					}

					for ( var i = 0; i < $scope.source.length; i++ ) {
						if ($scope.source[i].id == $stateParams.itemId) {
							$scope.source = $scope.source[i];
						}
					}

					if ( typeof $scope.source == 'undefined' ) {
						$state.transitionTo('source.list');
					}
			});
		}
	]
);

// Fix for Joomla 2.5 language modal
jQuery(document).ready(function (jQuery) {
	jQuery('#module-status span.multilanguage a').removeClass('modal');
});
