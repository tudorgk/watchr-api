<?php

/**
 * Attachment
 *
 * @property integer $id
 * @property string $location
 * @property string $attachment_type
 * @property string $description
 * @property string $filename
 * @property integer $size
 * @property integer $width
 * @property integer $height
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @method static \Illuminate\Database\Query\Builder|\Attachment whereId($value) 
 * @method static \Illuminate\Database\Query\Builder|\Attachment whereLocation($value) 
 * @method static \Illuminate\Database\Query\Builder|\Attachment whereAttachmentType($value) 
 * @method static \Illuminate\Database\Query\Builder|\Attachment whereDescription($value) 
 * @method static \Illuminate\Database\Query\Builder|\Attachment whereFilename($value) 
 * @method static \Illuminate\Database\Query\Builder|\Attachment whereSize($value) 
 * @method static \Illuminate\Database\Query\Builder|\Attachment whereWidth($value) 
 * @method static \Illuminate\Database\Query\Builder|\Attachment whereHeight($value) 
 * @method static \Illuminate\Database\Query\Builder|\Attachment whereCreatedAt($value) 
 * @method static \Illuminate\Database\Query\Builder|\Attachment whereUpdatedAt($value) 
 */
class Attachment extends \Eloquent {
	protected $fillable = [];

    protected $table = "attachment";

}