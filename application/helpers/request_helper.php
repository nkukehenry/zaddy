<?php

if(!function_exists('request_headers')){
	function request_headers(){

        $headers = apache_request_headers();

        if(!isset($headers[AGENT_ID])){

			$headers = $_SERVER;

			$headers[AGENT_ID] = $headers['HTTP_'.AGENT_ID];
			$headers[APP_KEY] = $headers['HTTP_'.APP_KEY];
			$headers[WALLET_KEY] = $headers['HTTP_'.WALLET_KEY];
        }

        return $headers;
	}
}

if(!function_exists('logToFile')){

function logToFile($reqdata,$type){

        $currentdate=date("Y-m-d h:i:s");
        if(is_array($reqdata))
            $reqdata="\n".json_encode($reqdata);
        if(is_object($reqdata))
            $reqdata="\n".json_encode($reqdata);

        $start="\n\n=========".$type." ".$currentdate." =========\n";
        $data=$start.$reqdata;
        file_put_contents(LOG_FILE, $data,FILE_APPEND);

    }
}

if(!function_exists('log_data')){

function log_data($type,$reqdata){

        $currentdate=date("Y-m-d h:i:s");
        if(is_array($reqdata))
            $reqdata="\n".json_encode($reqdata);
        if(is_object($reqdata))
            $reqdata="\n".json_encode($reqdata);

        $start="\n\n=========".$type." ".$currentdate." =========\n";
        $data=$start.$reqdata;
        file_put_contents(LOG_FILE, $data,FILE_APPEND);

    }
}