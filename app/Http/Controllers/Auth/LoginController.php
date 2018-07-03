<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\LoginRequest;
use App\Repositories\User\UserRepositoryInterface;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Request;
use App\Helper;

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
    protected $redirectTo = '/';
    protected $userRepository;
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(UserRepositoryInterface $userRepository)
    {
        $this->userRepository = $userRepository;
        $this->middleware('guest')->except('logout');
    }

    /**
     * @author SonNA
     * Set login for username field
     * @return string
     */
    public function username()
    {
        return 'username';
    }

    public function login(LoginRequest $request)
    {
        $config = [
            'host' => '172.16.90.42',
            'port' => 389,
            'protocol' => 3,
            'opt_refer' => 0,
            'base_dn' => 'ou=Users,dc=co-well,dc=vn',
            'bind_dn' => 'cn=admin,dc=co-well,dc=vn',
            'bind_password' => 'cowellldap@2017'
        ];

        $user_username = $request->username;
        $user_password = $request->password;

        //connect ldap and set option
        $ldapconn = Helper::connect_ldap($config);

        if($ldapconn){

            //Bind to ldap server
            $ldapbind = ldap_bind($ldapconn, $config['bind_dn'], $config['bind_password']);

            if($ldapbind){
                // search LDAP user
                $user_search = ldap_search($ldapconn,$config['base_dn'],"(|(uid=$user_username))");
                //get user entries
                $user_get = ldap_get_entries($ldapconn, $user_search);
                
                if($user_get['count'] === 1) {

                    //get user first entry
                    $user_entry = ldap_first_entry($ldapconn, $user_search);

                    //get user dn
                    $user_dn = ldap_get_dn($ldapconn, $user_entry);

                    $bind = @ldap_bind($ldapconn, $user_dn, $user_password);

                    if (!$bind)//user existed on ldap
                    {
                        ldap_close($ldapconn);//close ldap
                        return redirect()->to('login')
                            ->withErrors(['username' => __('auth.failed')]);

                    } else {//login with account system
                        //check user existed or not
                        $has_user = $this->userRepository->findByUID($user_username);
                        if ($has_user) {
                            //if password on ldap has changed, password on system will change
                            $this->userRepository->update($has_user->id, ['password' => bcrypt($user_password), 'id_resource' => 0]);

                            if (Auth::attempt($request->only('username', 'password'))) {
                                // Returns \App\User model configured in `config/auth.php`.
                                $user = Auth::user();
                                return redirect()->to($this->redirectTo);
                            }else{
                                return redirect()->to('login')
                                ->withErrors(['username' => __('auth.failed')]);
                            }

                        } else {
                            //if user has sucess on LDAP but not existed on system, create users
                            $this->userRepository->create(['fullname' => $user_get[0]['displayname'][0], 'username' => $user_username, 'password' => bcrypt($user_password), 'id_resource' => 0]);

                            if (Auth::attempt($request->only('username', 'password'))) {
                                // Returns \App\User model configured in `config/auth.php`.
                                $user = Auth::user();
                                return redirect()->to($this->redirectTo);
                            }
                        }
                    }
                }else{
                    ldap_close($ldapconn);//close ldap

                    if (Auth::attempt($request->only('username', 'password'))) {
                        // Returns \App\User model configured in `config/auth.php`.
                        $user = Auth::user();
                        return redirect()->to($this->redirectTo);
                    }

                    return redirect()->to('login')
                        ->withErrors(['username' => __('auth.failed')]);
                }

            }
        }
    }
    /**
     * @author SonNA
     * Set logout method
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function logout()
    {
        Auth::logout();
        return redirect('login');
    }
}
