<?php
declare(strict_types=1);

namespace ImportDrupalDb9\Import;

use ImportDrupalDb9\Core\Import\ImportMysql;

class UserImport extends ImportMysql {

	/**
	 * Executa a importação dos usuários
	 *
	 * @return 	string 	$msg 	Mensagem de estatus.
	 */
	public function execute()
	{
		$msg 	= "x usuários importados com sucesso.";

		$res 	= $this->sourceDb->query( $this->getSourceSqlUsers() )->fetchAll(  );

		dump( $res );

		return $msg;
	}

	/**
	 * Retorna a sql que recuperar todos os uusuários do banco de origem.
	 *
	 * @return 	string 	$sql 	$sql de usuários
	 */
	private function getSourceSqlUsers() : string
	{
		$sourceTablePrefix 	= $this->configDb['source']['table_prefix'];

		$sql = "SELECT * FROM {$sourceTablePrefix}users";
		$sql .= "";

		return $sql;
	}
}