<?php

namespace App\Helpers;

use App\Http\Repositories\ClusterRepository;
use App\Models\ServiceProfile;
use Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class Helper
{

    /**
     * @param $userId
     */
    public static function getUserServiceProfile($userId)
    {
        return ServiceProfile::where('user_id', $userId)->first();
    }

    /**
     * @param $num
     * @return int
     * @throws \Exception
     */
    public static function thousandsFormat($num)
    {
        if ($num > 1000) {
            $x = round($num);
            $x_number_format = number_format($x);
            $x_array = explode(',', $x_number_format);
            $x_parts = array('k', 'm', 'b', 't');
            $x_count_parts = count($x_array) - 1;
            $x_display = $x;
            $x_display = $x_array[0] . ((int) $x_array[1][0] !== 0 ? '.' . $x_array[1][0] : '');
            $x_display .= $x_parts[$x_count_parts - 1];
            return $x_display;
        }
        return $num;
    }


    /**
     * @param $fromDate
     * @param null $toDate
     * @return int
     * @throws \Exception
     */
    public static function dateDiff($fromDate, $toDate = null)
    {
        if (empty($toDate)) {
            $toDate = date('Y-m-d H:i:s');
        }
        $diff = strtotime($toDate) - strtotime($fromDate);
        $days = abs(floor($diff / 86400));
        if (empty($days)) {
            return "1";
        }
        return $days;
    }


    /**
     * @param $startDate
     * @param $endDate
     * @return array
     * @throws \Exception
     */
    public static function betweenDates($startDate, $endDate)
    {
        $period = new \DatePeriod(
            new \DateTime($startDate),
            new \DateInterval('P1D'),
            new \DateTime($endDate)
        );
        $data = [];
        foreach ($period as $key => $value) {
            $data[] = $value->format('Y-m-d');
        }
        $data[] = date('Y-m-d', strtotime($endDate));
        return array_unique($data);
    }
}
