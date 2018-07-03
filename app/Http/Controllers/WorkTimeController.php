<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Helper;
use App\ProjectUser;
use App\Repositories\WorkTime\WorkTimeRepositoryInterface;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\App;

class WorkTimeController extends Controller {

    protected $worktimeRepository;

    public function __construct(WorkTimeRepositoryInterface $worktimeRepository) {

        $this->worktimeRepository = $worktimeRepository;
    }

    /**
     * @author TheuNT
     * @todo get list worktime
     * @return array
     */
    public function getList($pager)
	{
        $limit = ( $pager + 1 ) * 5;
		$response = $this->worktimeRepository->getList($limit);
		return response()->json($response, 200);
	}

    /**
     * @author TheuNT
     * @todo get project list call from HomeController
     * @return array
     */
    public function getProject() {
        return $this->worktimeRepository->getProject();
    }

    /**
     * @author DungHT
     * @param Request $request
     * @return json string
     */
    public function createWorkTime(Request $request) {
        $response = [];

        //save data to database
        $result = $this->worktimeRepository->setDataAddNew($request->data)->createWorkTime();
        if (count($result) > 0) { //if response data > 0
            $response['data'] = $result;
        }//return array error message

        $response['errors'] = $this->worktimeRepository->errors;

        return response()->json($response, 200);
    }

    /**
     * @author TheuNT
     * @todo update project
     * @param Request $request
     * @return json string
     */
    public function updateProject(Request $request) {
        $response = $this->worktimeRepository->updateProject($request->id, $request->project_id);
        return response()->json($response, 200);
    }

    /**
     * @author TheuNT
     * @todo update Worktime
     * @param Request $request
     * @return json string
     */
    public function updateWorkTime(Request $request) {
        $result = [
            'code' => 0,
            'data' => []
        ];
        $response = $this->worktimeRepository->updateWorkTime($request->id, $request->work_time);
        if ($response > 0) {
            $result['code'] = 1;
            $limit = ($request->input('page') + 1)*5;
            $result['data'] = $this->worktimeRepository->getList($limit);
        }else {
            $result['data'] = $response;
        }
        return response()->json($result);
    }

    /**
     * @author TheuNT
     * @todo update Tags
     * @param Request $request
     * @return json string
     */
    public function updateTag(Request $request) {
        return response()->json($this->worktimeRepository->updateTag($request->project_user_id, $request->editTag));
    }

    /**
     * @author TheuNT
     * @todo Delete Worktime
     * @param Request $request
     * @return json boolean
     */
    public function deleteWorkTime(Request $request) {
        $response = $this->worktimeRepository->deleteWorkTime($request->id);
        return response()->json($response, 200);
    }
}
