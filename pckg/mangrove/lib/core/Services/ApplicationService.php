<?php

class ApplicationService extends RestService
{
	public function getApplication()
	{
		// Return list of all packages with >5 dependencies for now
	}

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
