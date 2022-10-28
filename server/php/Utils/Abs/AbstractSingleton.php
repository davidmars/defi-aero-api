<?php


namespace Utils\Abs;


/**
 *
 * La classe abstraite AbstractSingleton est faite pour
 * @package Utils
 * @see https://stackoverflow.com/questions/203336/creating-the-singleton-design-pattern-in-php5/1939269#1939269
 */
abstract class AbstractSingleton
{

    private static $getterStore=[];
    protected static function storeSet($prop,$value){
        self::$getterStore[$prop]=$value;
        return $value;
    }
    protected static function storeGet($prop){
        if(isset(self::$getterStore[$prop])){
            return self::$getterStore[$prop];
        }
        return false;
    }

    private static $instances = [];
    final public static function inst()
    {
        $cls = get_called_class(); // late-static-bound class name
        if (!isset(self::$instances[$cls])) {
            self::$instances[$cls] = new static;
        }
        return self::$instances[$cls];
    }

    /**
     * Make constructor protected, so nobody can call "new Class". (but may be extended)
     */
    protected function __construct() {
        $cls = get_called_class(); // late-static-bound class name
        if (isset(self::$instances[$cls])) {
            throw new \Exception("This is a singleton ($cls)");
        }
    }

    /**
     * Make clone magic method private, so nobody can clone instance.
     */
    final private function __clone() {}

    /**
     * Make sleep magic method private, so nobody can serialize instance.
     */
    final private function __sleep() {}

    /**
     * Make wakeup magic method private, so nobody can unserialize instance.
     */
    final private function __wakeup() {}
}