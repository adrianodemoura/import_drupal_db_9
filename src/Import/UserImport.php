<?php
declare(strict_types=1);

namespace ImportDrupalDb9\Import;

use ImportDrupalDb9\Core\Import\ImportMysql;
use ImportDrupalDb9\Core\Configure\Configure;

class UserImport extends ImportMysql {

	protected $tableTargetName = 'user';

	protected $tableSourceName = 'users';
	
	public function execute()
	{
		$retorno 		= (object)['status'=>true, 'total'=>rand(5,50), 'message'=>'sucesso'];

		//$this->__set('logSql', true);

		$dataUser = $this
			->where( ['uid >' => 0] )
			->findSource()
			->toArray();

		$retorno->total = count( $dataUser );

		return $retorno;
	}
}