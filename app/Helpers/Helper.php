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

        $token = base64_encode($payoutKey . ':' . $payoutSecret);
        // dd($payoutKey, $payoutSecret, $token,$url,$jsonData);
        $curlHeader = array(
            "Content-Type: application/json",
            "Authorization: Basic " . $token,
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

    /**
     * encryptCC
     *
     * @param  mixed $plainText
     * @param  mixed $key
     * @return string
     */
    public static function encryptCC($plainText, $key)
    {
        $key = self::hexToBin(md5($key));
        $initVector = pack(
            "C*",
            0x00,
            0x01,
            0x02,
            0x03,
            0x04,
            0x05,
            0x06,
            0x07,
            0x08,
            0x09,
            0x0a,
            0x0b,
            0x0c,
            0x0d,
            0x0e,
            0x0f
        );
        $openMode = openssl_encrypt($plainText, 'AES-128-CBC', $key, OPENSSL_RAW_DATA, $initVector);
        return bin2hex($openMode);
    }

    /**
     * decryptCC
     *
     * @param  mixed $encryptedText
     * @param  mixed $key
     * @return string
     */
    public static function decryptCC($encryptedText, $key)
    {
        $secretKey = self::hexToBin(md5($key));
        $initVector = pack(
            "C*",
            0x00,
            0x01,
            0x02,
            0x03,
            0x04,
            0x05,
            0x06,
            0x07,
            0x08,
            0x09,
            0x0a,
            0x0b,
            0x0c,
            0x0d,
            0x0e,
            0x0f
        );
        $encryptedText = self::hexToBin($encryptedText);
        return openssl_decrypt($encryptedText, "AES-128-CBC", $secretKey, OPENSSL_RAW_DATA, $initVector);
    }

    /**
     * pkcs5_pad
     *
     * @param  mixed $plainText
     * @param  mixed $blockSize
     * @return string
     */
    public static function pkcs5PadCC($plainText, $blockSize)
    {
        $pad = $blockSize - (strlen($plainText) % $blockSize);
        return $plainText . str_repeat(chr($pad), $pad);
    }


    /**
     * ********** Hexadecimal to Binary function for php 4.0 version ******** 
     *
     * @param  mixed $hexString
     * @return string
     */
    public static function hexToBin($hexString)
    {
        $length = strlen($hexString);
        $binString = "";
        $count = 0;
        while ($count < $length) {
            $subString = substr($hexString, $count, 2);
            $packedString = pack("H*", $subString);
            if ($count == 0) {
                $binString = $packedString;
            } else {
                $binString .= $packedString;
            }

            $count += 2;
        }
        return $binString;
    }
}
