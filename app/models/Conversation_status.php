<?php

/**
 * Conversation_status
 *
 * @property integer $id
 * @property string $status
 */
class Conversation_status extends \Eloquent {
	protected $fillable = [];

    protected $table = "conversation_status";

    protected $primaryKey = "id";

    public $timestamps = false;
}