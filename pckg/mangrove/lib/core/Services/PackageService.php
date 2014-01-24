<?php

class PackageService extends RestService
{
	/**
	 * Enter a package into our database, readying it for download
	 *
	 * @param string $name Package path name
	 */
	public function getSelect( $name )
	{
		$this->updateStatus($name, 'selected');
	}

	/**
	 * Download package to our server
	 *
	 * @param string $name Package path name
	 */
	public function getDownload( $name )
	{
		$this->updateStatus($name, 'download');
	}

	/**
	 * Install a package
	 *
	 * @param string $name Package path name
	 */
	public function getInstall( $name )
	{
		$this->updateStatus($name, 'install');
	}

	/**
	 * Utility function to quickly change a packages status
	 *
	 * @param string $name   Package path name
	 * @param string $status Status code
	 */
	private function updateStatus( $name, $status )
	{
		$package = MangroveApp::$r->x->one->package->name($name)->find();

		$package->status = $status;

		MangroveApp::$r->_($package);
	}
}
