<?php


use Cartalyst\Sentry\Users\Eloquent\User as SentryUserModel;

class User extends SentryUserModel
{
    // Add your validation rules here
    public static $rules = [
        'first_name' => 'required',
        'email' => 'required|email|unique:users,email,:id',
        'password' => 'confirmed|required|min:5',
        'recaptcha_response_field' => 'required|recaptcha',
    ];

    // Don't forget to fill this array
    protected $fillable = ['first_name', 'last_name', 'email', 'password'];

	/**
	 * Relasi pivot (Many-to-Many) dengan buku
	 * @return Collection
	 */
	public function books()
    {
        return $this->belongsToMany('Book')->withPivot('returned')->withTimestamps();
    }
}
