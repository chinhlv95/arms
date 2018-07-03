<?php

namespace App\Http\Controllers;

use App\Repositories\User\UserRepositoryInterface;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\App;
use App\Config;
use Folklore\Image\Facades\Image;
use GuzzleHttp\json_encode;
use App\Helper;
use Illuminate\Support\Facades\Auth;
use App\Repositories\Role\RoleRepositoryInterface;

class UserController extends Controller
{
    /**
     * @var UserRepositoryInterface|\App\Repositories\RepositoryInterface
     */
    protected $userRepository;
    protected $role;

    public function __construct(UserRepositoryInterface $userRepository, RoleRepositoryInterface $role){
        $this->userRepository = $userRepository;
        $this->role = $role;
    }
    
    /**
     * @author: HueNT
     * @return resource/user/index
     */
    public function index()
    {
        $entries = json_encode(config('contains.NUMBER_ENTRIES'));
        $resource = json_encode(config('contains.ID_RESOURCE'));
        
        return view('user/index', compact('entries','resource'));
    }
    
    /**
     * @author: HueNT
     * @todo get list users
     * @return json
    */
    public function getAll()
    {
        $response = $this->userRepository->getAllResource();
        return response()->json($response, 200);
    }
    
    /**
     * @author: HueNT
     * @todo create user
     * @return resource/view/user/create.blade
     */
    public function create()
    {
        $callingcode = json_encode(config('contains.CALLING_CODE'));
        $resource = json_encode(config('contains.ID_RESOURCE'));
        
        return view('user/create', compact('callingcode','resource'));
    }
    
   /**
    * @author: HueNT
    * @todo save user
    * @return boolean
    */
    public function save(Request $request)
    { 
        $data = $request->all();
        $data['password'] = bcrypt($request->password);
        // if Request contain data image profile, system call function upload to save image to path /public/upload/avatar/
        if($request->avatar)
        {
            if($this->upload($request->avatar))
            {
                $result = $this->upload($request->avatar);
                if($result['success'])
                {
                    $data['avatar'] = $result['name'];
                }
                else
                    $data['avatar'] = '';
            }
        }
        $result = $this->userRepository->create($data);
        if(isset($result))
        {
            return $request->session()->flash('message',[
                'level' => 'success',
                'title' => __('user.title_create'),
                'content' => __('user.insert_success')
                ]);
        }
        if(empty($result))
        {
            return $request->session()->flash('message',[
                'level' => 'danger',
                'title' => __('user.title_create'),
                'content' => __('user.insert_error')
            ]);
        }
    }

    /**
     * @author: HueNT
     * @todo upload avatar
     * @param array $data
     * @param boolean
     */
    public function upload($data)
    {
        $img = $data['data'];
    
        $img =  base64_decode(explode(',', $img)[1]);
    
        $name= 'arms_avatar_'.$data['name'];
        while(file_exists(config('contains.TARGET_UPLOAD_DIR'). $name))
        {
            $name = 'arms_avatar_'.str_random(5).'_'.$data['name'];
        }
        
        $path = config('contains.TARGET_UPLOAD_DIR') . $name;
       
        $success = file_put_contents($path, $img);
        
        $result = [
            'success' => $success,
            'name' => $name
        ];
        
        Image::make($path,array(
            'width' => 300,
            'height' => 300,
            'crop' => true
            ))->save($path);
            
        return $result;
    }
    
    /**
     * @author HueNT
     * @todo update avatar & password
     * @param Request $request
     * @return resource/view/user/update
     */
    public function changeAvatar(Request $request)
    {
        $path = config('contains.TARGET_UPLOAD_DIR');
        $callingcode = json_encode(config('contains.CALLING_CODE'));
        $url_image = config('contains.SRC_AVATAR_DEFAULT');
        $result = $this->userRepository->find($request->id)->toJson();
        
        if($request->newPassword != null)
        {
            $data['password'] = bcrypt($request->newPassword);
            $user = $this->userRepository->update($request->id, $data);
        }
        
        if($request->dataImage != null)
        {
            $img = $this->upload($request->dataImage);
            if($img['success'])
            {
                $data['avatar'] = $img['name'];
                $user = $this->userRepository->update($request->id, $data);
            }
        }
        
        if($request->newPassword == null && $request->dataImage == null)
        {
            $request->session()->flash('message',[
                'level' => 'warning',
                'title' => __('user.title_update'),
                'content' => __('user.update_warning')
            ]);
            return view('user/update', compact('result','path','callingcode','url_image'));
        }
        if(empty($user))
        {
            $request->session()->flash('message',[
                'level' => 'danger',
                'title' => __('user.title_update'),
                'content' => __('user.update_error')
            ]);
        }
        return view('user/update', compact('result','path','callingcode','url_image'));
    }
    
    /**
     * @author: HueNT
     * @todo check unique email
     * @param Request $request
     * @return boolean
     */
    public function checkMailExits(Request $request)
    {
        return $this->userRepository->checkUniqueCreate($request->field, $request->email);
    }
    
    /**
     * @author HueNT
     * @todo check username input is unique
     * @param Request $request
     */
    public function checkUsername(Request $request)
    {
        if($this->checkLDap($request->username))
        {
            return 1;
        }
        
        return $this->userRepository->checkUniqueCreate($request->field, $request->username);
    }
    
    /**
     * @author HueNT
     * @todo check mail when update
     * @param Request $request
     * @param unknown $id, $request
     * @return boolean
     */
    public function checkMailUpdate(Request $request)
    {
        return $this->userRepository->checkUniqueForUpdate($request->id, $request->field, $request->email);
    }
    
    /**
     * @author: HueNT
     * @todo get form edit infor user
     * @param $id, $request
     */
    public function update(Request $request, $id)
    {
        $path = config('contains.TARGET_UPLOAD_DIR');
        $callingcode = json_encode(config('contains.CALLING_CODE'));
        $url_image = config('contains.SRC_AVATAR_DEFAULT');
        
        $user = $this->userRepository->find($id);
        
        if($user)
        {
            $result = $user->toJson();
            return view('user/update', compact('result','path','callingcode','url_image'));
        }
        
        $request->session()->flash('message',[
            'level' => 'danger',
            'title' => __('user.title_danger'),
            'content' => __('user.content_danger')
            ]);
        
        return redirect()->back();
    }

    /**
     * @author HueNT
     * @todo check username when update
     * @param Request $request
     * @return boolean
     */
    public function checkUsernameUpdate(Request $request)
    {
        return $this->userRepository->checkUniqueForUpdate($request->id, $request->field, $request->username);
    }
    
    /**
     * @author HueNT
     * @todo save item user when update information
     * @param Request $request
     * @return boolean
     */
    public function saveUpdate(Request $request)
    {
        $data = $request->all();
        $result = $this->userRepository->update($request->id, $data);
        if($result)
        {
            return  $request->session()->flash('message',[
                    'level' => 'success',
                    'title' => __('user.title_update'),
                    'content' => __('user.update_success')
                ]);
        }
        return  $request->session()->flash('message',[
            'level' => 'danger',
            'title' => __('user.title_update'),
            'content' => __('user.update_error')
        ]);
    }
    
    /**
     * @author HueNT
     * @todo soft delete user in database
     * @param Request $request
     * @return boolean
     */
    public function delete(Request $request)
    {
        $this->userRepository->delete($request->id);
    }
    
    /**
     * @author HueNT
     * @todo search user with key word
     * @return \Illuminate\Http\JsonResponse
     */
    public function search($key){
        $response = $this->userRepository->search($key);
        
        return response()->json($response, 200);
    }
    
    /**
     * @author: HueNT
     * @todo check account exists in LDapServer
     * @param unknown $param
     */
    public function checkLDap($username)
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
        // connect ldap
        $ldapconn = Helper::connect_ldap($config);
        
        if($ldapconn)
        {
             $ldapbind = ldap_bind($ldapconn, $config['bind_dn'], $config['bind_password']);
             if($ldapbind){
                 // search LDAP user
                 $user_search = ldap_search($ldapconn,$config['base_dn'],"(|(uid=$username))");
                 //get user entries
                 $user_get = ldap_get_entries($ldapconn, $user_search);
                 ldap_close($ldapconn);
                 return $user_get['count'] === 1;  
             } 
        }
    }
    
    /**
     * @author huent6810
     * @todo get all members of manager's division
     * @param int $id
     */
    public function getMember($id)
    {   
        set_time_limit(0);
        $response = $this->userRepository->find($id);
        
        if($response->division_id){
            $result = $this->userRepository->getMemberOfDivision($id);// get all members has manage_id is $response->id
        }
        else 
            $result = [];
        return response()->json($result, 200);
    }
    
    /**
     * @author huent6810
     * @todo Active and Inactive user account
     * @param Request $request
     * @return boolean
     */
    public function activeAccount(Request $request)
    {
        $this->userRepository->activeAccount($request->account);
    }
}
