<?php
namespace MangroveClient\Service;

use Saltwater\Server as S;
use Saltwater\Utils as U;
use Saltwater\Thing\Service;

class Package extends Service
{
	public function fromSource( $source )
	{
		$this->source = MangroveApp::$app->temp . '/' . $source;

		if ( !is_dir($this->source) ) {
			$this->source = $this->unzip($this->source . '.zip', true);
		}

		$this->info = U::getJSON($this->source . '/info.json');

		$this->fromInfo($this->info);
	}

	public function fromInfo( $info )
	{
		// Using get_object_vars instead of a straight foreach for compat
		foreach ( get_object_vars($info) as $key => $value ) {
			switch ( $key ) {
				case 'time':
					$this->created = $value;

					break;
				case 'version':
					$version = MangroveUtils::explodeVersion($value);

					foreach ( get_object_vars($version) as $k => $v ) {
						$this->{'version_'.$k} = $v;
					}

					break;
				default:
					if ( is_string($value) ) {
						$key = str_replace('-', '_', $key);

						$this->$key = $value;
					}

					break;
			}
		}
	}

	/**
	 * @param string $path
	 */
	private function unzip( $path, $remove=false )
	{
		$file = pathinfo($path);

		$target = MangroveApp::$app->temp . '/' . $file['filename'];

		if ( !is_dir($target) ) mkdir($target, 0744);

		$zip = new ZipArchive();

		$zip->open($path);

		$zip->extractTo($target);

		if ( $remove ) unlink($path);

		return $target;
	}

	public function getInstaller()
	{
		$class = str_replace(
				' ', '', ucwords(
					str_replace('-', ' ', $this->type)
				)
			)
			. 'Installer';

		if ( !class_exists($class) ) {
			$class = 'LibraryInstaller';
		}

		return new $class($this);
	}

	private function registerPackage( $package )
	{
		$entry = $this->r->_('package');

		if ( empty($entry->installed) ) {
			$entry->installed = $this->r->isoDateTime();
		}

		$this->r->_($entry);
	}
}
