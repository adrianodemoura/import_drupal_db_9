<?php
declare(strict_types=1);

namespace ImportDrupalDb9\Core\Import;

use ImportDrupalDb9\Core\Configure\Configure;
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
}