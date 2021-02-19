<?php
declare(strict_types=1);

namespace ImportDrupalDb9\Core;

use ImportDrupalDb9\Core\Configure\Configure;
use ImportDrupalDb9\Core\Database\Query;
use PDO;
use Exception;

class Import extends Query {

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
	 * Método start
	 *
	 * @return 	void
	 */
	public function __construct()
	{
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
		$driverName 	= !empty( @$this->configDb['source']['driver'] )
			? ucfirst( strtolower( $this->configDb['source']['driver'] ) )
			: 'Mysql';
		$fullClassDriver= "ImportDrupalDb9\\Core\Database\\Driver\\" . $driverName;
		$this->sourceDb->driverDb = new $fullClassDriver;

		$targetDsn 		= "mysql:host={$this->configDb['target']['host']};dbname={$this->configDb['target']['database']};port={$this->configDb['target']['port']}";
		$this->targetDb = new PDO( $targetDsn, $this->configDb['target']['username'], $this->configDb['target']['password'] );
		$this->targetDb->setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION );
		$driverName 	= !empty( $this->configDb['target']['driver'] )
			? ucfirst( strtolower( $this->configDb['target']['driver'] ) ) 
			: 'Mysql';
		$fullClassDriver= "ImportDrupalDb9\\Core\Database\\Driver\\" . $driverName;
		$this->targetDb->driverDb = new $fullClassDriver;
	}

	/**
	 * Inicia a transação do banco.
	 *
	 * @return void
	 */
	private function begin()
	{
		return ( !$this->transactionCounter++ ) ? $this->targetDb->beginTransaction() : $this->transactionCounter >= 0;
	}

	/**
	 * Executa o commit do banco.
	 *
	 * @return mixed
	 */
	private function commit()
	{
		return (!--$this->transactionCounter) ? $this->targetDb->commit() : $this->transactionCounter >= 0;
	}

	/**
	 * Executa o rollback do banco.
	 *
	 * @return mixed
	 */
	private function rollback()
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
	 * Salva um lista de registros.
	 *
	 * @param 	array 	$arrFields 		
	 */
	public function saveAll( array $arrFields=[], string $tableName='' ) : bool
	{
		$this->begin();

		try 
		{
			$insert 	= false;
			$sqlInsert 	= "INSERT INTO $tableName (fields) VALUE (value)";
			$sqlUpdate 	= "UPDATE $tableName SET ";
			foreach( $arrFields as $_l => $_arrField )
			{

			}

			$sql = ( $insert ) ? $sqlInsert : $sqlUpdate;

			if ( $this->__get('logSql') ) { gravaLog( $sql, 'sql_'.$this->origin, 'a+' ); }

			#$this->targetDb->query( $sql );

		} catch ( Exception $e )
		{
			$this->rollback();
			$this->lastError = $e->getMessage();
			return false;
		}

		$this->commit();
		return true;
	}

	/**
	 * Método mágico get
	 *
	 * @param 	string 	$attribute 	Nome do atributo a ser retornado.
	 * @return 	mixed 	Valor do atributo.
	 */
	public function __get( $attribute='' )
	{
		return $this->$attribute;
	}

	/**
	 * Método mágico set
	 *
	 * @return 	void
	 */
	public function __set( $attribute='', $vlr=null )
	{
		$this->$attribute = $vlr;
	}
}