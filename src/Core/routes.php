<?php

if ( in_array( strtolower( isset($_SERVER['argv'][1])?$_SERVER['argv'][1]:''), ['--help', '-h', '-help'] ) ) { throw new Exception( 'printar ajuda', 191130); }

if ( in_array( strtolower( isset($_SERVER['argv'][1])?$_SERVER['argv'][1]:''), ['--bd','--restore',  '--banco', '-database'] ) ) { throw new Exception( 'recuperar banco original do target', 191131); }

if ( in_array( strtolower( isset($_SERVER['argv'][1])?$_SERVER['argv'][1]:''), ['--bkp','--backup'] ) ) { throw new Exception( 'executar backup do banco original do target', 191132); }

if ( in_array( strtolower( isset($_SERVER['argv'][1])?$_SERVER['argv'][1]:''), ['--sc','--schema'] ) ) { throw new Exception( 'criar schema de todas as tabelas', 191133); }