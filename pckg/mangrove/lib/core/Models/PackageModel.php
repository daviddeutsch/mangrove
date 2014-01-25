<?php

class PackageModel extends RedBean_PipelineModel
{
	public function fromSource( $source )
	{
		if ( !is_dir($source) ) {
			$source = $this->unzip($source, true);
		}

		$info = MangroveUtils::getJSON($source.'/info.json');

		$this->fromInfo($info);
	}

	public function fromInfo( $info )
	{

	}

	private function unzip( $path, $remove=false )
	{
		$file = pathinfo($path);

		$target = $this->temp . '/' . $file['filename'];

		if ( !is_dir($target) ) mkdir($target, 0744);

		$zip = new ZipArchive();

		$zip->open($path);

		$zip->extractTo($target);

		if ( $remove ) unlink($path);

		return $target;
	}

	private function installPackage( $package )
	{
		$sha = explode('.', $package);

		$sha = $sha[0];



		// Double-check everything is nice and tidy
		if ( is_dir($this->temp.'/'.$sha) ) {
			MangroveUtils::rrmdir($this->temp.'/'.$sha);
		}

		if ( file_exists($this->temp.'/'.$sha.'.zip') ) {
			unlink($this->temp.'/'.$sha.'.zip');
		}
	}

	private function registerPackage( $package )
	{
		$entry = $this->r->_('package');

		foreach ( get_object_vars($package) as $key => $value ) {
			switch ( $key ) {
				case 'payload':
					$entry->installed = $value->installed_time;
					break;
				case 'time':
					$entry->created = $value;
					break;
				case 'version':
					$version = $this->explodeVersion($value);

					foreach ( $version as $k => $v ) {
						$entry->{'version_'.$k} = $v;
					}
					break;
				default:
					if ( is_string($value) ) {
						$key = str_replace('-', '_', $key);

						$entry->$key = $value;
					}
			}
		}

		if ( empty($entry->installed) ) {
			$entry->installed = $this->r->isoDateTime();
		}

		$this->r->_($entry);
	}

}
