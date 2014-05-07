<?php

/**
 * Conversation
 *
 * @property integer $conversation_id
 * @property integer $fk_conversation_status
 * @property integer $fk_watchr_event
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\Conversation_reply[] $replies
 */
class Conversation extends \Eloquent {
	protected $fillable = [];

    protected $table = "conversation";

    protected $primaryKey = "conversation_id";

    public $timestamps = true;

    public function replies()
    {
        return $this->hasMany('Conversation_reply', 'fk_conversation', 'conversation_id');
    }

}