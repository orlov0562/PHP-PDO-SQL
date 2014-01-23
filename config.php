<?php

    // Подготавливаем конфиг для класса pdo_sql

    cfg::i()->set('PDO_DRIVER', 'mysql');
    cfg::i()->set('PDO_HOST', 'localhost');
    cfg::i()->set('PDO_USER', 'mysql');
    cfg::i()->set('PDO_PASS', 'mysql');
    cfg::i()->set('PDO_DB', 'dbname');
    cfg::i()->set('PDO_ERRMODE', PDO::ERRMODE_EXCEPTION); // PDO::ERRMODE_SILENT; PDO::ERRMODE_EXCEPTION
    cfg::i()->set('SQL_SHOW_ERRORS', TRUE);
    cfg::i()->set('SQL_DIE_ON_ERRORS', TRUE);
