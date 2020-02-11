<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Session;
use Illuminate\Http\Request;
use Quill\History\Models\History;
use Vellum\Module\Quill;
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
        $historyConfig = config('history');
        $this->activity_code = $historyConfig['activity_code'];
    }

    public function index()
    {
    	if(Auth::check() && check_cross_auth()) {
            return Redirect::to($this->site['main_module_slug']);
        } else {
            return Redirect::to(env('UAM_URL').'?action=logout');
        }
    }

    public function login()
	{
        return Redirect::to('/');
	}

	public function logout()
	{
		$historyDetails = [
			'user_id' => Auth::user()->id,
			'activity_code' => $this->activity_code['logged_out'],
			'historyable_id' => Auth::user()->id,
			'historyable_type' => 'App\User',
			'history_details' => serialize(Auth::user()->getAttributes())
		];
		History::create($historyDetails);

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
        $uamUrl = str_replace('local', 'beta', env('UAM_URL'));
        $uamUrl = str_replace(':8000', '', $uamUrl);

        if (env('APP_ENV') != 'local') {
            $uamUrl = env('UAM_URL');
        }

        $userData = $user->whereId($userId)->whereCrossToken($crossToken)->first();

        if ($userData && !empty($this->site)) {
        	$auth = Auth::loginUsingId($userId);

        	if ($auth) {

        		$historyDetails = [
        			'user_id' => $userId,
        			'activity_code' => $this->activity_code['logged_in'],
        			'historyable_id' => $userId,
        			'historyable_type' => 'App\User',
        			'history_details' => serialize($userData->getAttributes())
        		];

        		History::create($historyDetails);

        		session()->put('cross_token', $crossToken);

        		$permissionUrl = $uamUrl."/permissions/{$userId}/{$this->site['site_id']}?platform=Quill&fromVellum=1";

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

                $modulePermissions = [];
                $modules = array_column(event(Quill::MODULE), 'module');
                if (!$userPermissions['user']['is_admin'] && isset($userPermissions['permissions']) && $userPermissions['permissions']) {
                	foreach ($userPermissions['permissions'] as $key => $permission) {
                		if (in_array($key, $modules)) {
                			array_walk($permission, function(&$value){
                				$value = strtolower($value);
                			});
                			$modulePermissions[strtolower($key)] = $permission;
                		}
                	}

                } else if ($userPermissions['user']['is_admin']) {
                	foreach ($modules as $module) {
                		$modulePermissions[strtolower($module)] = ["*"];
                	}
                }

                session()->put('permissions', $modulePermissions);

                session()->put('is_manager', $userPermissions['user']['is_manager_website']);

                return redirect()->to($this->site['main_module_slug']);
        	}
        }

    	return redirect()->to($uamUrl);
    }
}
