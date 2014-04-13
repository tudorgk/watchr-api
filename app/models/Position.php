<?php

/**
 * Position
 *
 * @property integer $position_id
 * @property float $latitude
 * @property float $longitude
 * @method static \Illuminate\Database\Query\Builder|\Position wherePositionId($value)
 * @method static \Illuminate\Database\Query\Builder|\Position whereLatitude($value)
 * @method static \Illuminate\Database\Query\Builder|\Position whereLongitude($value)
 */
class Position extends \Eloquent {
	protected $fillable = [];

    protected $table = "position";

    protected $primaryKey = "position_id";

    public $timestamps = false;
}