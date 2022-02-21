<?php

 class Cache extends MX_Controller{
 
  function __construct()
    {
        // Construct the parent class
        parent::__construct();
       $this->load->library('redis', array('connection_group' => 'slave'), 'redis');
    }
 
 public function setData($key,$data){
   $this->redis->del($key);
   $this->redis->set($key,json_encode($data));
 }
  public function setStr($key,$data){
   $this->redis->del($key);
   $this->redis->set($key,$data);
 }
 
 public function getData($key){
 	$cachedata = $this->redis->get($key);
 
   if($cachedata)
     return json_decode($cachedata );
 }
 
 public function getStr($key){
 	return $this->redis->get($key);
 }
 
  public function trashdata($key){
 	return $this->redis->del($key);
 }
 
 public function cleardata(){
 	return $this->redis->command("flushall");
 }

 public function test_set($categoryId){
 	$billers = Modules::run('billers/getCategoryBillers',$categoryId);
    //print_r($billers);
    $this->setData('BILLERS_'.$categoryId,$billers);
 }
 
  public function test_get($categoryId){
 	print_r(json_decode($this->getData('BILLERS_'.$categoryId)));
 }
 
 
 }



?>