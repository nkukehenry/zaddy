<?php

if(!function_exists('get_headers')){
	function get_headers(){

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
