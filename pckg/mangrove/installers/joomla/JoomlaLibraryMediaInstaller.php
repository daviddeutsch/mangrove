<?php

class JoomlaLibraryMediaInstaller extends JoomlaLibraryInstaller
{
	public function afterInstall( $path )
	{
		// Copy files from library folder to a canonical component directory
		return self::addIndexFiles( array($path) );
	}

}
