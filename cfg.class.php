<?php

    class cfg {

        private static $instance = null;
        private $store = array();

        private function __construct()
        {
          // private constructor
        }

        public static function instance()
        {
            if ( is_null(self::$instance) )
            {
              self::$instance = new Cfg;
            }
            return self::$instance;
        }

        public static function i()
        {
            return self::instance();
        }

        public function set($var, $val)
        {
            $this->store[$var] = $val;
            return self::$instance;
        }

        public function get($var, $default=null)
        {
            return isset($this->store[$var]) ? $this->store[$var] : $default;
        }

        public function __toString()
        {
          return print_r($this->store, TRUE);
        }
    }