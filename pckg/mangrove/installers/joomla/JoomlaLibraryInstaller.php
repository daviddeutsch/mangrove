<?php

class JoomlaLibraryInstaller extends JoomlaInstaller
{
	public function install( $path )
	{
		// Copy media files to library directory, if they don't exist yet

		return true;
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
