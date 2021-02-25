<?php

declare(strict_types=1);

if ( !defined('DS') ) 	define('DS', DIRECTORY_SEPARATOR );

if ( !defined('TMP') )
{
	$dirTmp = is_dir( DIR_IMPORT_DB_9 . '/tmp' ) ?  DIR_IMPORT_DB_9 . '/tmp' : sys_get_temp_dir();

	define('TMP', $dirTmp );
}

if ( !file_exists( DIR_IMPORT_DB_9 . '/vendor/autoload.php') )
{
	throw new Exception( "A Aplicação não possui \"/vendor/autoload.php\". Certifique-se que rodou o comando \"composer dump\" ", 2);
}

$config = @include_once DIR_IMPORT_DB_9 . '/config/config.php';
if ( empty($config) ) { throw new Exception( "Não foi possível localizar o arquiv config/config.php" ); }
