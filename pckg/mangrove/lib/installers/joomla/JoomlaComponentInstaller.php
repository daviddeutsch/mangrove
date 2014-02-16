<?php

class JoomlaComponentInstaller extends JoomlaInstaller
{
	public function afterInstall()
	{
		$this->canonizeAssets( $this->package->info->joomla->name );

		parent::afterInstall();
	}

}
