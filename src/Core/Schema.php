<?php
declare(strict_types=1);

namespace ImportDrupalDb9\Core;

use ImportDrupalDb9\Core\Import\ImportMysql;
use Exception;

class Schema extends ImportMysql {

	/**
	 * Configura as relação de tabelas e seus campos
	 */
	public function create()
	{
		$lista = ['source'=>[], 'target'=>[] ];

		$this->setSource();

		$this->setTarget();

	}

	public function read(  )
	{
		return $retorno;
	}

	private function setSource()
	{
		$listTables = $this->allTables( 'source' );
		foreach( $listTables as $_l => $_table )
		{
			$lista['source'][$_table] = $this->describeTable( 'source', $_table );
		}
		logi( json_encode( $lista['source'] ), 'schema_source' );
	}

	private function setTarget()
	{
		$listTables = $this->allTables( 'target' );
		foreach( $listTables as $_l => $_table )
		{
			$lista['target'][$_table] = $this->describeTable( 'target', $_table );
		}
		logi( json_encode( $lista['target'] ), 'schema_target' );
	}

}