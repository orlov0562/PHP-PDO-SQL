<?php

/*******************************************************
 *
 *  Config example:
 *  cfg::i()->set('PDO_DRIVER', 'mysql');
 *  cfg::i()->set('PDO_HOST', 'localhost');
 *  cfg::i()->set('PDO_USER', 'mysql');
 *  cfg::i()->set('PDO_PASS', 'mysql');
 *  cfg::i()->set('PDO_DB', 'database');
 *  cfg::i()->set('PDO_ERRMODE', PDO::ERRMODE_SILENT);
 *  cfg::i()->set('SQL_SHOW_ERRORS', TRUE);
 *
 *******************************************************/

class pdo_sql
{
    private static $instance;

    public static function instance(array $params)
    {
       if(!isset(self::$instance))
       {
           self::$instance = new PDO(
              $params['dsn'],
              $params['user'],
              $params['pass']
           );
       }
       return self::$instance;
    }

    public static function i()
    {
       $params = array(
            'dsn'=> sprintf(
                        '%s:dbname=%s;host=%s;',
                        cfg::i()->get('PDO_DRIVER'),
                        cfg::i()->get('PDO_DB'),
                        cfg::i()->get('PDO_HOST')
            ),
            'user'=>cfg::i()->get('PDO_USER'),
            'pass'=>cfg::i()->get('PDO_PASS'),
       );

       $instance = self::instance($params);

       if ($mode = cfg::i()->get('PDO_ERRMODE'))
       {
           $instance->setAttribute(
            PDO::ATTR_ERRMODE,
            $mode
           );
       }

       return $instance;
    }

    private function __construct()
    {
        //not allowed with singleton.
    }

    /*****************************************************/

    public static function query($sql, array $vars=array())
    {
        $db = self::i();
        $obj = $db->prepare($sql);
        $obj->execute($vars);
        if (is_object($obj) and $obj->errorCode() == '00000')
        {
            $ret = $obj->rowCount();
        }
        else
        {
            self::show_errors($obj->errorInfo(), $sql, $vars);
            $ret = null;
        }
        return $ret;
    }

    public static function get_results($sql, array $vars=array())
    {
        $ret = array();
        $db = self::i();
        $obj = $db->prepare($sql);
        $obj->execute($vars);
        if (is_object($obj) and $obj->errorCode() == '00000')
        {
            $ret = $obj->fetchAll();
        } else {
            self::show_errors($obj->errorInfo(), $sql, $vars);
            $ret = null;
        }
        return $ret;
    }

    public static function get_row($sql, array $vars=array())
    {

        $db = self::i();
        $obj = $db->prepare($sql);
        $obj->execute($vars);

        if (is_object($obj) and $obj->errorCode() == '00000')
        {
            $ret = $obj->fetch(PDO::FETCH_ASSOC);
        } else {
            self::show_errors($obj->errorInfo(), $sql, $vars);
            $ret = null;
        }

        return $ret;
    }

    public static function get_val($sql, array $vars=array())
    {
        $ret = null;
        $res = self::get_row($sql, $vars);
        if ($res AND is_array($res))
        {
            $ret = current($res);
        }
        return $ret;
    }

    public static function insert_id()
    {
        return self::i()->lastInsertId();
    }

    private static function show_errors($error_info, $sql, array $vars=array())
    {
        if (cfg::i()->get('SQL_SHOW_ERRORS'))
        {
            $msg = '';
            $msg .= '<div style="border:1px double red; padding:10px; margin:10px; font-size:12px; font-family:Tahoma Arial; background-color:white;"><pre>'.PHP_EOL;
            $msg .= '<strong>SQLSTATE ID:</strong> '.htmlspecialchars($error_info[0]).PHP_EOL;
            $msg .= '<strong>DRIVER ERR ID:</strong> '.htmlspecialchars($error_info[1]).PHP_EOL;
            $msg .= '<strong>ERROR:</strong> '.htmlspecialchars($error_info[2]).PHP_EOL;
            $msg .= PHP_EOL;
            $msg .= '<strong>SQL:</strong> '.PHP_EOL.'<span style="color:blue;">'.htmlspecialchars($sql).'</span>'.PHP_EOL;
            if (!empty($vars))
            {
                $msg .= PHP_EOL;
                $msg .= '<strong>SQL VARS:</strong> '.PHP_EOL.'<span style="color:green;">'.htmlspecialchars(print_r($vars, TRUE)).'</span>'.PHP_EOL;
            }
            $msg .= PHP_EOL;
            $msg .= '<strong>Backtrace:</strong> '.PHP_EOL.'<span style="color:gray;">';
            $backtrace = debug_backtrace();
            unset($backtrace[0]);
            $backtrace = array_values($backtrace);
            $msg .= htmlspecialchars(print_r($backtrace, TRUE));
            $msg .= '</span>';

            $msg .= '</pre></div>'.PHP_EOL;
            if (PHP_SAPI === 'cli') $msg = strip_tags($msg);
            echo $msg;
            if (cfg::i()->get('SQL_DIE_ON_ERRORS')) die(0);
        }
    }
}