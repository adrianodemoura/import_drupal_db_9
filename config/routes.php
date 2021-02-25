<?php

if ( in_array( strtolower( isset($_SERVER['argv'][1])?$_SERVER['argv'][1]:''), ['--tag', '-t', '-tag'] ) ) { throw new Exception( 'printar sua doca aqui ajuda', 18001); }

