<?php

/**
 * Watchr_event_category
 *
 * @property integer $event_category_id
 * @property integer $fk_event
 * @property integer $fk_category
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @method static \Illuminate\Database\Query\Builder|\Watchr_event_category whereEventCategoryId($value)
 * @method static \Illuminate\Database\Query\Builder|\Watchr_event_category whereFkEvent($value)
 * @method static \Illuminate\Database\Query\Builder|\Watchr_event_category whereFkCategory($value)
 * @method static \Illuminate\Database\Query\Builder|\Watchr_event_category whereCreatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\Watchr_event_category whereUpdatedAt($value)
 */
class Watchr_event_category extends \Eloquent {
	protected $fillable = [];

    protected $table = "watchr_event_category";

    protected $primaryKey = "event_category_id";

    public $timestamps = true;

}