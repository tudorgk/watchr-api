<?php

/**
 * Country
 *
 * @property integer $country_id
 * @property string $iso2
 * @property string $short_name
 * @property string $long_name
 * @property string $iso3
 * @property string $numcode
 * @property string $un_member
 * @property string $calling_code
 * @property string $cctld
 * @method static \Illuminate\Database\Query\Builder|\Country whereCountryId($value)
 * @method static \Illuminate\Database\Query\Builder|\Country whereIso2($value)
 * @method static \Illuminate\Database\Query\Builder|\Country whereShortName($value)
 * @method static \Illuminate\Database\Query\Builder|\Country whereLongName($value)
 * @method static \Illuminate\Database\Query\Builder|\Country whereIso3($value)
 * @method static \Illuminate\Database\Query\Builder|\Country whereNumcode($value)
 * @method static \Illuminate\Database\Query\Builder|\Country whereUnMember($value)
 * @method static \Illuminate\Database\Query\Builder|\Country whereCallingCode($value)
 * @method static \Illuminate\Database\Query\Builder|\Country whereCctld($value)
 */
class Country extends \Eloquent {
	protected $fillable = [];

    protected $table = "country_t";

    protected $primaryKey = "country_id";
}