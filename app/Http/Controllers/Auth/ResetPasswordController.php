<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Repositories\Password\PasswordRepositoryInterface;
use App\Repositories\User\UserRepositoryInterface;
use Illuminate\Foundation\Auth\ResetsPasswords;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpFoundation\Request;
use Illuminate\Contracts\Hashing\Hasher as HasherContract;
use Session;

class ResetPasswordController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Password Reset Controller
    |--------------------------------------------------------------------------
    |
    | This controller is responsible for handling password reset requests
    | and uses a simple trait to include this behavior. You're free to
    | explore this trait and override any methods you wish to tweak.
    |
    */

    use ResetsPasswords;

    /**
     * Where to redirect users after resetting their password.
     *
     * @var string
     */
    protected $redirectTo = '/';
    protected $passwordRepository;
    protected $userRepository;
    protected $hasher;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(Request $request,
                                PasswordRepositoryInterface $passwordRepository,
                                UserRepositoryInterface $userRepository,
                                HasherContract $hasher)
    {
        $this->middleware('guest');
        $this->passwordRepository = $passwordRepository;
        $this->userRepository = $userRepository;
        $this->hasher = $hasher;
    }

    /**
     * Display the password reset view for the given token.
     *
     * If no token is present, display the link request form.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  string|null  $token
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function showResetForm(Request $request, $token = null)
    {
        $reset = $this->passwordRepository->checkTokenExpired(base64_decode($request->email));

        //if status != 1 ( =0 ), then update status = 1 ( mean was clicked )
        if($reset->status != 1){

            $this->passwordRepository->updateStatus( base64_decode($request->email), 1 );

            //if token doesnt exist in database, return message
            if($this->passwordRepository->checkExpriedTime(base64_decode($request->email))){ // check if time token has than 1 hour
                return view('auth.passwords.reset')->withErrors(['token_error' => 'This reset link more than 1 hour']);
            }else{ // if time less than 1 hour then check if token has used or not ?
                $reset = $this->passwordRepository->checkTokenExpired(base64_decode($request->email));
                if(!$this->hasher->check( $token, $reset->token )){ //using hasher to check token
                    return view('auth.passwords.reset')->withErrors(['token_error' => 'This reset token has Expired']);
                }
            };

            return view('auth.passwords.reset')->with(
                ['token' => $token, 'email' => $request->email]
            );

        }else{//if == 1, return token expired
            return view('auth.passwords.reset')->withErrors(['token_error' => 'This reset token has Expired']);
        }
    }

    /**
     * Reset the given user's password.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function reset(Request $request)
    {

        $data = ['password' => bcrypt($request->password)];

        if($this->userRepository->updatePassword(base64_decode($request->email),$data)){//password updated

            return view('auth.passwords.success');
        }

        return redirect()->back()->withErrors(['password' => "Some thing went wrong"]);

    }

    public function credentials($request,$rules, $messages){
        return Validator::make($request, $rules, $messages);
    }

}
