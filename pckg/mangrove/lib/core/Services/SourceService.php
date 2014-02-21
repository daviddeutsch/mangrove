<?php

class SourceService extends RestService
{
	public function postAuthenticate()
	{
		// Send passphrase to server, store response as status
		// If authenticated, also load projects, branches, packages
	}

	public function getProjects()
	{
		// Load project+branch list
	}
}
