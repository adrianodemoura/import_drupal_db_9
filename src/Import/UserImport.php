<?php
declare(strict_types=1);

namespace ImportDrupalDb9\Import;

use ImportDrupalDb9\Core\Import\ImportMysql;

class UserImport extends ImportMysql {
	/**
	 * Pode limpar os usuários do target ?
	 *
	 * @var 	boolean
	 */
	private $cleanTarget = true;

	/**
	 * Executa a importação dos usuários
	 *
	 * @return 	string 	$msg 	Mensagem de estatus.
	 */
	public function execute() : string
	{
		$this->__set('logSql', true );

		$msg 	= "{x} usuários importados com sucesso.";

		if ( $this->cleanTarget ) $this->cleanTarget();

		$res 	= $this->db('source')->query( $this->getSourceSqlUsers() )->toArray();

		$msg 	= str_replace( "{x}", count($res), $msg );

		return $msg;
	}

	/**
	 * Limpa os usuário do target
	 *
	 * @return 	void
	 */
	private function cleanTarget()
	{
		if ( !$this->cleanTarget ) return false;

		$targetTablePrefix = $this->configDb['target']['table_prefix'];

		$totalUsuarios = @$this->db('target')
			->query( "SELECT COUNT(1) as total_usuarios FROM {$targetTablePrefix}users_field_data" )
			->toArray()['total_usuarios'];

		if ( $totalUsuarios )
		{
			$sql = "DELETE FROM {$targetTablePrefix}users_field_data WHERE uid>0";

			$res = $this->db('target')->query( $sql );

			echo "{$totalUsuarios} usuários excluídos com sucesso ... ";
		}
	}

	/**
	 * Retorna a sql que recuperar todos os uusuários do banco de origem.
	 *
	 * @return 	string 	$sql 	$sql de usuários
	 */
	private function getSourceSqlUsers() : string
	{
		$sourceTablePrefix = $this->configDb['source']['table_prefix'];

		$sql = "SELECT * FROM {$sourceTablePrefix}users WHERE uid>0 AND uid<10";
		$sql .= "";

		return $sql;
	}
}