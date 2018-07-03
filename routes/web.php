<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/
Auth::routes();
Route::middleware(['auth'])->group(function(){
    Route::get('/', 'HomeController@index')->name('home');
    Route::get('/logout', 'Auth\LoginController@logout')->name('logout');

    /**
     * @author: SonNA
     * Route for profile
     */
    Route::prefix('profile')->group(function(){
       Route::get('/show','ProfileController@show')->name('showProfile');
       Route::get('/checkUnique','ProfileController@checkUnique')->name('checkUnique');
       Route::post('/update','ProfileController@update')->name('updateProfile');
    });

    /**	
     * @author: HueNT
     * manage users 
     */
    Route::group(array('prefix' => 'user', 'middleware' => 'manager.admin'), function () {
        Route::get('index','UserController@index')->name('getListUser');
        Route::get('get','UserController@getAll')->name('getAll');
        Route::get('create','UserController@create')->name('getCreateUser');
        Route::post('save', 'UserController@save')->name('insertUser');
        Route::get('update/{id}','UserController@update')->name('getUpdateUser');
        Route::post('change','UserController@saveUpdate')->name('postUpdateUser');
        Route::post('checkMailExits','UserController@checkMailExits')->name('checkMailExits');
        Route::post('checkUsername','UserController@checkUsername')->name('checkUserName');
        Route::post('checkMailUpdate','UserController@checkMailUpdate')->name('checkMailUpdate');
        Route::post('checkUsernameUpdate','UserController@checkUsernameUpdate')->name('checkUsernameUpdate');
        Route::post('delete','UserController@delete')->name('postDeleteUser');
        Route::get('search/{key}','UserController@search');
        Route::post('changeAvatar','UserController@changeAvatar')->name('changeAvatar');
        Route::get('getMember/{id}', 'UserController@getMember');
        Route::post('active', 'UserController@activeAccount');
    });
    
    /**
     * @author huent6810
     * manage role
     */
    Route::group(array('prefix' => 'role', 'middleware' => 'manager.admin'), function () {
        Route::get('getAll', 'RoleController@getAll');
        Route::post('update', 'RoleController@change');
    });
    
	/**
	* @author HueNT6810
	* Project entry time
	*/
	Route::prefix('project')->group(function () {
		Route::get('getList/{id_project_resource}','ReportController@getList');
		Route::get('getAll','ReportController@getAll');
		Route::get('synchData','ReportController@synchData');
		Route::get('filter', 'ReportController@filter');
		Route::get('exportReport','ReportController@export')->name('exportReport');
        // get tag
        Route::get('getAllTag', 'ReportController@getAllTag');
        Route::get('tagSelected', 'ReportController@getTagSelected');
        // get client
        Route::get('getClient', 'ReportController@getClient');
        
        Route::get('synchCondition', 'ReportController@synchCondition');
        Route::post('saveAssign', 'ProjectManagerController@saveAssignMember');
        Route::post('deleteMemberAssigned','ProjectManagerController@deleteMemberAssigned');
	});

	 /**
	   * @author: TheuNT
	   * Route for persional worktime
	   */
	Route::prefix('worktime')->group(function () {
	    Route::get('index','WorkTimeController@index')->name('getListMember');
	    Route::get('getList/{page}','WorkTimeController@getList')->name('getListWorktime');
	    Route::post('createWorkTime','WorkTimeController@createWorkTime')->name('createWorkTime');
	    Route::get('updateProject','WorkTimeController@updateProject')->name('updateProject');
	    Route::post('updateWorkTime','WorkTimeController@updateWorkTime')->name('updateWorkTime');
	    Route::post('updateTag','WorkTimeController@updateTag')->name('updateTagWorktime');
	    Route::post('deleteWorkTime','WorkTimeController@deleteWorkTime')->name('deleteWorkTime');
	});

    /**
      *@author: SonNA
      *@desc project manager
      */
    Route::group(array('prefix' => 'projectManager', 'middleware' => 'manager.admin'), function () {
        Route::get('/','ProjectManagerController@index')->name('project');
        Route::get('/list','ProjectManagerController@getListProject')->name('projectList');
        Route::get('/create','ProjectManagerController@create')->name('projectCreate');
        Route::get('/edit/{id}','ProjectManagerController@edit')->name('projectEdit');
        Route::get('/search','ProjectManagerController@searchProject')->name('projectSearch');
        Route::get('/ExportLog','ProjectManagerController@ExportLogFile')->name('ExportLogFile');
        Route::post('/store','ProjectManagerController@store')->name('projectStore');
        Route::post('/update','ProjectManagerController@update')->name('projectUpdate');
        Route::post('/delete','ProjectManagerController@delete')->name('projectDelete');
        Route::post('/mapping','ProjectManagerController@mappingProject')->name('mappingProject');
        Route::post('/import','ProjectManagerController@importProject')->name('importProject');
    });

    /**
     * @author tienhv
     * @desc Tags manager
     */
    Route::group(array('prefix' => 'tags', 'middleware' => 'manager.admin'), function () {
        Route::get('/','TagsController@index')->name('config');
        Route::get('/search','TagsController@searchTag')->name('search');
        Route::get('/checkUnique','TagsController@existedTag')->name('checkUnique');
        Route::get('/check_using_tag','TagsController@checkUsingTag')->name('check_using_tag');
        Route::post('/create','TagsController@create')->name('create');
        Route::post('/edit/{id}','TagsController@edit')->name('edit');
        Route::post('/delete/{id}','TagsController@delete')->name('delete');

    });
    /**
     * @author: Tienhv
     * @desc Division manager
     */
    Route::group(array('prefix' => 'division', 'middleware' => 'manager.admin'), function () {
        Route::get('/','DivisionManagerController@index')->name('division');
        Route::get('/check','DivisionManagerController@checkIsExists')->name('checkIsExists');
        Route::post('/create','DivisionManagerController@create')->name('create');
        Route::post('/edit/{id}','DivisionManagerController@edit')->name('edit');
        Route::post('/editParent/{id}','DivisionManagerController@editParent')->name('editParent');
        Route::post('/delete/{id}','DivisionManagerController@delete')->name('delete');
        Route::get('/checkUnique','DivisionManagerController@existed')->name('checkUnique');
    });

    Route::get('lang/{lang}', ['as'=>'lang.switch', 'uses'=>'LanguageController@switchLang']);
    Route::get('error', 'HomeController@getError')->name('get.error');

    /**
     * @author: Tienhv
     * @desc Holidays manager
     */
    Route::group(array('prefix' => 'holiday', 'middleware' => 'manager.admin'), function () {
        Route::get('/','HolidaysController@index')->name('holiday');
        Route::post('/create','HolidaysController@create')->name('create');
        Route::post('/edit/{id}','HolidaysController@edit')->name('edit');
        Route::post('/delete/{id}','HolidaysController@delete')->name('delete');
        Route::get('/checkUnique','HolidaysController@existed')->name('checkUnique');
    });
});


