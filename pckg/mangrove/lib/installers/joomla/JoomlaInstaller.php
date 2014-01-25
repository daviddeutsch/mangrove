<?php

class JoomlaInstaller extends MangroveInstaller
{
	public function install()
	{
		parent::install();

		jimport('joomla.installer.installer');

		$installer = new JInstaller();

		$installer->install($this->package->source);
	}

	public function afterInstall()
	{
		// TODO:
		//self::addIndexFiles( array($path) );

		parent::afterInstall();
	}

	public static function addIndexFiles( $paths )
	{
		foreach ( $paths as $path ) {
			if ( !is_dir($path) ) {
				continue;
			}

			$allsub = scandir($path);

			$subdirs = array();
			foreach ( $allsub as $subdir ) {
				if ( strpos($subdir, '.') === false ) {
					$subdirs[] = $path . '/' . $subdir;
				}
			}

			if ( count($subdirs) ) {
				self::addIndexFiles($subdirs);
			}

			$fpath = $path . '/index.html';
			if ( !file_exists($fpath) ) {
				file_put_contents($fpath, '<!DOCTYPE html><title></title>');
			}
		}

		return true;
	}

	protected function canonizeAssets( $folder )
	{
		$path = JPATH_ROOT . '/media/' . $folder;

		foreach ( $this->assets as $type => $content ) {
			if ( empty($content) ) continue;

			$subpath = $path . '/' . $type;

			if ( !is_dir($subpath) ) mkdir($subpath, 0744, true);

			foreach ( $content as $asset ) {
				rename( $asset, $subpath . '/' . basename($asset) );
			}
		}

		self::addIndexFiles( array($path) );
	}
}
