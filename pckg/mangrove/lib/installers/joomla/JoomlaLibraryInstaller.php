<?php

class JoomlaLibraryInstaller extends JoomlaInstaller
{
	public function install()
	{
		$target = JPATH_ROOT . '/libraries/' . $this->package->info->name;

		return true;
	}
}
