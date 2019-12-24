<?php

function grun_get_stat_from_img($uploaded_file){

 


    //$tmpFile = file_get_contents( $_FILES['file']['tmp_name'] );
    /*$strUrl = "https://vision.googleapis.com/v1/images:annotate?key=AIzaSyAYLewmPru1UrMYpOPhbvk2Xp3GXNyd7FU";   

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
    
    $text = ($json->responses[0]->textAnnotations[0]->description);

    $distance_pattern = "/[+-]?([0-9]?[0-9])([.][0-9][0-9]?)?+[\s]?(km|KM|Km|กม|nu)/";
    if (preg_match($distance_pattern, $text, $matches)) {
        $distance =  $matches[1] . $matches[2]  ;
    }

    $time_pattern = "/([1-5])?:?([0-5][0-9]):?([0-5][0-9])/";
    if (preg_match($time_pattern, $text, $matches)) {
        $time =  $matches[0];
        $time_h = $matches[1];
        $time_m = $matches[2];
        $time_s = $matches[3];
    }
    
    return array(
        'distance' => $distance,
        'time' => array(
            'hour' =>  $time_h,
            'minute' => $time_m,
            'second' => $time_s
        )
    ); */

    return array(
        'distance' => 0.0,
        'time' => array(
           
        )
    );


}

?>