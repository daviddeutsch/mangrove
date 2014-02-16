<?php

class JoomlaComponentInstaller extends JoomlaInstaller
{
	public function afterInstall()
	{
		print_r($this);exit;

		$this->canonizeAssets( $this->package->info->joomla->name );

		parent::afterInstall();
	}

}
