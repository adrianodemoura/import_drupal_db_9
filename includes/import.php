<?php
declare(strict_types=1);

try
{
	define( 'DIR_IMPORT_DB_9', str_replace( ['/includes', '/bin'], '', __DIR__ ) );

	include_once DIR_IMPORT_DB_9 . '/includes/bootstrap.php';

	$config = @include_once DIR_IMPORT_DB_9 . '/config/config.php';
	if ( empty($config) ) { throw new Exception( "Não foi possível localizar o arquiv config/config.php" ); }

	$listaImportacao = @$config['import'];
	if ( empty($listaImportacao) ) { throw new Exception( "Nenhuma entidade foi configurada para importação. Verifique se a tag \"import\" foi informada no arquivo \"config/config.php\".", 2); }

	include_once DIR_IMPORT_DB_9 . "/src/Core/Routes.php";

	include_once DIR_IMPORT_DB_9 . "/config/routes.php";

	// criando o schema de todas as tabelas do target e dou source
	$Schema = new ImportDrupalDb9\Core\Schema();
	$Schema->create();

	// importando item a item da lista de importação
	foreach( $listaImportacao as $_l => $_class )
	{
		$fullClass 	= "ImportDrupalDb9\\Import\\" . ucfirst( strtolower( $_class ) ) . "Import";

		$Import 	= new $fullClass();

		$retorno 	= $Import->execute();

		gravaLog( $retorno, 'resultado_importacao_'.$_class );

		echo $retorno."\n";
	}

	echo "\nfim: ".count($listaImportacao)." impotações executadas com sucesso.\n";
} catch ( Exception $e )
{
	switch ( $e->getCode() )
	{
		case 18001:
			include_once DIR_IMPORT_DB_9 . '/docs/help/tag';
			break;

		case 191130:
			include_once DIR_IMPORT_DB_9 . '/docs/help/help';
			break;

		case 191131:
			echo "Aguarde a importação do banco original ...\n";
			echo "mysql -u{$config['databases']['target']['username']} -p'{$config['databases']['target']['password']}' {$config['databases']['target']['database']} < " . TMP . "/bkp/dump9.sql";
			exec( $comando );
			echo "Importação executada com sucesso ...\n";
			break;

		case 191132:
			echo "Aguarde o backup ...\n";
			exec( "mysqldump -u{$config['databases']['target']['username']} -p'{$config['databases']['target']['password']}' {$config['databases']['target']['database']} > " . TMP . "/bkp/".date('Y-m-d_H-i_s')."_dump_{$config['databases']['target']['database']}.sql" );
			echo "Backup executado com sucesso ...\n";
			break;

		case 191133:
			echo "Aguarde o sceneamento de todas as tabelas do source e target \n";
			$Schema = new ImportDrupalDb9\Core\Schema();
			$Schema->execute();
			break;

		default:
			echo "error: {$e->getMessage()} \n";
			break;
	}	
}
