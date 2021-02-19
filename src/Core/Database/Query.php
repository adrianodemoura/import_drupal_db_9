<?php
declare(strict_types=1);

namespace ImportDrupalDb9\Core\Database;
use ImportDrupalDb9\Core\Database\Where;

use PDO;

class Query {

	private $result = [];

	private $where 	= [];

	private $page 	= 0;

	private $range 	= 10;

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
		return !empty($this->where) ? "WHERE ".Where::convert( $this->where ) : "";
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
		$this->origin = 'source';

		return $this->find();
	}

	public function findTarget() 
	{
		$this->origin = 'target';

		return $this->find();
	}

	public function find()
	{
		$tableName 		= $this->configDb[$this->origin]['table_prefix'] 
			. ( ($this->origin==='source') ? $this->tableSourceName : $this->tableTargetName );

		$fields 		= $this->getFields();

		$where 			= $this->getWhere();

		$group 			= $this->getGroup();

		$order 			= $this->getOrder();

		$originDb 		= $this->origin."Db";

		$sql 			= $this->$originDb->driverDb->query( $fields, $tableName, $where, $group, $order, $this->page, $this->range );

		if ( $this->__get('logSql') ) { gravaLog( $sql, 'sql_'.$this->origin, 'a+' ); }

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

	public function page( int $page=1 )
	{
		$this->page = $page;

		return $this;
	}

	public function range( int $range=10 )
	{
		$this->range = $range;

		return $this;
	}

}