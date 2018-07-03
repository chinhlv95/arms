<?php

namespace App\Http\Controllers;

use App\Repositories\User\UserRepositoryInterface;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;
use App\Helper;

class ProfileController extends Controller
{

	protected $userRepository;

	public function __construct(UserRepositoryInterface $userRepository){
		$this->userRepository = $userRepository;
	}

	/**
	* Retrieving profile of authenticated user
	* @author SonNA
	* @return Response resource/views/profile/index | $user, $calling_code
	*/
	public function show(){
		$user = Auth::user();
		$calling_code = Config::get('contains.CALLING_CODE');

		return view('profile.index',compact('user','calling_code'));
	}

	/**
	* Update the user's profile.
	* @author SonNA
	* @param  Request  $request
	* @return Response session notify
	*/
    public function update(Request $request){
	    $params = $request->except('id');
		
	    if($request->hasFile('avatar')) {
		    $upload = Helper::uploadFile($request->file('avatar'), config('contains.TARGET_UPLOAD_DIR'), 'arms_avatar_');
		    if($upload['success']){
			    $params['avatar'] = $upload['file_name'];
		    }
	    }

		if(isset($params['password'])){
			$params['password'] = bcrypt($params['password']);
		}

		$query = $this->userRepository->update($request->id, $params);
		
	    if($query){
		    $request->session()->flash('message',[
			    'level' => 'success',
			    'title' => __('auth.profile_edit'),
			    'content' => __('auth.profile_update_success')
		    ]);
	    }else{
		    $request->session()->flash('message',[
			    'level' => 'danger',
			    'title' => __('auth.profile_edit'),
			    'content' => __('auth.profile_update_error')
		    ]);

	    }
    }

	/**
	* Check email existed.
	* @author SonNA
	* @param  Request  $request
	* @return Response bool
	*/
	public function checkUnique(Request $request){
		return $this->userRepository->checkUniqueForUpdate($request->id, $request->field, $request->param);
	}

}
