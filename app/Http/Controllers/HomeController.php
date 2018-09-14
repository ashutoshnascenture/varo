<?php

namespace App\Http\Controllers;


use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;
use Auth;
use App\Plan;
use App\User;
use Session; 
use Validator;
use Laravel\Cashier\Billable;
use Stripe\Subscription;
use DB;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
           // $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        // $plans = Plan::getStripePlans();
        //echo "<pre>"; print_r($plans); die;
        $plans = DB::table('plans')->get();
        return view('home')->with(compact('plans')); 
    }

    public function planDetail($planID='')
    {
    	
         $planID = base64_decode($planID);
         $plan = DB::table('plans')->find($planID);
         if ($plan) {
          return view('plan-detail')->with(compact('plan'));   
         }
         return redirect()->back();
    }
 
    public function  planPayment(Request $request)
    {
         $rules = User::$rules;
         $messages = User::$message;
         $input = $request->all();
         $validator = Validator::make($input, $rules, $messages);
         if ($validator->fails()) {
            return redirect()->back()->withInput($input)->withErrors($validator->errors());   
         }
          $input = $request->all();
          $plan = DB::table('plans')->find($input['palnID']);
          if ($plan) { 
             $userDetail = $input;
             return view('plan-payment')->with(compact('plan','userDetail'));  
          } else {
             return redirect()->back();
          }
    }

    public function subscribePlan(Request $request) 
    {
     
        $this->validate( $request, [ 'stripeToken' => 'required', 'plan' => 'required'] );
        $pickedPlan = $request->get('plan');
		$pickedPlanName = $request->get('plan_name');
		 User::create([
		 	'name' => $request->get('first_name'),
            'first_name' => $request->get('first_name'),
            'last_name'  => $request->get('last_name'),
             'email'     => $request->get('email'),
            'password'   => Hash::make($request->get('password')),
        ]);
		$checkUser = Auth::attempt([
		    'email' => $request->get('email'), 
			'password' => $request->get('password')
		]);
		
		if ( $checkUser ) {
			$user = Auth::user(); 
          try {
			// check already subscribed and if already subscribed with picked plan
			if( $user->subscribed('main') && ! $user->subscribedToPlan($pickedPlan, 'main') ) {
				$user->subscription('main')->swap($pickedPlan);
				
			} else {
				// Its new subscription
				// if user has a coupon, create new subscription with coupon applied
				if( $coupon = $request->get('coupon') ) {
					$user->newSubscription( 'main', $pickedPlan)
						->withCoupon($coupon)
						->create($request->get('stripeToken'), [
							'email' => $user->email
						]);
						
				} else {
					// Create subscription
					
					$user->newSubscription( 'main', $pickedPlan)->create($request->get('stripeToken'), [
						'email' => $user->email,
						'description' => $user->name
					]);

				}

			}
		} catch (\Exception $e) {

			 return redirect('/plan-select/'.base64_encode($request->get('planlocalid')))->withErrors(['status' => $e->getMessage()]);
			
		}
		return redirect('/plans')->with('status', 'You are now subscribed to ' . $pickedPlanName . ' plan.');

		} else {
             
			return redirect('/plan-select/'.base64_encode($request->get('planlocalid')))->withErrors(['status' => 'somthing went wrong please try again']);;
		}
		

    }
}