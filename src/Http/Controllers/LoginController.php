<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Session;
use Illuminate\Http\Request;
use App\User;

use Auth;
use Hash;
use Activity;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    protected $site = '';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->site = config('site');
    }

    public function index()
    {
    	if(Auth::check() && check_cross_auth()) {
            return Redirect::to($this->site['main_module_slug']);
        } else {
            return Redirect::to(env('UAM_URL').'?action=logout');
        }
    }

    public function login(Request $request)
	{
		 $rules = array(
            'username' => 'required',
            'password' => 'required|alphaNum|min:3'
        );

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return Redirect::to('/')
                ->withErrors($validator)
                ->withInput($request->except('password'));
        } else {

          // create our user data for the authentication
            $userdata = array(
                'username' => $request->get('username'),
                'password' => $request->get('password'),
            );

            $userdataFallback = array(
                'username' => $request->get('username'),
                'password' => md5($request->get('password')),
            );

            $user = \App\User::firstOrNew($userdataFallback);

            // attempt to do the login
            if (Auth::attempt($userdata)) {
                //activity log here

                return Redirect::to('article');
            } elseif ($user->exists) {

                $user->password = Hash::make($userdata['password']);
                $user->save();

                //activity log here//activity log here

                Auth::login($user);

                return Redirect::to('article');
            } else {

                // validation not successful, send back to form
                return Redirect::to('/')->with('message', 'Username or password is not correct.');
            }
        }
	}

	public function logout()
	{
		session()->flush();
        Auth::logout(); // log the user out of our application
        return Redirect::to('/login'); // redirect the user to the login screen
	}

	public function authenticate(User $user, Request $request)
    {
    	$userId = $request->input('userId');
    	$website = $request->input('website');
    	$crossToken = $request->input('token');


    	// since http server communication such as file_get_contents and curl is not working on local
        // connect to staging site of uam to get the permissions
        $uamUrl = str_replace('local', 'staging', env('UAM_URL'));
        $uamUrl = str_replace(':8000', '', $uamUrl);

        if (env('APP_ENV') != 'local') {
            $uamUrl = env('UAM_URL');
        }

        $userData = $user->whereId($userId)->whereCrossToken($crossToken)->first();

        if ($userData && !empty($this->site)) {
        	$auth = Auth::loginUsingId($userId);

        	if ($auth) {
        		//Insert logging in to activity logs
        		session()->put('cross_token', $crossToken);

        		$permissionUrl = $uamUrl."/permissions/{$userId}/{$this->site['site_id']}?platform=Quill";

        		$contextOptions = [];

        		if (env('APP_ENV') == 'local') {
        			//remove this pag na push na sa stgquill.cosmo.summitmedia-digital.com.ph check if mag working
	        		$contextOptions = [
				       'ssl' => [
				            'verify_peer' => false,
				            'verify_peer_name' => false,
				        ]
				    ];

				    $userPermissions = file_get_contents($permissionUrl, false, stream_context_create($contextOptions));

				} else {
					$userPermissions = file_get_contents($permissionUrl);
				}

                $userPermissions = json_decode($userPermissions, true);

                session()->put('is_admin', $userPermissions['user']['is_admin']);

                if (!$userPermissions['user']['is_admin'] && $userPermissions['permissions']) {
                    session()->put('permissions', $userPermissions['permissions']);
                }

                session()->put('is_manager', $userPermissions['user']['is_manager_website']);


                return redirect()->to($this->site['main_module_slug']);
        	}
        }

    	return redirect()->to($uamUrl);
    }
}
