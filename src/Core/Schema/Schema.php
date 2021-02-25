<?php
declare(strict_types=1);

namespace ImportDrupalDb9\Core\Schema;

use ImportDrupalDb9\Core\Import\ImportMysql;
use Exception;

class Schema extends ImportMysql {

	/**
	 * Configura as relação de tabelas e seus campos
	 */
	public function create()
	{
		echo "Aguarde o escaneamento de todas as tabelas do source e target \n";

		$lista = ['source'=>[], 'target'=>[] ];

		$this->setSource();

		$this->setTarget();

		echo "Escaneamento executado com sucesso. O Schema foi criado em \"" . DIR_IMPORT_DB_9 . "/tmp\"  \n";
	}

	private function setSource()
	{
		$listTables = $this->allTables( 'source' );
		foreach( $listTables as $_l => $_table )
		{
			$lista['source'][$_table] = $this->describeTable( 'source', $_table );
		}
		gravaLog( json_encode( $lista['source'] ), 'schema_source' );
	}

	private function setTarget()
	{
		$listTables = $this->allTables( 'target' );
		foreach( $listTables as $_l => $_table )
		{
			$lista['target'][$_table] = $this->describeTable( 'target', $_table );
		}
		gravaLog( json_encode( $lista['target'] ), 'schema_target' );
	}

}