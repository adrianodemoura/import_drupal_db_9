<?php
declare(strict_types=1);

namespace ImportDrupalDb9\Core\Database;

class Where {

	public static function convert( array $where=[] ) : string
	{
		if ( empty($where) ) { return ""; }

		$_where = "";

		foreach($where as $_field => $_vlr )
		{
			$operator 	= "=";

			$field 		= trim( str_replace(['=','<','>','<=', '>='], '', $_field ) );

			$operator 	= ( strpos($_field, '>') > -1 ) 	? ">" 	: $operator;

			$operator 	= ( strpos($_field, '<') > -1 ) 	? "<" 	: $operator;

			$operator 	= ( strpos($_field, '<=') > -1 ) 	? "<=" 	: $operator;

			$operator 	= ( strpos($_field, '>=') > -1 ) 	? ">=" 	: $operator;

			if ( strlen($_where) > 1 ) { $_where .= " AND "; }

			$_where .= $field." ".$operator." ".$_vlr;
		}

		return "WHERE $_where";
	}
}