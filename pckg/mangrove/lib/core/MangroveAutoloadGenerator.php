<?php

class MangroveAutoloadGenerator
{
	public function create()
	{
		$php = <<<EOF
<?php

class MangroveBootstrapAutoload
{
	private static \$loader;

	public static function loadClassLoader(\$class)
	{
		if ( \$class !== 'MangroveClassLoader' ) return;

		require realpath(
			dirname(__FILE__)
			. '/../../libraries/valanx/mangrove/core/MangroveClassLoader.php'
		);
	}

	public static function getLoader()
	{
		if ( self::\$loader !== null ) {
			return self::\$loader;
		}

		spl_autoload_register(
			array('MangroveBootstrapAutoload', 'loadClassLoader')
		);

		self::\$loader = \$loader = new MangroveClassLoader();

		spl_autoload_unregister(
			array('MangroveBootstrapAutoload', 'loadClassLoader')
		);

		\$vendorDir = dirname(__DIR__);
		\$baseDir = dirname(\$vendorDir);

		\$map = require __DIR__ . '/autoload_namespaces.php';
		foreach (\$map as \$namespace => \$path) {
			\$loader->set(\$namespace, \$path);
		}

		\$classMap = require __DIR__ . '/autoload_classmap.php';
		if (\$classMap) {
			\$loader->addClassMap(\$classMap);
		}

		\$loader->register(true);

		return \$loader;
	}
}

return MangroveBootstrapAutoload::getLoader();

EOF;
	}
}
