<?php

class AssetInstaller extends MangroveInstaller
{
	public function install()
	{
		parent::install();

		$this->populateAssets( $this->package->source );
	}

	public function populateAssets( $path )
	{
		foreach ( scandir($path) as $r ) {
			if ( is_dir($r) ) {
				$this->populateAssets($path . '/' . $r);
			}

			$f = explode('.', $r);

			$ext = array_pop($f);

			$path = $this->package->source . $r;

			switch ( $ext ) {
				case 'json': break;
				case 'js':
					$this->assets['js'][] = $path;
					break;
				case 'css':
					$this->assets['css'][] = $path;
					break;
				default:
					$this->assets['img'][] = $path;
					break;
			}
		}
	}
}
