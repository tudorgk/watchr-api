<?php

/**
 * Watchr_category
 *
 * @property integer $category_id
 * @property string $category_name
 * @property string $category_description
 * @property integer $fk_subcategory
 * @method static \Illuminate\Database\Query\Builder|\Watchr_category whereCategoryId($value)
 * @method static \Illuminate\Database\Query\Builder|\Watchr_category whereCategoryName($value)
 * @method static \Illuminate\Database\Query\Builder|\Watchr_category whereCategoryDescription($value)
 * @method static \Illuminate\Database\Query\Builder|\Watchr_category whereFkSubcategory($value)
 * @property string $category_icon
 * @property-read \Illuminate\Database\Eloquent\Collection|\Watchr_category[] $subcategories
 */
class Watchr_category extends \Eloquent {
	protected $fillable = [];

    protected $table = "watchr_category";

    protected $primaryKey = "category_id";

    public $timestamps = false;

    public function subcategories(){
        return $this->hasMany('Watchr_category','fk_subcategory');
    }
}