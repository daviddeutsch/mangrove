var mangroveApp = angular.module(
	"mangroveApp",
	[
		'restangular', 'ngRoute', 'ui.router',
		'ngAnimate', 'ui.bootstrap', 'OmniBinder',
		'mangroveBase'
	]
);

jurl = function (name) {
	return "components/com_mangrove/templates/" + name + ".html";
};

mangroveApp
	.config(
		[
		'$stateProvider', '$urlRouterProvider', 'RestangularProvider',
		function ($stateProvider, $urlRouterProvider, RestangularProvider) {
			RestangularProvider.setResponseExtractor(function(response) {
				var newResponse = response;
				if (angular.isArray(response)) {
					angular.forEach(newResponse, function(value, key) {
						newResponse[key].originalElement = angular.copy(value);
					});
				} else {
					newResponse.originalElement = angular.copy(response);
				}

				return newResponse;
			});

			RestangularProvider.setBaseUrl('index.php?option=com_mangrove');

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
			'$scope', '$timeout', '$filter', 'Restangular',
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

mangroveApp
	.service('restangularBinder',
	[
		'DirtyPersist', 'obBinderTypes', '$parse', 'Restangular',
		function (DirtyPersist, obBinderTypes, $parse, Restangular) {
			this.subscribe = function (binder) {
				binder.index = [];

				binder.persist = DirtyPersist;
				binder.persist.subscribe(binder.query);

				if (binder.type === obBinderTypes.COLLECTION) {
					binder.persist.on('add', binder.query, function (update) {
						var index = getIndexOfItem(binder.scope[binder.model], update.id, binder.key);

						index = typeof index === 'number' ? index : binder.scope[binder.model].length;

						binder.onProtocolChange.call(binder, [{
							addedCount: 1,
							added: [update],
							index: index,
							removed: []
						}]);
					});

					binder.persist.on('remove', binder.query, function (update) {
						var index = getIndexOfItem(binder.scope[binder.model], update.id, binder.key);

						if (typeof index !== 'number') return;

						var change = {
							removed: [update],
							addedCount: 0,
							index: index
						};

						binder.onProtocolChange.call(binder, [change]);
					});

					binder.persist.on('update', binder.query, function (update) {
						var index, removed;

						index = getIndexOfItem(binder.scope[binder.model], update.id, binder.key);

						index = typeof index === 'number' ? index : binder.scope[binder.model].length - 1;

						removed = angular.copy(binder.scope[binder.model][index]);

						binder.onProtocolChange.call(binder, [{
							index: index,
							addedCount: 1,
							removed: [removed],
							added: [update]
						}]);
					});
				}

				function getIndexOfItem (list, id, key) {
					var itemIndex;

					angular.forEach(list, function (item, i) {
						if (itemIndex) return itemIndex;
						if (item[key] === id) itemIndex = i;
					});

					return itemIndex;
				}
			};

			this.processChanges = function (binder, delta) {
				var change,
					getter = $parse(binder.model);

				for (var i = 0; i < delta.changes.length; i++) {
					change = delta.changes[i];

					if (change.addedCount) {
						for (var j = change.index; j < change.addedCount + change.index; j++) {
							binder.ignoreNProtocolChanges++;

							var elem = getter(binder.scope)[j];

							Restangular.all(binder.query).post(elem)
								.then(function(reply) {
									elem.id = reply;
								});
						}
					}

					if (change.removed.length) {
						for (var k = 0; k < change.removed.length; k++) {
							binder.ignoreNProtocolChanges++;

							var object = change.removed[k];

							Restangular.one(binder.query, object.id).remove();
						}
					}

					if (!$.isEmptyObject(change.changed)) {
						binder.ignoreNProtocolChanges++;

						Restangular.one(binder.query, change.index)
							.customPOST(JSON.stringify(change.changed));
					}
				}
			};
		}
	]
);

mangroveApp
	.service( 'DirtyPersist',
		[
			'$q', '$timeout', 'Restangular', 'appStatus',
			function ($q, $timeout, Restangular, appStatus) {
				appStatus.loading();

				var callbacks = [];

				var delay = 1000;

				var client = makeid();

				function makeid() {
					var text = "";
					var possible = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789";

					for( var i=0; i < 12; i++ )
						text += possible.charAt(Math.floor(Math.random() * possible.length));

					return text;
				}

				// Register listener client, start polling
				Restangular.all('hook/subscriber').post({name:client}).then(
					function() {
						tick();
					}
				);

				// Poll the server every interval if we're listening for something
				var tick = function () {
					appStatus.ready(500);

					query().then(
						function () {
							Platform.performMicrotaskCheckpoint();

							$timeout(tick, delay);
						}
					);
				};

				// When polling, trigger callbacks
				var query = function () {
					var deferred = $q.defer();

					if ( callbacks.length ) {
						deferred.promise
							.then(updateCheck)
							.then(function(){
								appStatus.ready(500);
							})
					}

					deferred.resolve();

					return deferred.promise;
				};

				var updateCheck = function () {
					appStatus.loading();

					Restangular.all('hook/updates/'+client).getList().then(
						function (reply) {
							// If we find nothing right now, slow down a little
							if ( !reply || reply == "null" ) {
								delay = 2000;
								return;
							}

							var updates = [];

							angular.forEach(reply, function(update) {
								updates.push(update.originalElement);
							});

							if ( updates.length < 1 ) {
								delay = 2000;
								return;
							}

							// Regular speed now
							delay = 1000;

							angular.forEach(updates, function(update) {
								angular.forEach(callbacks, function(callback) {
									if (
										( update.operation == callback.operation )
											&& ( update.type == callback.query )
										) {
										callback.callback(update.object);
									}
								});
							});
						}
					);
				};

				this.on = function (operation, query, callback) {
					callbacks.push(
						{
							operation: operation,
							query:     query,
							callback:  callback
						}
					);
				};

				this.subscribe = function (res) {
					Restangular.all('hook/subscription').post(
						{client:client, resource:res}
					);
				}
			}
		]
	);

// Fix for Joomla 2.5 language modal
jQuery(document).ready(function (jQuery) {
	jQuery('#module-status span.multilanguage a').removeClass('modal');
});
