<?php
declare(strict_types=1);

namespace ImportDrupalDb9\Import;

use ImportDrupalDb9\Core\Import\ImportMysql;
use Exception;

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

		return "{$totalUsuariosImportados} usuários importados com sucesso.\n";
	}

	/**
	 * Executa a importação dos usuários
	 *
	 * @return 	int 	$totalUsuarios 	Total de usuáriso importados.
	 */
	private function importUsers() : int
	{
		$sourceUsers 		= $this->db('source')->query( $this->getSourceSqlUsers() )->toArray();

		$targetTablePrefix 	= $this->configDb['target']['table_prefix'];

		$uidJaIncluso 		= [];

		$totalSalvos 		= 0;

		$senhaPadrao 		= '$S$ECpkmLmTDrOnrL6u0aJb8f2amHuXkQKW0SIqOKQwxYQ6nsFO92lW';
		// AdminDrupal9_6701! $S$E26OJxEl2d6TIpG1WLqB78VtXaFfuvOPoBADkUeVV3FxQ44kYf2A
		// sgt219 $S$DBhGJs6wx.ImKwG/cG0vgv3xHgROPvhURS6E.es3yX5RZwLpSNub
		// MudarSenha9_6701! $S$ECpkmLmTDrOnrL6u0aJb8f2amHuXkQKW0SIqOKQwxYQ6nsFO92lW

		foreach( $sourceUsers as $_l => $_arrFields )
		{
			if ( in_array( $_arrFields['uid'], $uidJaIncluso) ) continue;

			$uidJaIncluso[] = $_arrFields['uid'];

			try
			{
				// inserindo o usuário
				$sqlInsert = "INSERT INTO {$targetTablePrefix}users";
				$sqlInsert .= " ( uid, uuid, langcode ) VALUE";
				$sqlInsert .= " ( {$_arrFields['uid']}, {$_arrFields['uid']}, '{$_arrFields['language']}' )";
				$res = $this->db('target')->query( $sqlInsert );

				// inserindo users_field_data
				$sqlInsert = "INSERT INTO {$targetTablePrefix}users_field_data";
				$sqlInsert .= " ( uid
					, access
					, changed
					, created
					, default_langcode
					, init
					, langcode
					, login
					, mail
					, name
					, pass
					, preferred_langcode
					, status
					, timezone ) VALUE";
				$sqlInsert .= " ( {$_arrFields['uid']}
					, {$_arrFields['access']}
					, {$_arrFields['created']}
					, {$_arrFields['created']}
					, 1
					, '{$_arrFields['init']}'
					, 'pt-br'
					, '{$_arrFields['login']}'
					, '{$_arrFields['mail']}'
					, '{$_arrFields['name']}'
					, '{$senhaPadrao}'
					, '{$_arrFields['language']}'
					, {$_arrFields['status']}
					, '{$_arrFields['timezone']}' )";
				$res = $this->db('target')->query( $sqlInsert );

				// inserindo o user__roles

				// inserindo o user__user_picture

				// inserindo users_data

				$totalSalvos++;
			} catch ( Exception $e )
			{
				echo "{$e->getMessage()}\n";
			}
		}

		return $totalSalvos;
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

		$sql  = "SELECT u.uid, u.name, u.pass, u.mail, u.login, u.created, u.access, u.status, u.timezone, u.language, u.init";
		$sql .= " FROM {$sourceTablePrefix}users u";
		$sql .= " LEFT JOIN {$sourceTablePrefix}users_roles ur  ON ur.uid  = u.uid";
		$sql .= " LEFT JOIN {$sourceTablePrefix}userprotect upr ON upr.uid = u.uid";
		$sql .= " WHERE u.uid>0 AND u.uid<100";

		return $sql;
	}
}