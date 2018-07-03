<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Config;
use App\Repositories\WorkTime\WorkTimeRepositoryInterface;

class HomeController extends Controller
{
    protected $worktimeRepository;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(WorkTimeRepositoryInterface $worktimeRepository)
    {
        $this->worktimeRepository = $worktimeRepository;
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $calling_code = Config::get('contains.CALLING_CODE');
        $mtool_resource = config('contains.ID_MTOOL_RESOURCE');
        $projects_data = $this->worktimeRepository->getProject();
        
        return view('index', compact('calling_code', 'mtool_resource', 'projects_data'));
    }
    
    /**
     * @author huent6810
     * get error not found
     */
    public function getError()
    {
        return view('error/permission');
    }
}
