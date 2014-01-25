<?php

class AssetInstaller extends MangroveInstaller
{
	public function install()
	{
		parent::install();

		foreach ( scandir($this->package->source) as $r ) {
			if ( is_dir($r) ) continue;

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
