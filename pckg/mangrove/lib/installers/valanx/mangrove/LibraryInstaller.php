<?php

class LibraryInstaller extends MangroveInstaller
{
	public function install()
	{
		parent::install();

		$target = JPATH_ROOT . '/libraries/' . $this->package->info->name;

		if ( !is_dir($target) ) mkdir($target, 0744, true);

		rename( $this->package->source, $target );
	}
}
