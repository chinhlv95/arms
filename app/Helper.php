<?php
/**
	* Helper file
	* User: son_jp
	* Date: 8/29/2017
	* Time: 4:25 PM
	*/
namespace App;

use App;
use Illuminate\Support\Facades\URL;
use Validator;
use Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class Helper{
	//connect ldap function
	public static function connect_ldap($config){
		//connect ldap and set option
		$ldapconn = ldap_connect($config['host'], $config['port']) or die("Could not connect to LDAP server.");
		ldap_set_option($ldapconn, LDAP_OPT_PROTOCOL_VERSION, $config['protocol']) or die('Unable to set LDAP protocol version');
		ldap_set_option($ldapconn, LDAP_OPT_REFERRALS, $config['opt_refer']);

		return $ldapconn;
	}

	/**
	 * Upload file
	 * @date 22/02/2016
	 * @author SonNA6229 <sonna6229@co-well.com.vn>
	 */
	public static function uploadFile($file, $path, $param = '')
	{
		$result = array(
			'success' => false,
			'file_name' => ''
		);
		if($file)
		{
			//get file extension
			$extension = $file->getClientOriginalExtension();
			$result['old_name'] = $file->getClientOriginalName();
			$fileName = $param.rand(1,1000).'_'.'.'.$extension;

			$result['success'] = $file->move($path, $fileName);
			$result['file_name'] = $fileName;
		}

		return $result;
	}

	/**
	 * Upload file
	 * @date 22/02/2016
	 * @author SonNA6229 <sonna6229@co-well.com.vn>
	 */
	public static function uploadMultiFile($files, $path)
	{
		$result = array();
		$uploadcount = 0;
		$file_count = count($files);
		foreach($files as $key=>$file) {
			$rules = array('file' => 'required|max:3072'); //'required|mimes:png,gif,jpeg,txt,pdf,doc'
			$validator = Validator::make(array('file'=> $file), $rules);
			if($validator->passes()){
				$extension = $file->getClientOriginalExtension();
				$filename = rand(1,10).'_'.$key.'.'.$extension;
				//upload file
				$upload_success = $file->move($path, $filename);
				$result[$key]['file_name'] = $filename;
				//get file name old
				$result[$key]['old_name'] = $file->getClientOriginalName();
				$uploadcount ++;
			}
		}
		if(!$uploadcount == $file_count){
			return false;
		}
		return $result;
	}
	
	/**
	 * Set Color
	 */
	public static function renderColors($n) {
		$colors = [];
		$count = is_array($n) ? count($n) : $n;
	
		for ($i = 0; $i < $count; $i++) {
			$r = rand(0, 6);
			$color1 = substr(str_shuffle('ABCDEF'), 0, $r);
			$color2 = substr(str_shuffle('0123456789'), 0, 6 - $r);
			$color = $i % 2 == 0 ? $color1 . $color2 : $color2 . $color1;
			if(!in_array($color, $colors)) {
				$colors[is_array($n) ? $n[$i] : $i] = $color;
			}
			else $i = $i-1;
		}
	
		return $colors;
	}
	
	/**
	 * Check date format
	 */
	public static  function checkDateFormat($string = null) {
		if (trim($string) == '') {
			return false;
		}
	
		$stringReplace = str_replace('/', '-', $string);
		$pattern = '/^[0-9+-]*$/';
	
		if (!preg_match($pattern, $stringReplace)) {
			return false;
		}
	
		$date = explode('-', $stringReplace);
		if (count($date) < 3) {
			return false;
		}
	
		// Check Year
		if ($date[0] <= 0) {
			return false;
		}
		
		// Check month
		if (($date[1] <= 0) || ($date[1] > 12)) {
			return false;
		}
		
		// Check day
		if (($date[2] <= 0) || ($date[2] > 31)) {
			return false;
		}
		
		// check format strtotime
		if (!strtotime($string)) {
			return false;
		}
	
		return true;
	}
                   

	/**
	 * convert utf 8 array
	 */
	static function utf8_converter($array)
	{
		array_walk_recursive($array, function(&$item, $key){
			if(!mb_detect_encoding($item, 'utf-8', true)){
				$item = utf8_encode($item);
			}
		});

		return $array;
	}

	/**
	 * get current url name match router
	 */
	 static function check_current_page($str){
		$match_str = strstr(substr($_SERVER['REQUEST_URI'],1), '/', true ) ? 
					 strstr(substr($_SERVER['REQUEST_URI'],1), '/', true ) : 
					 ( strstr(substr($_SERVER['REQUEST_URI'],1), '?', true ) ? strstr(substr($_SERVER['REQUEST_URI'],1), '?', true ) : substr($_SERVER['REQUEST_URI'] , 1 ) );
		
		return $match_str === $str;
	 }

	/**
	 * Remove dupplicate multidim array
	 */
	 static function unique_multidim_array($array, $key) { 
		$temp_array = array(); 
		$i = 0; 
		$key_array = array(); 
		
		foreach($array as $val) {
			if (!in_array($val[$key], $key_array) && $val[$key] != null) { 
				$key_array[$i] = $val[$key]; 
				$temp_array[$i] = $val; 
			} 
			$i++; 
		} 
		return $temp_array;
	}
	
    /**
     * @author huent6810
     * check user's role
     */
	static function check_permission($role)
	{
	    $items= DB::table('roles')->leftJoin('role_user', 'roles.id', '=', 'role_user.role_id')
	                               ->leftJoin('users', 'role_user.user_id', '=', 'users.id')
	                               ->where('users.id', Auth::id())
	                               ->select('roles.id', 'roles.role_name')
	                               ->distinct('roles.id')
	                               ->get();
	    $arrRole = array_pluck($items, 'id');
	    if(in_array($role, $arrRole))
	        return 1;
	    else
    	    return 0;
	}

    /**
     * format date yyyy-mm-dd
     * @param $startDate
     * @param $endDate
     * @param $holidays
     * @return float|int
     */
    static function getWorkingDays($startDate,$endDate,$holidays) {
        // do strtotime calculations just once
        $endDate = strtotime($endDate);
        $startDate = strtotime($startDate);
        //The total number of days between the two dates. We compute the no. of seconds and divide it to 60*60*24
        //We add one to inlude both dates in the interval.
        $days = ($endDate - $startDate) / 86400 + 1;
        $no_full_weeks = floor($days / 7);
        $no_remaining_days = fmod($days, 7);
        //It will return 1 if it's Monday,.. ,7 for Sunday
        $the_first_day_of_week = date("N", $startDate);
        $the_last_day_of_week = date("N", $endDate);
        //---->The two can be equal in leap years when february has 29 days, the equal sign is added here
        //In the first case the whole interval is within a week, in the second case the interval falls in two weeks.
        if ($the_first_day_of_week <= $the_last_day_of_week) {
            if ($the_first_day_of_week <= 6 && 6 <= $the_last_day_of_week) $no_remaining_days--;
            if ($the_first_day_of_week <= 7 && 7 <= $the_last_day_of_week) $no_remaining_days--;
        }
        else {
            // (edit by Tokes to fix an edge case where the start day was a Sunday
            // and the end day was NOT a Saturday)
            // the day of the week for start is later than the day of the week for end
            if ($the_first_day_of_week == 7) {
                // if the start date is a Sunday, then we definitely subtract 1 day
                $no_remaining_days--;
                if ($the_last_day_of_week == 6) {
                    // if the end date is a Saturday, then we subtract another day
                    $no_remaining_days--;
                }
            }
            else {
                // the start date was a Saturday (or earlier), and the end date was (Mon..Fri)
                // so we skip an entire weekend and subtract 2 days
                $no_remaining_days -= 2;
            }
        }
        //The no. of business days is: (number of weeks between the two dates) * (5 working days) + the remainder
        //---->february in none leap years gave a remainder of 0 but still calculated weekends between first and last day, this is one way to fix it
        $workingDays = $no_full_weeks * 5;
        if ($no_remaining_days > 0 )
        {
            $workingDays += $no_remaining_days;
        }
        //We subtract the holidays
        foreach($holidays as $holiday){
            $time_stamp=strtotime($holiday);
            //If the holiday doesn't fall in weekend
            if ($startDate <= $time_stamp && $time_stamp <= $endDate && date("N",$time_stamp) != 6 && date("N",$time_stamp) != 7)
                $workingDays--;
        }
        return $workingDays;
    }
    
    /**
     * get name collum excel for export data
     * @param unknown $num
     * @return string|unknown
     */
    static function getNameFromNumber($num) {
        $numeric = ($num - 1) % 26;
        $letter = chr(65 + $numeric);
        $num2 = intval(($num - 1) / 26);
        if ($num2 > 0) {
            return Helper::getNameFromNumber($num2) . $letter;
        } else {
            return $letter;
        }
    }
    
    /**
     * get array name collum excel for export data
     */
    static function getArrayNameFromNumber($num) {
        $result = [];
        for($i = 1; $i <= $num; $i++) {
            $result[] = Helper::getNameFromNumber($i);
        }
        return $result;
    }
}