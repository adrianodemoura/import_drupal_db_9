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
	 * Executa a importação dos usuários.
	 *
	 * @return 	void
	 */
	public function execute()
	{
		$this->__set('logSql', true );

		$this->db('target')->begin();
		try 
		{
			if ( $this->cleanTarget ) $this->cleanTarget();
		
			$totalUsuariosImportados = $this->importUsers();

			$this->db('target')->commit();

			echo "{$totalUsuariosImportados} usuários importados com sucesso. Não esqueça de limpar o cache no Drupal 9\n";
		} catch ( Exception $e )
		{
			$this->db('target')->rollback();
			echo "{$e->getCode()} - {$e->getMessage()}";
		}
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

		$uidJaIncluso 		= [0];

		$totalSalvos 		= 0;

		foreach( $sourceUsers as $_l => $_arrFields )
		{
			if ( in_array( $_arrFields['uid'], $uidJaIncluso) ) continue;

			$uidJaIncluso[] = $_arrFields['uid'];

			// inserindo o usuário
			$sqlInsert = "INSERT INTO {$targetTablePrefix}users";
			$sqlInsert .= " ( uid, uuid, langcode ) VALUE";
			$sqlInsert .= " ( {$_arrFields['uid']}, {$_arrFields['uid']}, '{$_arrFields['language']}' )";
			$res = $this->db('target')->query( $sqlInsert );

			// inserindo users_field_data
			$sqlInsert = "INSERT INTO {$targetTablePrefix}users_field_data";
			$sqlInsert .= " ( uid, access, changed, created, default_langcode
				, init, langcode, login
				, mail, name
				, pass
				, preferred_langcode, status, timezone ) VALUE";
			$sqlInsert .= " ( {$_arrFields['uid']}
				, {$_arrFields['access']}, {$_arrFields['created']}, {$_arrFields['created']}, 1
				, '{$_arrFields['init']}', 'pt-br', '{$_arrFields['login']}'
				, '{$_arrFields['mail']}', '{$_arrFields['name']}'
				, '{$_arrFields['pass']}'
				, '{$_arrFields['language']}', {$_arrFields['status']}, '{$_arrFields['timezone']}' )";
			$res = $this->db('target')->query( $sqlInsert );

			if ( $this->__get('verbose') ) echo "inserindo usuário id: {$_arrFields['uid']} \n";

			$totalSalvos++;
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

		$targetTablePrefix 	= $this->configDb['target']['table_prefix'];

		$totalUsuarios 		= @$this->db('target')->query( "SELECT COUNT(1) as total_usuarios FROM {$targetTablePrefix}users_field_data" )->toArray()['total_usuarios'];

		$listaTabelasUsuario= ['users_field_data', 'users'];

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

		$sql  = "SELECT
					tu.uid,
					tu.name,
					tu.pass,
					tu.mail,
					tu.login,
					tu.created,
					tu.access,
					tu.status,
					tu.timezone,
					tu.language,
					tu.init,
					tr.name as name_role,
					tr.rid,
					tr.weight 
				FROM
					tb_users tu
				LEFT JOIN tb_userprotect tup ON tup.uid = tu.uid
				LEFT JOIN tb_users_roles tur ON tur.uid = tu.uid
				LEFT JOIN tb_role tr ON tr.rid = tur.rid";
		//$sql .= " WHERE tu.uid>0 AND tu.uid<100";

		return $sql;
	}
}