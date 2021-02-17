<?php
declare(strict_types=1);

namespace ImportDrupalDb9\Core\Database;
use ImportDrupalDb9\Core\Database\Where;

use PDO;

class Query {

	private $result = [];

	private $where 	= [];

	protected $logSql = true;

	private function getFields() : string
	{
		if ( empty($this->fields) )
		{
			$fields = "*";
		} else
		{
			$fields = implode(',', $this->fields );
		}

		return $fields;
	}

	private function getWhere() : string
	{
		return Where::convert( $this->where );
	}

	private function getGroup() : string
	{
		if ( empty($this->group) )
		{
			$group = "";
		} else
		{
			$group = " GROUP ".implode(',', $this->group );
		}

		return $group;
	}

	private function getOrder() : string
	{
		if ( empty($this->order) )
		{
			$order = "";
		} else
		{
			$order = " ORDER BY ".implode(',', $this->order );
		}

		return $order;
	}

	public function findSource()
	{
		$tableName 		= $this->configDb['source']['table_prefix'] . $this->tableSourceName;

		$fields 		= $this->getFields();

		$where 			= $this->getWhere();

		$group 			= $this->getGroup();

		$order 			= $this->getOrder();

		$sql 			= "SELECT $fields from $tableName $where $group $order";

		if ( $this->logSql ) { gravaLog( $sql, 'sql_source', 'a+' ); }

		$this->result 	= $this->sourceDb->query( $sql );

		return $this;
	}

	public function toArray() : array
	{
		return $this->result->fetchAll( PDO::FETCH_ASSOC );
	}

	public function where( array $where=[] )
	{
		$this->where = $where;

		return $this;
	}

	public function group( array $group=[] )
	{
		$this->group = $group;

		return $this;
	}

	public function order( array $order=[] )
	{
		$this->order = $order;

		return $this;
	}

}