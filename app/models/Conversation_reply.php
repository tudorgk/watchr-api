<?php

/**
 * Conversation_reply
 *
 * @property integer $reply_id
 * @property string $reply_text
 * @property integer $fk_reply_status
 * @property integer $fk_conversation
 * @property integer $fk_user
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 */
class Conversation_reply extends \Eloquent {
    protected $fillable = [];

    protected $table = "conversation_reply";

    protected $primaryKey = "reply_id";

    public $timestamps = true;

    protected $hidden = ["fk_conversation", "fk_user"];

    public function conversation()
    {
        return $this->belongsTo('Conversation','fk_conversation');
    }

}