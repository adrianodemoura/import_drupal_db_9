<?php

namespace ImportDrupalDb9\Configure;

class Configure {

	public static function read( string $tag = '' ) : array
	{
		$config = include DIR_IMPORT_DB_9 . "/config/config.php";

		return isset( $config[ $tag ] ) ? $config[ $tag ] : [];
	}
}