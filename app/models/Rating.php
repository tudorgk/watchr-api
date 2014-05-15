<?php

/**
 * Rating
 *
 * @property integer $rating_id
 * @property integer $fk_user_id
 * @property integer $fk_event_id
 * @property integer $rating_value
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 */
class Rating extends \Eloquent {
    protected $fillable = [];

    protected $table = "rating";

    protected $primaryKey = "rating_id";
}