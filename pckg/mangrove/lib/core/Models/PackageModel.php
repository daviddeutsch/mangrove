<?php

class PackageModel extends RedBean_PipelineModel
{
	public function fromSource( $source )
	{
		$this->source = MangroveApp::$temp . '/' . $source;

		if ( !is_dir($this->source) ) {
			$this->source = $this->unzip($this->source, true);
		}

		$info = MangroveUtils::getJSON($this->source . '/info.json');

		$this->fromInfo($info);
	}

	public function fromInfo( $info )
	{
		// Using get_object_vars instead of a straight foreach for compat reason
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

	private function unzip( $path, $remove=false )
	{
		$file = pathinfo($path);

		$target = MangroveApp::$temp . '/' . $file['filename'];

		if ( !is_dir($target) ) mkdir($target, 0744);

		$zip = new ZipArchive();

		$zip->open($path);

		$zip->extractTo($target);

		if ( $remove ) unlink($path);

		return $target;
	}

	private function install()
	{
		$class = str_replace(
				' ', '', ucwords(
					str_replace('-', ' ', $this->info->type)
				)
			)
			. 'Installer';

		$installer = new $class($this);

		$installer->beforeInstall();

		$installer->install();

		$installer->afterInstall();

		$sha = explode('.', $package);

		$sha = $sha[0];



		// Double-check everything is nice and tidy
		if ( is_dir(MangroveApp::$temp.'/'.$sha) ) {
			MangroveUtils::rrmdir(MangroveApp::$temp.'/'.$sha);
		}
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
