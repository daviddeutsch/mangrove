<?php

class JoomlaComponentInstaller extends JoomlaInstaller
{
	public function afterInstall( $path )
	{
		parent::addIndexFiles( array($path) );

		$this->canonizeAssets( $this->package->info->joomla->name );
	}

}
