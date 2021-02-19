<?php
declare(strict_types=1);

namespace ImportDrupalDb9\Import;

use ImportDrupalDb9\Core\Import;
use Exception;

class UserImport extends Import {

	protected $tableTargetName = 'user';

	protected $tableSourceName = 'users';
	
	public function execute()
	{
		$retorno = (object)['status'=>true, 'total'=>0, 'message'=>'sucesso'];

		$this->__set('logSql', true);

		$sourceDataUser = $this
			->where( ['uid >' => 0, 'uid <' => 100] )
			->findSource()
			->toArray();
		$totalUser = count( $sourceDataUser );

		$targetDataUser = $this->getTargetDataUser( $sourceDataUser );
		for( $i=0; $i<$totalUser; $i++ )
		{
			if ( !$this->saveAll( $targetDataUser, 'users_field_data' ) ) throw new Exception( $this->lastError );
		}

		return "$totalUser de usuários importados com sucesso.";
	}

	/**
	 */
	private function getTargetDataUser( array $sourceDataUser=[] )
	{
		if ( empty($sourceDataUser) ) return [];

		$targetDataUser = [];

		foreach( $sourceDataUser as $_l => $_arrUser )
		{
			//$targetDataUser[ $_l ]['uid'] = $_arrUser[]
		}

		return $targetDataUser;
	}
}