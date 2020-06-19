<?php

function grun_get_stat_from_img($uploaded_file){

 


    //$tmpFile = file_get_contents( $_FILES['file']['tmp_name'] );
    //$strUrl = "https://vision.googleapis.com/v1/images:annotate?key=AIzaSyAYLewmPru1UrMYpOPhbvk2Xp3GXNyd7FU";   
    $strUrl = "https://vision.googleapis.com/v1/images:annotate?key=AIzaSyAjCzAxMSynvpsl4f19XH9OMX0PDnUN_qw"; 
    
    $arrHeader = array();
    $arrHeader[] = "Content-Type: application/json";

    $objImgData = file_get_contents( $uploaded_file['tmp_name'] );
    $objImgBase64 =  base64_encode($objImgData);

    $arrPostData = array();
    $arrPostData['requests'][0]['image']['content'] = $objImgBase64;

    $arrPostData['requests'][0]['features'][0]['type'] = "TEXT_DETECTION";
    $arrPostData['requests'][0]['features'][0]['maxResults'] = "5";

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL,$strUrl);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $arrHeader);
    curl_setopt($ch, CURLOPT_HEADER, false);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($arrPostData));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER,true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    $result = curl_exec($ch);
    curl_close ($ch);
    $json = json_decode(trim($result));
   

    $patterns = Array(
        'GARMIN' => Array(
            'distance' => Array(
                'en' => "/([0-9]?[0-9])([.][0-9][0-9]?)/",
                'th' => "/([0-9]?[0-9])([.][0-9][0-9]?)+[\s]?(nu.|กม)/"
            ),
            'time' => "/([1-5])?:?([0-5][0-9]):?([0-5][0-9])/"
        ),
        'SUUNTO' => Array(
            'distance' => Array(
                'en' => "/([0-9]?[0-9])([.][0-9][0-9]?)?+[\s]?(km|KM|Km|กม|nu)/"
            ),
            'time' => "/([1-5])?:?([0-5][0-9])\'?([0-5][0-9])/"
        ),
        'adidas' => Array(
            'distance' => Array(
                'en' => "/A[\s]([0-9]?[0-9])([.][0-9][0-9]?)+[\s]?(km|KM|Km)/"
            ),
            'time' => "/O[\s]([0-5][0-9])?:?([0-5][0-9]):?([0-5][0-9])/"
        ),
        'endomondo' => Array(
            'distance' => Array(
                'en' => "/([0-9]?[0-9])([.][0-9][0-9]?)+[\s]?(nN|nH)/"
            ),
            'time' => "/([1-9])?:?([0-5][0-9]):?([0-5][0-9])/"
        ),
        'STRAVA' => Array(
            'distance' => Array(
                'en' => "/([0-9]?[0-9])([.][0-9|O][0-9|O]?)+[\s]?(km|KM|Km)/"
            ),
            'time' => "/([1-9])?h?[\s]?([1-9]?[0-9])m[\s]([1-9]?[0-9]s)?/",
            'time_alter' => "/\|[\s]*([0-9])?:?([0-5][0-9]):?([0-5][0-9])[\s]*\|/"
        ),
        'Outdoor Run' => Array(
            'distance' => Array(
                'en' => "/Distance[\s]([0-9]?[0-9])([.][0-9][0-9]?)+[\s]?(km|KM|Km)/"
            ),
            'time' => "/km[\s]([1-9])?:?([0-5][0-9]):?([0-5][0-9])/"
        )

    );

    $locale = ($json->responses[0]->textAnnotations[0]->locale);
    $text = ($json->responses[0]->textAnnotations[0]->description);
    
    $is_match = false;
    foreach ( $patterns  as $brand => $ptr){
        if (strpos($text, $brand) !== false) {
            $is_match = true;
            $m = $ptr['distance'][$locale] == null? $ptr['distance']['en'] : $ptr['distance'][$locale];
            if (preg_match($m, $text, $matches)) {
                $distance =  $matches[1] . $matches[2];
                $distance = str_replace("O","0", $distance);
            }
    
            if (preg_match($ptr['time'], $text, $matches)) {
                $time =  $matches[0];
                $time_h = $matches[1];
                $time_m = $matches[2];
                $time_s = str_replace("s","",$matches[3]);
            }
            elseif($ptr['time_alter'] !=null && preg_match($ptr['time_alter'], $text, $matches)){
                $time =  $matches[0];
                $time_h = $matches[1];
                $time_m = $matches[2];
                $time_s = str_replace("s","",$matches[3]);
            }
           
            break;
        }
    }

    return array(
        'distance' => $distance,
        'time' => array(
            'hour' =>  $time_h,
            'minute' => $time_m,
            'second' => $time_s
        )
    );  

}

?>