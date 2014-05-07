<?php

/**
 * User_profile
 *
 * @property integer $user_id
 * @property string $username
 * @property string $email
 * @property string $password
 * @property string $salt
 * @property string $first_name
 * @property string $last_name
 * @property integer $fk_photo
 * @property integer $fk_country
 * @property integer $fk_profile_status
 * @method static \Illuminate\Database\Query\Builder|\User_profile whereUserId($value)
 * @method static \Illuminate\Database\Query\Builder|\User_profile whereUsername($value)
 * @method static \Illuminate\Database\Query\Builder|\User_profile whereEmail($value)
 * @method static \Illuminate\Database\Query\Builder|\User_profile wherePassword($value)
 * @method static \Illuminate\Database\Query\Builder|\User_profile whereSalt($value)
 * @method static \Illuminate\Database\Query\Builder|\User_profile whereFirstName($value)
 * @method static \Illuminate\Database\Query\Builder|\User_profile whereLastName($value)
 * @method static \Illuminate\Database\Query\Builder|\User_profile whereFkPhoto($value)
 * @method static \Illuminate\Database\Query\Builder|\User_profile whereFkCountry($value)
 * @method static \Illuminate\Database\Query\Builder|\User_profile whereFkProfileStatus($value)
 * @property integer $gender
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @method static \Illuminate\Database\Query\Builder|\User_profile whereGender($value)
 * @method static \Illuminate\Database\Query\Builder|\User_profile whereCreatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\User_profile whereUpdatedAt($value)
 * @property-read \Attachment $photo
 * @property string $remember_token
 */
class User_profile extends \Eloquent {
	protected $fillable = [
        "username",
        "email",
        "first_name",
        "last_name",
    ];

    protected $table = "user_profile";

    protected $primaryKey = "user_id";

    protected $hidden = ["password", "salt", "fk_photo", "fk_profile_status"];

    protected $guarded = [
        "user_id",
        "password",
        "salt"
    ];

    public function photo()
    {
        return $this->hasOne('Attachment', 'fk_photo', 'id');
    }
}