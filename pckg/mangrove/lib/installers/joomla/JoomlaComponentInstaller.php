<?php

class JoomlaComponentInstaller extends JoomlaInstaller
{
	public function afterInstall()
	{
		// TODO:
		// parent::addIndexFiles( array($path) );

		$this->canonizeAssets( $this->package->info->joomla->name );
	}

}
