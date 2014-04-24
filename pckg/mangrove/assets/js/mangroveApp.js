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
				url: '/:sourceId',
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
			dataPersist.getList(
				$scope,
				'sources',
				'source',
				{
					remove: function() { $state.transitionTo('sources.list'); },
					load: function() {
						$scope.loading = false;
						$state.go('sources.detail',{sourceId:$scope.source.id});
					}
				}
			);
		}
	]
);

mangroveApp
	.controller('SourceCtrl',
	[
		'$scope', '$state', '$stateParams',
		function ($scope, $state, $stateParams)
		{
			$scope.editmode = false;

			if ( $stateParams.sourceId == 0 ) {
				$scope.editmode = true;

				return;
			} else if ( typeof $scope.sources == 'undefined' ) {
				$state.transitionTo('sources.list');
			}

			for ( var i = 0; i < $scope.sources.length; i++ ) {
				if ($scope.sources[i].id == $stateParams.sourceId) {
					$scope.source = $scope.sources[i];
				}
			}

			if ( typeof $scope.source == 'undefined' ) {
				$state.transitionTo('sources.list');
			}
		}
	]
);

// Fix for Joomla 2.5 language modal
jQuery(document).ready(function (jQuery) {
	jQuery('#module-status span.multilanguage a').removeClass('modal');
});
