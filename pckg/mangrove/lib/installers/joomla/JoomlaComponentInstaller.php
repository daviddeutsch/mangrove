<?php

class JoomlaComponentInstaller extends JoomlaInstaller
{
	public function afterInstall( $path )
	{
		parent::addIndexFiles( array($path) );

		self::canonizeAssets();
	}


}
