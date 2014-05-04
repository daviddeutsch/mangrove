var mangroveApp = angular.module(
	"mangroveApp",
	[
		'ngAnimate', 'ui.router', 'angularMoment',
		'mgcrea.ngStrap', 'swamp'
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
					'main': { templateUrl: jurl('application.list') }
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
					res: 'app',
					callback: {
						add: function(id) { $state.go('application.detail',{itemId:id}); },
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
	'$scope', '$state', '$stateParams', 'dataPersist', 'swHttp',
	function ($scope, $state, $stateParams, dataPersist, swHttp)
	{
		$scope.editmode = false;

		$scope.loading = true;

		$scope.status = 'untainted';

		$scope.details = {};

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
		.then(function(){
			if ( $stateParams.itemId == 0 ) {
				$scope.editmode = true;
			} else {
				dataPersist.bindItem($scope, 'source', Number($stateParams.itemId))
					.then(
					function() {
						$scope.loading = false;

						if ( $scope.source.status ) {
							$scope.status = $scope.source.status;
						}
					},
					function() { $state.transitionTo('source.list'); }
				);
			}
		}, function() {
			$state.transitionTo('source.list');
		}
		).then(function(){ $scope.loading = false; });

		$scope.init = function() {
			$scope.loading = true;

			$scope.status = 'connecting';

			swHttp.get('/source/'+$scope.item.id+'/init')
				.success(
				function(data){
					$scope.loading = false;

					$scope.status = data;
				}
			);
		};

		$scope.authenticate = function( type ) {
			$scope.loading = true;

			$scope.status = 'connecting';

			var details = {};

			if ( type == 'account' ) {
				details.username = $scope.details.username;
				details.password = $scope.details.password;
			} else {
				details.passphrase = $scope.details.passphrase;
			}

			swHttp.post('/source/'+$scope.item.id+'/authenticate', details)
				.success(
				function(data){
					$scope.loading = false;

					$scope.status = data;
				}
			);
		};

	}
	]
);

// Fix for Joomla 2.5 language modal
jQuery(document).ready(function (jQuery) {
	jQuery('#module-status span.multilanguage a').removeClass('modal');
});
