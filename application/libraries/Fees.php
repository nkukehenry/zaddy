<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Fees{

        protected $CI;
        protected $itemFeesTbl = "item_fees";
        protected $feeGroupsTbl = "item_fees";
        protected $itemsTbl = "item_fees";
        protected $billerFeesTbl = "biller_fees";

        public function __construct()
        {
                // Assign the CodeIgniter super-object
                $this->CI = & get_instance(); 
                $this->db = $this->CI->db; //CI DB
        }
	
        public function getItem( $code ){
		return $this->db->where("paymentCode",$code)
			    ->get($billerFeesTbl)
			    ->row();
         }
	
         public function getBillerFees( $id,$amount){
		return $this->db->where("billerId",$id)
			    ->join("feesetup","feesetup.id = biller_fees.feegroupId")
                            ->where("feesetup.upperlimit>= $amount AND (feesetup.lowerlimit-$amount+2)<0")
			    ->get($billerFeesTbl)
			    ->row();
         }

        public function getItemFees( $id,$amount )
        {
               return $this->db->where("itemId",$id)
			    ->join("feesetup","feesetup.id = item_fees.feegroupId")
                            ->where("feesetup.upperlimit>= $amount AND (feesetup.lowerlimit-$amount+2)<0")
			    ->get($this->itemFeesTbl)
			    ->row();
        }

        

}

?>