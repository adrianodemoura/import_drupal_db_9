<?php
declare(strict_types=1);

namespace ImportDrupalDb9\Core\Schema;

use ImportDrupalDb9\Core\Import\ImportMysql;
use Exception;

class Schema extends ImportMysql {

	public function create()
	{
		echo "Aguarde o escaneamento de todas as tabelas do source e target \n";

		$this->setSource();

		$this->setTarget();

		echo "Escaneamento executado com sucesso. O Schema foi criado em \"" . DIR_IMPORT_DB_9 . "/tmp/schema\"  \n";
	}

	private function setSource()
	{
		$listTables = $this->allTables( 'source' );

		foreach( $listTables as $_l => $_table ) { $lista['source'][$_table] = $this->describeTable( 'source', $_table ); }

		$this->writeSchema( $lista['source'], 'schema_source' );
	}

	private function setTarget()
	{
		$listTables = $this->allTables( 'target' );

		foreach( $listTables as $_l => $_table ) { $lista['target'][$_table] = $this->describeTable( 'target', $_table ); }

		$this->writeSchema( $lista['target'] , 'schema_target' );
	}

	private function writeSchema( $content=[], $name='' )
	{
		$fp = fopen( TMP . DS . 'schema' . DS . $name.'.json', 'w' );

        ob_start();

        echo print_r( json_encode( $content ), true);

        $saida = ob_get_clean();

        fwrite($fp, $saida.PHP_EOL);

        fclose($fp);
	}

}