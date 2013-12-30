<?php

class JoomlaLibraryInstaller extends JoomlaInstaller
{
	public function install( $path )
	{
		$target = JPATH_ROOT . '/libraries/' . $this->info->name;

		return true;
	}
}
