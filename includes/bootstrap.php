<?php

declare(strict_types=1);

// diretório do drupal 9
if ( strpos( DIR_IMPORT_DB_9, 'web/modules/contrib/modulo_import_db_9') > -1)
{
	define( 'DIR_DRUPAL9', str_replace(['web/', 'modules/', 'contrib/', 'import_db_9'], '', DIR_IMPORT_DB_9 ) );
} else
{
	define( 'DIR_DRUPAL9', '.' );
}

if ( !defined('DS') ) 	define('DS', DIRECTORY_SEPARATOR );

// temporário do drupal
if ( defined('DRUPAL_ROOT') ) define('TMP', DRUPAL_ROOT . '/tmp' );

// diretório temporário
if ( !defined('TMP') )
{
	$dirTmp = is_dir( DIR_IMPORT_DB_9 . '/tmp' ) ?  DIR_IMPORT_DB_9 . '/tmp' : sys_get_temp_dir();

	define('TMP', $dirTmp );
}

if ( !file_exists( DIR_DRUPAL9 . '/vendor/autoload.php') )
{
	throw new Exception( "A Aplicação não possui \"/vendor/autoload.php\". Certifique-se que rodou o comando \"composer dump\" ", 2);
}

include_once DIR_DRUPAL9 . '/vendor/autoload.php';

include_once DIR_IMPORT_DB_9 . '/includes/global.php';