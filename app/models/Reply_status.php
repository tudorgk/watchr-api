<?php

/**
 * Reply_status
 *
 * @property integer $id
 * @property string $status
 */
class Reply_status extends \Eloquent {
	protected $fillable = [];

    protected $table = "reply_status";

    public $timestamps = false;
}