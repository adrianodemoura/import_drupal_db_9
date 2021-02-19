<?php
declare(strict_types=1);

namespace ImportDrupalDb9\Core\Database\Driver;

class Mysql {
	
	/**
	 * Monta a sql
	 *
	 * @param 	string 	$fields 	Campos da query
	 * @param 	string 	$tableName 	Nome da Tabela
	 * @param 	string  $where 		Filtro da query
	 * @param 	string 	$group 		Agrupamento
	 * @param 	string 	$order 		Ordem da query
	 * @param 	int 	$page 		Página.
	 * @param
	 */
	public function query( string $fields='*', string $tableName='', string $where='', string $group="", string $order="", int $page=0, int $range=10 ) : string
	{

		$limit 	= ( $page>0 ) 
			? $this->getLimit( $page, $range ) 
			: "";

		$query = "SELECT $fields from $tableName $where $group $order $limit";

		return trim( $query );
	}

	/**
	 * Retorna a paginação pra uma sql
	 *
	 * @param 	int 	$page 		Número da Página.
	 * @param 	int 	$range 		limite da página.
	 * @return 	string 	$limit 		String da paginação.
	 */
	public function getLimit( int $page=1, int $range=10 ) : string
	{
		$init 	= ($page * $range) -  $range;

		$limit 	= "LIMIT $init, $range";

		return $limit;
	}
}