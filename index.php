<?php

    require_once('cfg.class.php');
    require_once('pdo_sql.class.php');

    require_once('config.php');

    var_dump(
        pdo_sql::get_val('SELECT NOW()');
    );

    var_dump(
        pdo_sql::get_val('SELECT ?+?', array(2,3));
    );