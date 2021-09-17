<?php

namespace {

    class Phpfox_Config_Container
    {

        /**
         * @var array
         */
        private $bag = [];

        public function __construct()
        {
            $this->bag =  include PHPFOX_DIR  .'include/package.config.php';
        }

        /**
         * @param string $section
         * @param string $item
         *
         * @return mixed|null
         */
        public function get($section, $item = null)
        {
            if (!isset($this->bag[$section])) {
                return null;
            }

            if (!$item) {
                return $this->bag[$section];
            }

            if (!isset($this->bag[$section][$item])) {
                return null;
            }

            return $this->bag[$section][$item];
        }

        public function merge($data)
        {
            foreach ($data as $k => $v) {
                if (!isset($this->bag[$k])) {
                    $this->bag[$k] = [];
                }

                if (!is_array($v)) {
                    $this->bag[$k] = $v;
                } else {
                    $this->bag[$k] = array_merge($this->bag[$k], $v);
                }
            }
        }
    }

    class Phpfox_Service_Container
    {
        /**
         * @var array
         */
        private $bag = [];

        const SECTION = 'services';
        /**
         * @param string $key
         *
         * @return mixed|null
         */
        public function get($key)
        {
            $key = str_replace('phpfox.', '', str_replace('_', '.',
                strtolower($key)));
            return isset($this->bag[$key]) ? $this->bag[$key]
                : $this->bag[$key] = $this->build($key);
        }

        /**
         * @param string $key
         * @param mixed $value
         */
        public function set($key, $value)
        {
            $this->bag[$key] =  $value;
        }

        /**
         * @param string $key
         *
         * @return bool
         */
        public function exists($key)
        {
            $key = str_replace('phpfox.', '', str_replace('_', '.',
                strtolower($key)));
            return isset($this->bag[$key]);
        }

        /**
         * @param string $key
         *
         * @return bool
         */
        public function has($key){
            $scheme  =  Phpfox::getConfig(self::SECTION, $key);
            $class =  null;

            if(is_string($scheme)){
                return new $scheme;
            }

            if(is_array($scheme)){
                $class = array_shift($scheme);
            }

            if($class){
                return true;
            }
        }
        /**
         * @param string $key
         *
         * @return mixed|null
         * @throws \InvalidArgumentException
         */
        public function build($key)
        {
            $scheme  =  Phpfox::getConfig(self::SECTION, $key);
            $class =  null;

            if(is_string($scheme)){
                return new $scheme;
            }

            if(is_array($scheme)){
                $class = array_shift($scheme);
            }

            if($class){
                if(!class_exists($class)){
                    throw new \InvalidArgumentException(_p('unexpected_class_class',['class' => $class]));
                }
                return call_user_func_array([new $class,
                'factory'], $scheme);
            }

            if(!$class){
                throw new \InvalidArgumentException(_p('unexpected_service_key',['key' => $key]));
            }

            if(!class_exists($class)){
                throw new \InvalidArgumentException(_p('unexpected_class_class',['class' => $class]));
            }
            return (new \ReflectionClass($class))->newInstanceArgs($scheme);
        }

        public function __sleep()
        {
            return ['section'];
        }
    }
}