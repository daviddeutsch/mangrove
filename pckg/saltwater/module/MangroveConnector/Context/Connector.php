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
				$db = S::$n->db;

				$connection = $db->x->connection->url($data->url)->find();

				if ( !empty($connection->id) ) {
					S::$n->pointer->setPointer($data->id);
				}


			}
		}
	}

	public function getInfo()
	{
		$pointer = S::$n->pointer->getPointer();

		if ( empty($pointer) ) {
			return (object) array (
				'status' => null
			);
		} else {
			return ;
		}

	}
}
