<?php

/**
 * Device
 *
 * @property integer $device_id
 * @property string $brand
 * @property string $model
 * @property string $device_uid
 * @property string $device_token
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @method static \Illuminate\Database\Query\Builder|\Device whereDeviceId($value)
 * @method static \Illuminate\Database\Query\Builder|\Device whereBrand($value)
 * @method static \Illuminate\Database\Query\Builder|\Device whereModel($value)
 * @method static \Illuminate\Database\Query\Builder|\Device whereDeviceUid($value)
 * @method static \Illuminate\Database\Query\Builder|\Device whereDeviceToken($value)
 * @method static \Illuminate\Database\Query\Builder|\Device whereCreatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\Device whereUpdatedAt($value)
 */
class Device extends \Eloquent {
	protected $fillable = [];

    protected $table = "device";

    protected $primaryKey = "device_id";
}