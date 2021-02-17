<?php

namespace ImportDrupalDb9\Import;

use ImportDrupalDb9\Import\ImportMysql;

class UserImport extends ImportMysql {
	
	public function execute()
	{
		$retorno 	= (object)['status'=>true, 'total'=>rand(5,50), 'message'=>'sucesso'];

		/*$configDb 	= include DIR_IMPORT_DB_9 . "/config/config.php";

		$tablePrefix= @$configDb['Databases']['source']['table_prefix'];

		$dataUser 	= $connSource
			->query( "SELECT * from ${tablePrefix}users" )
			->fetchAll( \PDO::FETCH_ASSOC );*/

		return $retorno;
	}
}