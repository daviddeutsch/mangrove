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
			if ( $r == '.' || $r == '..' ) continue;

			if ( is_dir($path . '/' . $r) ) {
				$this->populateAssets($path . '/' . $r);

				return;
			}

			$f = explode('.', $r);

			$ext = array_pop($f);

			switch ( $ext ) {
				case 'json': case 'txt': case 'md': break;
				case 'js':
					$this->assets['js'][] = $path . '/' . $r;
					break;
				case 'css':
					$this->assets['css'][] = $path . '/' . $r;
					break;
				default:
					$this->assets['img'][] = $path . '/' . $r;
					break;
			}
		}
	}
}
