<?php
/**
 * Created by PhpStorm.
 * User: Tudor
 * Date: 4/7/14
 * Time: 3:24 PM
 */

/**
 * Singleton class
 *
 */
final class StringValidator
{
    /**
     * Call this method to get singleton
     *
     * @return UserFactory
     */
    public static function Instance()
    {
        static $inst = null;
        if ($inst === null) {
            $inst = new StringValidator();
        }
        return $inst;
    }

    /**
     * Private ctor so nobody else can instance it
     *
     */
    private function __construct()
    {

    }

    public function isAlphanumeric($str)
    {
        return ctype_alnum($str);
    }
}
