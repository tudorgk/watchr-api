<?php

/**
 * User_photo
 *
 * @property integer $photo_id
 * @property string $location
 * @property string $description
 * @property string $filename
 * @property string $file_type
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 */
class User_photo extends \Eloquent {
    protected $fillable = [];

    protected $table = "user_photo";

    protected $primaryKey = "photo_id";

    public $timestamps = true;
}