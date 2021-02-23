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

		if ( $this->cleanTarget ) $this->cleanTarget();

		$totalUsuariosImportados = $this->importUsers();

		return "{$totalUsuariosImportados} importados com sucesso.\n";
	}

	/**
	 * Executa a importação dos usuários
	 *
	 * @return 	int 	$totalUsuarios 	Total de usuáriso importados.
	 */
	private function importUsers() : int
	{
		$res 	= $this->db('source')->query( $this->getSourceSqlUsers() )->toArray();

		return count( $res );
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

		$totalUsuarios = @$this->db('target')->query( "SELECT COUNT(1) as total_usuarios FROM {$targetTablePrefix}users_field_data" )->toArray()['total_usuarios'];

		$listaTabelasUsuario = ['users_field_data', 'users'];

		foreach( $listaTabelasUsuario as $_l => $_tabela )
		{
			$sql = "DELETE FROM {$targetTablePrefix}{$_tabela} WHERE uid>0";
			$res = $this->db('target')->query( $sql );
		}

		if ( $totalUsuarios ) echo "{$totalUsuarios} usuários excluídos com sucesso ...\n";
	}

	/**
	 * Retorna a sql que recuperar todos os uusuários do banco de origem.
	 *
	 * @return 	string 	$sql 	$sql de usuários
	 */
	private function getSourceSqlUsers() : string
	{
		$sourceTablePrefix = $this->configDb['source']['table_prefix'];

		$sql  = "SELECT * FROM {$sourceTablePrefix}users u";
		$sql .= " LEFT JOIN {$sourceTablePrefix}users_roles ur  ON ur.uid  = u.uid";
		$sql .= " LEFT JOIN {$sourceTablePrefix}userprotect upr ON upr.uid = u.uid";
		$sql .= " WHERE u.uid>0 AND u.uid<100";

		return $sql;
	}
}