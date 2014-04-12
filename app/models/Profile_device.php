<?php

/**
 * Profile_device
 *
 * @property integer $profile_device_id
 * @property integer $fk_user_profile
 * @property integer $fk_device
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @method static \Illuminate\Database\Query\Builder|\Profile_device whereProfileDeviceId($value)
 * @method static \Illuminate\Database\Query\Builder|\Profile_device whereFkUserProfile($value)
 * @method static \Illuminate\Database\Query\Builder|\Profile_device whereFkDevice($value)
 * @method static \Illuminate\Database\Query\Builder|\Profile_device whereCreatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\Profile_device whereUpdatedAt($value)
 */
class Profile_device extends \Eloquent {
	protected $fillable = [];

    protected $table = "profile_device";

    protected $primaryKey = "profile_device_id";
}