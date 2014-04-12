<?php

/**
 * Relationship_type
 *
 * @property integer $relationship_type_id
 * @property string $name
 * @property string $description
 * @method static \Illuminate\Database\Query\Builder|\Relationship_type whereRelationshipTypeId($value)
 * @method static \Illuminate\Database\Query\Builder|\Relationship_type whereName($value)
 * @method static \Illuminate\Database\Query\Builder|\Relationship_type whereDescription($value)
 */
class Relationship_type extends \Eloquent {
	protected $fillable = [];

    protected $table = "relationship_type";

    protected $primaryKey = "relationship_type_id";

    public $timestamps = false;
}