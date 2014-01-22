<?php

class ApplicationService extends AbstractService
{
	public function postSelect( $package )
	{
		// Mark a package for later install, possibly already downloading it
		// Do the same for required (child) packages
	}

	public function postInstall( $package )
	{
		// Actually do the install on main and child packages
	}
}
