<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Cashier\Billable;
use Stripe\Subscription;

class User extends Authenticatable
{
    use Notifiable, Billable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'last_name','name', 'email', 'password','first_name','last_name','phone','status','remember_token','role_id','company_name','zipcode','address','city','state_id','user_type','country_id'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
	 public static $rules = array(
        'last_name'               => 'required',
        'first_name'              => 'required',
        'email'                   => 'required|email|unique:users',
        'password'                => 'required|regex:/^(?=.*\d)(?=.*[a-z])(?=.*[A-Z]).{6,50}$/', 
        'confirm_pass'            => 'required|same:password',
       
       
    );
    public static $message = array(
        'first_name.required'                    => 'First Name is required',
        'last_name.required'                     => ' Last Name is required',
        'email.required'                         => 'Email is required',
        'password.required'                      => 'Password is required',
        'confirm_pass.required'                  => 'Confirm Password is required', 
        'confirm_pass.same'                      => 'Confirm Password should match password', 
        'password.regex'                         => 'Password must contain a min. of 6 characters, at last one lowercase and capital letter, and a number', 
        
    );
	 
    protected $hidden = [
        'password', 'remember_token',
    ];

    public static function recordUpdate()
    {
        return array(
             'last_name'               => 'required',
             'first_name'              => 'required',
             'email'                   => 'required|email|unique:users',
             'company_name'            => 'required'
        );
    }
    public static function paymentrules()
    {
        $rules['company_name'] = 'required_if:user_type,1';
        $rules['country_id'] = 'required';
        $rules['state_id'] = 'required';
        $rules['city']     = 'required';
        $rules['zipcode'] = 'required'; 
        $rules['address'] = 'required'; 

        return $rules;
    }
   public static function paymentMessage()
   {
       return   array(
        'country_id.required'               => 'please select country',
        'company_name.required_if'             => 'Comapny Name is required',
        'state_id.required'                 => 'Please select state',
        'city.required'                     => 'Please enter city name',
        'zipcode.required'                  => 'please enter zipcode', 
        'address.same'                      => 'please enter address', 
            );

   }
  
   public function roles()
   {
     return $this->belongsToMany(Role::class);
   }
   public function hasAnyRole($roles)
   {
    if(is_array($roles)){

        foreach ($roles as $role) {

            if ($this->hasRole($role)) {
                return true;
            }
        }
    }
    else{
        if ($this->hasRole($roles)) {

            return true;
        }
    }
    return false;
   }
   public function hasRole($role)
   {
     if ($this->roles()->where('name', $role)->first()) {
            return true;
        }

        return false;
  }

  public function country()
   {
    return $this->belongsTo('App\Country','country_id','id');  
    
   } 
    public function state()
   {
    return $this->belongsTo('App\State','state_id','id');  
    
   } 

}
