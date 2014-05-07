<?php

/**
 * Watchr_event
 *
 * @property integer $event_id
 * @property string $event_name
 * @property string $description
 * @property string $timestamp
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @property integer $fk_created_by_user
 * @property integer $fk_event_status
 * @property integer $fk_location
 * @method static \Illuminate\Database\Query\Builder|\Watchr_event whereEventId($value)
 * @method static \Illuminate\Database\Query\Builder|\Watchr_event whereEventName($value)
 * @method static \Illuminate\Database\Query\Builder|\Watchr_event whereDescription($value)
 * @method static \Illuminate\Database\Query\Builder|\Watchr_event whereTimestamp($value)
 * @method static \Illuminate\Database\Query\Builder|\Watchr_event whereCreatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\Watchr_event whereUpdatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\Watchr_event whereFkCreatedByUser($value)
 * @method static \Illuminate\Database\Query\Builder|\Watchr_event whereFkEventStatus($value)
 * @method static \Illuminate\Database\Query\Builder|\Watchr_event whereFkLocation($value)
 * @property boolean $hasMedia
 * @method static \Illuminate\Database\Query\Builder|\Watchr_event whereHasMedia($value)
 * @property-read \Illuminate\Database\Eloquent\Collection|\Attachment[] $attachments
 */
class Watchr_event extends \Eloquent {
    protected $fillable = [

    ];

    protected $table = "watchr_event";

    protected $primaryKey = "event_id";

    protected $hidden =
        [
            "password",
            "salt",
            "fk_created_by_user",
            "fk_event_status",
            "fk_location",
        ];

    public function attachments()
    {
        return $this->belongsToMany('Attachment','event_attachment' , 'fk_event', 'fk_attachment');
    }


}