<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Log;

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

    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = '/home';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(){
        $this->middleware('guest')->except('logout');
    }

    public function login(Request $request){
        $this->validateLogin($request);

        if ($this->attemptLogin($request)) {
            $user = $this->guard()->user();
            $user->generateToken();

            Log::create([
                'user_id' =>  $user->id,
                'action' => 'Login',
            ]);

            //['user_id', 'article_id', 'action', 'created_at', 'updated_at']

            return response()->json([
                'user' => $user->toArray(),
            ]);
        }

        return $this->sendFailedLoginResponse($request);
    }

    public function logout(Request $request){

        $user = Auth::guard('api')->user();
        //$enter= 'not enter';
        if ($user) {
            $user->api_token = null;
            $user->save();
            //$enter= 'is  entered';
            Auth::logout();
            Log::create([
                'user_id' =>  $user->id,
                'action' => 'Logout',
            ]);

            //['user_id', 'article_id', 'action', 'created_at', 'updated_at']
        }
        return response()->json(['data' => 'User logged out.', 'user' => $user], 200);
    }
}
