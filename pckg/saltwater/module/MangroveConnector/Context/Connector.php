<?php

namespace MangroveConnector\Context;

use Saltwater\Server as S;
use Saltwater\Thing\Context;
use \RedBean_OODBBean as Bean;

class Connector extends Context
{
	public function pushData( $data )
	{
		if ( $data instanceof Bean ) {
			if ( $data->getMeta('type') == 'source' ) {
				S::$n->pointer->setPointer($data->id);
			}
		}
	}

	public function getInfo()
	{
		return S::$n->pointer->getPointer();
	}
}
