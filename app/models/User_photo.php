<?php

class User_photo extends \Eloquent {
    protected $fillable = [];

    protected $table = "user_photo";

    protected $primaryKey = "photo_id";

    public $timestamps = true;
}