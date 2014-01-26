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
				if ( $r == '.' || $r == '..' ) continue;

				$this->populateAssets($path . '/' . $r);

				return;
			}

			$f = explode('.', $r);

			$ext = array_pop($f);

			$path .= $r;

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
