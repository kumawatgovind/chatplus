<?php

namespace App\Helpers;

use App\Http\Repositories\ClusterRepository;
use App\Models\ServiceProfile;
use Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

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

    /**
	 * Run Curl using this function
	 */
	public static function curlRequest($data, $apiUrl, $method = 'POST')
	{
        $payoutKey = config('constants.PAYOUT_KEY_ID');
        $payoutSecret = config('constants.PAYOUT_KEY_SECRET');
        $url = config('constants.PAYOUT_URL') . $apiUrl;
        $jsonData = json_encode($data);
        
        $token = base64_encode($payoutKey.':'.$payoutSecret);
        // dd($url, $data, $token);
		$curlHeader = array(
			"Content-Type: application/json",
			"Authorization: Basic ".$token,
		);
		$requestDataCurl = array(
			CURLOPT_URL => $url, // your preferred url
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_ENCODING => "",
			CURLOPT_MAXREDIRS => 10,
			CURLOPT_TIMEOUT => 86400,
			CURLOPT_CONNECTTIMEOUT => 60000,
			CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
			CURLOPT_CUSTOMREQUEST => $method,
			CURLOPT_POSTFIELDS => $jsonData,
			CURLOPT_HTTPHEADER => $curlHeader
		);

		$curl = curl_init();
		curl_setopt_array(
			$curl,
			$requestDataCurl
		);
		$response = curl_exec($curl);
		$err = curl_error($curl);
		$httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
		curl_close($curl);
		return ['httpCode' => $httpCode, 'response' => $response, 'err' => $err];
	}
    
    /**
     * getXlsxData
     *
     * @param  mixed $filePath
     * @return array
     */
    public static function getXlsxData($filePath)
    {
        $reader = new \PhpOffice\PhpSpreadsheet\Reader\Xlsx();
        $spreadSheet = $reader->load($filePath);
        $excelSheet = $spreadSheet->getActiveSheet();
        return $excelSheet->toArray();
    }
}
