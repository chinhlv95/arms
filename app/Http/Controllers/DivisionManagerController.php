<?php

namespace App\Http\Controllers;

use App\Repositories\Division\DivisionRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;

class DivisionManagerController extends Controller
{
    protected $divisionRepository;
    public function __construct(DivisionRepository $divisionRepository)
    {
        $this->divisionRepository = $divisionRepository;
    }

    /**
     * @author TienHV
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index () {
        $initData = $this->divisionRepository->getTreeViewDivision();
        return view('division.index',compact('initData'));
    }

    /**
     * @author TienHV
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function create (Request $request) {
        $response = [
            'code'      => 0,
            'message'   => __('origanization.message.create_error'),
            'data'      => []
        ];
        $item = [
            'name'          => $request->input('name'),
            'parent_id'     => $request->input('parent_id')
        ];
        if($this->divisionRepository->checkIsExists($request->input('name'),$request->input('parent_id'))){
            $response['message'] = __('origanization.message.existed_name');
            return response()->json($response);
        };
        if($this->divisionRepository->createNewDivision($item)) {
            $response['code'] = 1;
            $response['message'] = __('origanization.message.create_success');
        }
        $response['data'] = $this->divisionRepository->getTreeViewDivision();

        return response()->json($response);
    }

    /**
     * @author TienHV
     * @param Request $request
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function edit (Request $request, $id) {
        $response = [
            'code'      => 0,
            'message'   => __('origanization.message.update_division_error'),
            'data'      => []
        ];
        $item = [
            'name'          => $request->input('name'),
            'parent_id'     => $request->input('parent_id')
        ];
        if($this->divisionRepository->checkIsExists($request->input('name'),$request->input('parent_id'), $id)){
            $response['message'] = __('origanization.message.add_error');
            return response()->json($response);
        }else {
            $checkIds = $this->divisionRepository->getListDivisionIdByParentId($id);
            $checkIds[] = $id;
            if (!in_array($request->input('parent_id'),$checkIds)) {
                if ($this->divisionRepository->updateDivision($id,$item)) {
                    $response['code'] = 1;
                    $response['message'] = __('origanization.message.update_success');
                }
            }
        };
        $response['data'] = $this->divisionRepository->getTreeViewDivision();
        return response()->json($response);
    }

    /**
     * @author TienHV
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function delete (Request $request, $id) {
        $response = [
            'code'      => 0,
            'message'   => __('origanization.message.delete_division_error'),
            'data'      => []
        ];
        if ($this->divisionRepository->deleteAllDivisionByParentId($id)) {
            $response['code']   = 1;
            $response['message'] = __('origanization.message.delete_success');
            $response['data'] = $this->divisionRepository->getTreeViewDivision();
        }
        return response()->json($response);
    }

    /**
     * @author TienHV
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function checkIsExists (Request $request) {
        $response = ['code' => 0];
        if($this->divisionRepository->checkIsExists($request->input('name'),$request->input('parent_id'))){
            $response['code'] = 1;
        };
        return response()->json($response);
    }

    /**
     * @author TienHV
     * @param Request $request
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function editParent (Request $request,$id) {
        $response = [
            'code'      => 0,
            'message'   => __('origanization.message.update_division_error'),
            'data'      => []
        ];
        $item = [
            'parent_id'     => $request->input('parent_id')
        ];
        $currentItem = $this->divisionRepository->find($id);
        if ($request->input('parent_id') != $currentItem->parent_id) {
            if ($this->divisionRepository->updateDivision($id,$item)) {
                $response['code'] = 1;
                $response['message'] = __('origanization.message.update_success');
            }
        }
        $response['data'] = $this->divisionRepository->getTreeViewDivision();
        return response()->json($response);
    }

    /**
     * @author TienHV
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function existed(Request $request){
        $result = [
            'code' => $this->divisionRepository->checkIsExisted($request->input('name'),$request->input('id')),
        ];
        return response()->json($result);
    }
}
