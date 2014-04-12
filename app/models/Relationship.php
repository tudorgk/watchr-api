<?php

/**
 * Relationship
 *
 * @property integer $relationship_id
 * @property integer $fk_user_1
 * @property integer $fk_user_2
 * @property integer $fk_relationship_type
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @method static \Illuminate\Database\Query\Builder|\Relationship whereRelationshipId($value)
 * @method static \Illuminate\Database\Query\Builder|\Relationship whereFkUser1($value)
 * @method static \Illuminate\Database\Query\Builder|\Relationship whereFkUser2($value)
 * @method static \Illuminate\Database\Query\Builder|\Relationship whereFkRelationshipType($value)
 * @method static \Illuminate\Database\Query\Builder|\Relationship whereCreatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\Relationship whereUpdatedAt($value)
 */
class Relationship extends \Eloquent {
	protected $fillable = [];

    protected $table = "relationship";

    protected $primaryKey = "relationship_id";
}