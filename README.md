# Importação DB 9
Script para importação de banco de dados para o Drupal 9

### Instalação
```
$ composer require adrianodemoura/import_drupal_db_9
```

### Executar a Importação
```
$ bin/import
```

### Ajuda
```
$ bin/import --help
```

### Recuperar banco original
```
$ bin/import --restore
* Necessário que arquivo `dump9.sql`, esteja no direorio `bkp`.
