<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 11/15/2017
 * Time: 1:57 PM
 */

namespace App\Repositories\Holiday;
use App\Repositories\EloquentRepository;
use Illuminate\Support\Facades\DB;

class HolidayRepository implements  HolidayRepositoryInterFace
{
    /**
     * @param $holiday
     * @return mixed
     */
    public function createHoliday ($holiday) {
        $holiday['created_at'] = date('Y-m-d H:i:s');
        return DB::table('holidays')->insert($holiday);
    }

    /**
     * @return mixed
     */
    public function getList() {
        return DB::table('holidays')
            ->orderBy('day', 'desc')
            ->get();
    }

    /**
     * @param $holiday
     * @param $id
     * @return mixed
     */
    public function editHoliday ($holiday,$id) {
        $holiday['updated_at'] = date('Y-m-d H:i:s');
        return DB::table('holidays')
            ->where('id', $id)
            ->update($holiday);
    }

    /**
     * @param $id
     */
    public function deleteHoliday ($id) {
        return DB::table('holidays')->where('id', '=', $id)->delete();
    }

    /**
     * get list array
     * @return array
     */
    public function getArrayHoliday () {
        $result = [];
        $items = $this->getList();
        foreach ($items as $item) {
            $result[] = $item->day;
        }
        return $result;
    }

    /**
     * @param $day
     * @param null $id
     * @return bool
     */
    public function checkIsExits ($day,$id=null) {
        $query = DB::table('holidays')->where('day', '=',$day);
        if ($id) {
            $query = $query->where('id', '!=', $id);
        }
        if ($query->first()) {
            return true;
        }
        return false;
    }
}