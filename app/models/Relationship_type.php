<?php

class Relationship_type extends \Eloquent {
	protected $fillable = [];

    protected $table = "relationship_type";

    protected $primaryKey = "relationship_type_id";

    public $timestamps = false;
}