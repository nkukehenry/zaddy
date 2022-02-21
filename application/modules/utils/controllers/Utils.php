<?php

class Utils extends MX_controller{

   function __construct(){
 
   parent::__construct();

 }

 
//http requests
 public function sendHttpPost($url,$headers=[],$body){
 
      $ch = curl_init($url);
 
      file_put_contents(LOG_FILE, "\n HEADERS OUT ".json_encode($headers),FILE_APPEND);
       //post values
      curl_setopt($ch,CURLOPT_POSTFIELDS,$body);
      // Option to Return the Result, rather than just true/false
      curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
      // Set Request Headers
      curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
      //time to wait while waiting for connection...indefinite
      curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 0);

      curl_setopt($ch,CURLOPT_POST,1);
      //set curl time..processing time out
      curl_setopt($ch, CURLOPT_TIMEOUT, PROCESS_TIMEOUT);
      // Perform the request, and save content to $result
      ini_set("max_execution_time",EXEC_TIMEOUT);
      $result = curl_exec($ch);
        //curl error handling
        $curl_errno = curl_errno($ch);
                $curl_error = curl_error($ch);
                if ($curl_errno > 0) {
                       curl_close($ch);
                      return  "CURL Error ($curl_errno): $curl_error\n";
                    }
 		 $info = curl_getinfo($ch);
		 file_put_contents(LOG_FILE, "\n REQUEST FULL ".json_encode($info),FILE_APPEND);
         curl_close($ch);
         $decodedResponse =json_decode($result);
         return $decodedResponse;
 }

 public function sendHttpGet($url,$headers,$body=[]){
 
      $ch = curl_init($url);
 
      file_put_contents(LOG_FILE, "\n HEADERS OUT ".json_encode($headers),FILE_APPEND);
       //post values
      //curl_setopt($ch,CURLOPT_POSTFIELDS,$body);
      // Option to Return the Result, rather than just true/false
      curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
      // Set Request Headers
      curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
      //time to wait while waiting for connection...indefinite
      curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 0);

      curl_setopt($ch,CURLOPT_POST,0);
      //set curl time..processing time out
      curl_setopt($ch, CURLOPT_TIMEOUT, PROCESS_TIMEOUT);
      // Perform the request, and save content to $result
      ini_set("max_execution_time",EXEC_TIMEOUT);
      $result = curl_exec($ch);
        //curl error handling
        $curl_errno = curl_errno($ch);
                $curl_error = curl_error($ch);
                if ($curl_errno > 0) {
                       curl_close($ch);
                      return  "CURL Error ($curl_errno): $curl_error\n";
                    }
 
 		 $info = curl_getinfo($ch);
		 file_put_contents(LOG_FILE, "\n REQUEST FULL ".json_encode($info),FILE_APPEND);
         curl_close($ch);
         $decodedResponse =json_decode($result);
         return $decodedResponse;
 }



}


?>