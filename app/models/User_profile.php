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
use Illuminate\Auth\UserInterface;
use Illuminate\Auth\Reminders\RemindableInterface;

class User_profile extends \Eloquent implements UserInterface, RemindableInterface {
	protected $fillable = [
        "username",
        "email",
        "first_name",
        "last_name",
    ];

    protected $table = "user_profile";

    protected $primaryKey = "user_id";

    protected $hidden = ["password", "fk_photo", "fk_profile_status"];

    protected $guarded = [
        "user_id",
        "password"
    ];

    public function country(){
        return $this->hasOne('Country', 'fk_country', 'country_id');
    }

    public function photo()
    {
        return $this->hasOne('Attachment', 'fk_photo', 'id');
    }

    public function getAuthIdentifier()
    {
        return $this->getKey();
    }

    /**
     * Get the password for the user.
     *
     * @return string
     */
    public function getAuthPassword()
    {
        return $this->password;
    }

    /**
     * Get the e-mail address where password reminders are sent.
     *
     * @return string
     */
    public function getReminderEmail()
    {
        return $this->email;
    }

    public function getRememberToken()
    {
        return $this->remember_token;
    }

    public function setRememberToken($value)
    {
        $this->remember_token = $value;
    }

    public function getRememberTokenName()
    {
        return 'remember_token';
    }
}