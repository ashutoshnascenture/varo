<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Site extends Model
{
    //


	 protected $table = 'sites';
     protected $fillable = [
        'user_id','plan_id','ftp_host','ftp_password','ftp_username','sftp_host','subscription_id','site_status',
        'sftp_password','sftp_username','url','cpanel_username','cpanel_password','name','url','cpanel_host','site_image'
     ];

     public static $rules = array(
        'name'                   => 'required',
        'url'                    => 'required'
        
    );
    public static $message = array(
        'name.required'                    => 'Site  nameis required',
        'url.required'                     => 'URL is required'        
    );
  
   public function parent()
   {
    return $this->belongsTo('App\Subscription','subscription_id','stripe_id')->where('site_status', 1)->with(['plan','invoicelist.amount']);  
    // only  get subscription if site added use site_status.
   } 
   public function user()
   {

     return $this->belongsTo('App\User','user_id','id');
   }
  /* public function subscription()
   {
    return $this->belongsTo('App\Subscription','subscription_id','stripe_id');  
   } */
  
}
