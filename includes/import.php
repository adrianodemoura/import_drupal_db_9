<?php
declare(strict_types=1);

try
{
	define( 'DIR_IMPORT_DB_9', str_replace( ['/includes', '/bin'], '', __DIR__ ) );

	include_once DIR_IMPORT_DB_9 . '/includes/bootstrap.php';

	if ( in_array( strtolower( isset($_SERVER['argv'][1])?$_SERVER['argv'][1]:''), ['--help', '-h', '-help'] ) ) { throw new Exception( 'printar ajuda', 191130); }

	$config = @ include_once DIR_IMPORT_DB_9 . '/config/config.php';

	if ( empty($config) ) { throw new Exception( "Não foi possível localizar o arquiv config/config.php" ); }

	$listaImportacao = @$config['import'];

	if ( empty($listaImportacao) ) { throw new Exception( "Nenhuma entidade foi configurada para importação. Verifique se a tag \"import\" foi informada no arquivo \"config/config.php\".", 2); }

	$comando = "mysqldump -u{$config['databases']['target']['username']} -p'{$config['databases']['target']['password']}' {$config['databases']['target']['database']} > ".DIR_IMPORT_DB_9."/bkp/".date('Y-m-d_H-i_s')."_dump_bd_9.sql";

	exec($comando);	

	/*foreach( $listaImportacao as $_l => $_class )
	{
		$fullClass 	= "ImportDrupalDb9\\Import\\" . ucfirst( strtolower( $_class ) ) . "Import";

		$Import 	= new $fullClass();

		$retorno 	= $Import->execute();

		gravaLog( $retorno, 'resultado_importacao_'.$_class );

		echo $retorno."\n";
	}*/

	//echo "success: ".count($listaImportacao)." entidade(s) importadas com sucesso. \n";
} catch ( Exception $e )
{
	switch ( $e->getCode() )
	{
		case 191130:
			include_once DIR_IMPORT_DB_9 . '/docs/help/help';
			break;
		
		default:
			echo "error: {$e->getMessage()} \n";
			break;
	}	
}
