<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class SendMessageController extends Controller
{
    public function send_order_message($data, $phone){
        $api_key='4a7083cb0a61181f';
        $secret_key = 'YjRkYzZkOGI2MjY1N2I5NWM4YTEzNmUzNjdkOTRjY2Q4NWMzNjg2ZmJiZmZkYWE4OGJlODc4OTEzNjRkYWI5Mw==';

        $postData = array(
            'source_addr' => 'B-SMART',
            'encoding'=>0,
            'schedule_time' => '',
            'message' => "Maintanance alert scheduled maintanance, " . $data['equipment'] . ", " . $data['department'] . ", " . $data['serial_number'] . ", " . $data['date'],
            'recipients' => [array('recipient_id' => '1','dest_addr'=>$phone)]
        );

        $Url ='https://apisms.beem.africa/v1/send';

        $ch = curl_init($Url);
        error_reporting(E_ALL);
        ini_set('display_errors', 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt_array($ch, array(
            CURLOPT_POST => TRUE,
            CURLOPT_RETURNTRANSFER => TRUE,
            CURLOPT_HTTPHEADER => array(
                'Authorization:Basic ' . base64_encode("$api_key:$secret_key"),
                'Content-Type: application/json'
            ),
            CURLOPT_POSTFIELDS => json_encode($postData)
        ));

        $response = curl_exec($ch);

        //Decode the response from the API
        $response_data = json_decode($response,true);

        //Check the response code if not equal to 100 (successfully)
        if ($response_data['code'] != 100){
            Log::info($response_data);
        }
        return $response;

//        if($response === FALSE){
//            echo $response;
//
//            die(curl_error($ch));
//        }
//        var_dump($response);
    }
}
