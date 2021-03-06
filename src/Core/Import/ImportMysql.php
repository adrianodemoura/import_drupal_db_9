<?php
declare(strict_types=1);

namespace ImportDrupalDb9\Core\Import;

use ImportDrupalDb9\Core\Configure\Configure;
use Exception;
use PDO;

class ImportMysql {

	/**
	 * Configuraçãos do banco de dados, de origem e destino.
	 *
	 * @var 	array
	 */
	protected $configDb = ['source'=> [], 'target'=>[] ];

	/**
	 * Conexão com o banco de origem.
	 *
	 * @var 	Object
	 */
	protected $sourceDb = [];

	/**
	 * Conexão com o banco de destino.
	 *
	 * @var 	Object
	 */
	protected $targetDb = [];

	/**
	 * Banco selecionado, source ou target.
	 *
	 * @var 	string
	 */
	protected $selectDb = 'source';

	/**
	 * Resultado da última query.
	 *
	 * @var 	mixed
	 */
	protected $result 	= null;

	/**
	 * Se true, grava no temporaŕio todas as sqls, executadas.
	 *
	 * @var 	boolean
	 */
	private $logSql 	= false;

	/**
	 * Último erro
	 *
	 * @var 	string
	 */
	protected $lastError= "";

	/**
	 * contador de transcações
	 *
	 * @var 	int
	 */
	protected $transactionCounter = 0;

	/**
	 * modo verbose, printa todos os comandos.
	 *
	 * @var 	boolean
	 */
	private $verbose = 0;

	/**
	 * Método start
	 *
	 * @return 	void
	 */
	public function __construct()
	{
		if ( in_array( '--verbose', $_SERVER['argv'] ) )
		{
			$this->__set('verbose', 1);
		}

		$this->connect();
	}

	/**
	 * método de finalização
	 *
	 * @return 	void
	 */
	public function __destruct()
	{
		$this->sourceDb = null;
		$this->targetDb = null;
	}

	/**
	 * Inicia a transação do banco.
	 *
	 * @return void
	 */
	protected function begin()
	{
		return ( !$this->transactionCounter++ ) ? $this->targetDb->beginTransaction() : $this->transactionCounter >= 0;
	}

	/**
	 * Executa o commit do banco.
	 *
	 * @return mixed
	 */
	protected function commit()
	{
		return (!--$this->transactionCounter) ? $this->targetDb->commit() : $this->transactionCounter >= 0;
	}

	/**
	 * Executa o rollback do banco.
	 *
	 * @return mixed
	 */
	protected function rollback()
	{
		if ( $this->transactionCounter >= 0 )
        {
            $this->transactionCounter = 0;
            return $this->targetDb->rollback();
        }

        $this->transactionCounter = 0;
        return false;
	}

	/**
	 * Método de conexão.
	 *
	 * @return 	void
	 */
	private function connect()
	{
		$configDb		= Configure::read('databases');

		$this->configDb['source'] = $configDb['source'];

		$this->configDb['target'] = $configDb['target'];

		$sourceDsn 		= "mysql:host={$this->configDb['source']['host']};dbname={$this->configDb['source']['database']};port={$this->configDb['source']['port']}";
		$this->sourceDb = new PDO( $sourceDsn, $this->configDb['source']['username'], $this->configDb['source']['password'] );
		$this->sourceDb->setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION );

		$targetDsn 		= "mysql:host={$this->configDb['target']['host']};dbname={$this->configDb['target']['database']};port={$this->configDb['target']['port']}";
		$this->targetDb = new PDO( $targetDsn, $this->configDb['target']['username'], $this->configDb['target']['password'] );
		$this->targetDb->setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION );
	}

	/**
	 * Método mágico get
	 *
	 * @param 	string 	$attribute 	Nome do atributo a ser retornado.
	 * @return 	mixed 	Valor do atributo.
	 */
	public function __get( string $attribute='' )
	{
		return $this->$attribute;
	}

	/**
	 * Método mágico set
	 *
	 * @return 	void
	 */
	public function __set( string $attribute='', $vlr=null )
	{
		$this->$attribute = $vlr;
	}

	/**
	 * Executa a ação de importação.
	 *
	 * @return 	string 	$msg 	Mensagem da operação.
	 */
	public function execute()
	{
		//
	}

	/**
	 * Configura a última conexão usada.
	 *
	 * @param 	string 	$db 	Nome da conexão, source ou target.
	 * @return 	this
	 */
	public function db( string $db='source' )
	{
		if ( !in_array( $db, ['source', 'target'] ) )
		{
			dump("Erro ao selecionar database", true );
			throw new Exception( "Database inválido !!!", 1);
		}

		$this->selectDb = $db;

		return $this;
	}

	/**
	 * Executa um query, no banco source ou target.
	 *
	 * @param 	string 	$sql 	Sql a ser executada.
	 * @return 	this
	 */
	public function query( string $sql="" )
	{
		//if ( $this->logSql ) gravaLog( date("Y-m-d H:i:s") . " " . " " . str_replace("\n", "", $sql), 'sql_'.$this->selectDb, 'a+' );
		if ( $this->logSql ) gravaLog( date("Y-m-d H:i:s") . " " . $sql, 'sql_'.$this->selectDb, 'a+' );

		if ( $this->selectDb === 'source' )
		{
			$this->result = $this->sourceDb->query( $sql );
		} elseif ( $this->selectDb === 'target' )
		{
			$this->result = $this->targetDb->query( $sql );
		}

		return $this;
	}

	/**
	 * Retorna o resultado de uma query
	 *
	 * @return 	array 	array 	Resultado da query em array;
	 */
	public function toArray() : array
	{
		return @$this->result->fetchAll( PDO::FETCH_ASSOC );
	}

	/**
	 * Retorna a lista de todas as tabels do banco de dados.
	 *
	 * @return 	array $listTables 	Lista das tabelas.
	 */
	public function allTables( string $db='source' ) : array
	{
		$this->db( $db )->query( "SHOW TABLES" );
		 
		return $this->result->fetchAll( PDO::FETCH_COLUMN );
	}

	/**
	 * Retorna a as propriedades de cada campo de uma tabela.
	 *
	 * @param 	string 	$db 		Origem do banco, source ou target.
	 * @param 	string 	$table 		Nome da tabela
	 * @return 	array 	$fields 	Matriz com todos as propriedades dde cada campo.
	 */
	public function describeTable ( string $db='source', string $table='' ) : array
	{
		$_listFields 	= $this->db( $db )->query( "DESCRIBE $table" )->toArray();

		$fields 		= [];

		foreach( $_listFields as $_l => $_arrProp )
		{
			$fieldName = @$_arrProp['Field'];

			$fields[ $fieldName ]['name'] 	= $fieldName;
			$fields[ $fieldName ]['type'] 	= @$_arrProp['Type'];
			$fields[ $fieldName ]['null'] 	= @$_arrProp['Null'];
			$fields[ $fieldName ]['key'] 	= @$_arrProp['Key'];
			$fields[ $fieldName ]['default']= @$_arrProp['Default'];
			$fields[ $fieldName ]['extra']  = @$_arrProp['Extra'];
		}

		return $fields;
	}
}