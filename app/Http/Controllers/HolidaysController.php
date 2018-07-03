<?php

namespace App\Http\Controllers;

use App\Helper;
use App\Repositories\Holiday\HolidayRepository;
use Illuminate\Http\Request;

class HolidaysController extends Controller
{
    protected $holidayRepository;

    public function __construct(HolidayRepository $holidayRepository)
    {
        $this->holidayRepository = $holidayRepository;
    }

    public function index () {
        $data = $this->holidayRepository->getList();
        return view('holidays.index',compact('data'));
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function create (Request $request) {
        $result = [
            'code' => false,
            'data' => []
        ];
        $holiday = [
            'day'   => date('Y-m-d',strtotime($request->input('date'))),
            'note'  => $request->input('note')
        ];
        if ($this->holidayRepository->checkIsExits($holiday['day'])) {
            $result['message'] = __('holiday.message.holiday_unique');
            return response()->json($result);
        }
        if ($this->holidayRepository->createHoliday($holiday)) {
            $result['code'] = true;
            $result['data'] = $this->holidayRepository->getList();
        }
        return response()->json($result);
    }

    /**
     * @param Request $request
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function edit (Request $request, $id) {
        $result = [
            'code' => false,
            'data' => []
        ];
        $holiday = [
            'day'   => date('Y-m-d',strtotime($request->input('date'))),
            'note'  => $request->input('note')
        ];
        if ($this->holidayRepository->checkIsExits($holiday['day'],$id)) {
            $result['message'] = __('holiday.message.holiday_unique');
            return response()->json($result);
        }
        if ($this->holidayRepository->editHoliday($holiday,$id)) {
            $result['code'] = true;
            $result['data'] = $this->holidayRepository->getList();
        }
        return response()->json($result);
    }

    /**
     * @param Request $request
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function delete (Request $request, $id) {
        $result = [
            'code' => false,
        ];
        if ($this->holidayRepository->deleteHoliday($id)) {
            $result['code'] = true;
            $result['data'] = $this->holidayRepository->getList();
        }
        return response()->json($result);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function existed(Request $request){
        $id = $request->input('id');
        $holiday = [
            'day'   => date('Y-m-d',strtotime($request->input('date'))),
        ];
        $result = [
            'code' => !$this->holidayRepository->checkIsExits($holiday['day'],$id),
        ];
        return response()->json($result);
    }
}
