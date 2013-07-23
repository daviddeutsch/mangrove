<?php

class JoomlaInstaller extends MangroveInstaller
{
	public function install( $path )
	{
		jimport('joomla.installer.installer');

		$installer = new JInstaller();

		return $installer->install( $path );
	}

	public function afterInstall( $path )
	{
		return self::addIndexFiles( array($path) );
	}

	public static function addIndexFiles( $paths )
	{
		foreach ( $paths as $path ) {
			if ( !is_dir( $path ) ) {
				continue;
			}

			$allsub = scandir( $path );

			$subdirs = array();
			foreach ( $allsub as $subdir ) {
				if ( strpos( $subdir, '.' ) === false ) {
					$subdirs[] = $path . '/' . $subdir;
				}
			}

			if ( count( $subdirs ) ) {
				self::addIndexFiles( $subdirs );
			}

			$fpath = $path . '/index.html';
			if ( !file_exists( $fpath ) ) {
				file_put_contents( $fpath, '<!DOCTYPE html><title></title>' );
			}
		}

		return true;
	}
}
