<?php

/**
 * Event_attachment
 *
 * @property integer $id
 * @property integer $fk_event
 * @property integer $fk_photo
 * @method static \Illuminate\Database\Query\Builder|\Event_attachment whereId($value) 
 * @method static \Illuminate\Database\Query\Builder|\Event_attachment whereFkEvent($value) 
 * @method static \Illuminate\Database\Query\Builder|\Event_attachment whereFkPhoto($value) 
 */
class Event_attachment extends \Eloquent {
	protected $fillable = [];

    protected $table = "event_attachment";

    public $timestamps = false;
}