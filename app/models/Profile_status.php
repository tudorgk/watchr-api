<?php

/**
 * Profile_status
 *
 * @property integer $profile_status_id
 * @property string $status_value
 * @method static \Illuminate\Database\Query\Builder|\Profile_status whereProfileStatusId($value)
 * @method static \Illuminate\Database\Query\Builder|\Profile_status whereStatusValue($value)
 */
class Profile_status extends \Eloquent {
	protected $fillable = [];

    protected $table = "profile_status";

    protected $primaryKey = "profile_status_id";

}