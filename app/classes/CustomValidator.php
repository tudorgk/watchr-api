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
final class CustomValidator
{
    /**
     * Call this method to get singleton
     *
     * @return CustomValidator
     */
    public static function Instance()
    {
        static $inst = null;
        if ($inst === null) {
            $inst = new CustomValidator();
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

    public function is_lphanumeric($str)
    {
        return ctype_alnum($str);
    }

    public function exists_in_db($table, $column, $value){

        $result = DB::table($table)->where($column, '=', $value)->first();

        if($result)
            return true;
        else
            return false;
    }

    public function is_valid_id($table, $column, $value){

        if(is_null($value) || strcmp($value,"") == 0 ||
            !$this->exists_in_db($table,$column,$value)){
            return false;
        }
        else
            return true;
    }
}
