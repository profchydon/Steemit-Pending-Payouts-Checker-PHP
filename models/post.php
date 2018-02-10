<?php

class post {
    
    public function __construct() {
		   @$username = $_GET["username"];
$username = str_replace('@', '', $username);
$this->username      = strtolower($username);
    }


Public function getBlogData() {
$url = 'https://api.steemjs.com/get_discussions_by_blog?query={"tag":"'.$this->username.'","limit":"100"}';
$json= file_get_contents($url);
$data = json_decode($json,true);
return $data;
	}

    public  function getCommentData() {
$url = 'https://api.steemjs.com/get_discussions_by_comments?query={"start_author":"'.$this->username.'","limit":"100"}';
$json= file_get_contents($url);
$data = json_decode($json,true);
return $data;
	}

 public  function processBlogData() {
	$newItems = array();
$total_price =0;
$count = 0;
$username = $this->username;
$data = $this->getBlogData();



foreach ($data as $item) {
	
		
		if(!($item["pending_payout_value"]=="0.000 SBD") && $username == $item["author"])
		{
			
				$post_link = "https://www.steemit.com/@".$item["author"]."/".$item["permlink"];
			
			$author_link = "https://www.steemit.com/@".$item["author"];

			$post_title = substr($item["title"],0,44).'...';
			
			
			
			$price = str_replace(" SBD", "", $item["pending_payout_value"]);
	
			
			
			//Adjustment For 75% Returns for Authors
			if ($username == $item["author"]){
				
				
				if ($item["parent_permlink"] == 'utopian-io'){
					$each_pay  = ($price  * 0.375) - ($price * 0.1125); 
					$total_price = $total_price + $each_pay;
					}
				else {$each_pay  = $price   * 0.375;
						$total_price =$total_price + $each_pay;
				}
				
				
				$each_pay  = $each_pay.' SBD';
				
				
				$newItems[$count]['author_link'] = $author_link;
				$newItems[$count]['author'] = $item["author"];
				$newItems[$count]['post_link'] = $post_link;
				$newItems[$count]['post_title'] = $post_title;
				$newItems[$count]['pending_payout_value'] = $item["pending_payout_value"];
				$newItems[$count]['each_pay'] = $each_pay;
				$newItems[$count]['parent_permlink'] = $item["parent_permlink"];
							
				$nT = str_replace("T"," ",$item["created"]);
				$nT = strtotime($nT);
				$newItems[$count]['created'] = $this->fancy_date($nT);
				
				
				
				
			}
				
$count++;
}

$newItems['total'] = $total_price;
$newItems['usd'] = $this->getSBD();
}

return $newItems;
    }
 
 public  function processCommentData() {
	$newItems = array();
$total_price =0;
$count = 0;
$username = $this->username;
$data = $this->getCommentData();

foreach ($data as $item) {
	
		
		if(!($item["pending_payout_value"]=="0.000 SBD") && $username == $item["author"])
		{
			
				$post_link = "https://www.steemit.com/@".$item["author"]."/".$item["permlink"];
			
			$author_link = "https://www.steemit.com/@".$item["author"];

			$post_title = substr($item["title"],0,44).'...';
			
			
			
			$price = str_replace(" SBD", "", $item["pending_payout_value"]);
	
			
			
			//Adjustment For 75% Returns for Authors
			if ($username == $item["author"]){
				
				
				if ($item["parent_permlink"] == 'utopian-io'){
					$each_pay  = ($price  * 0.375) - ($price * 0.1125); 
					$total_price = $total_price + $each_pay;
					}
				else {$each_pay  = $price   * 0.375;
						$total_price =$total_price + $each_pay;
				}
				
				
				$each_pay  = $each_pay.' SBD';
				
			
				$newItems[$count]['author_link'] = $author_link;
				$newItems[$count]['author'] = $item["author"];
				$newItems[$count]['post_link'] = $post_link;
				$newItems[$count]['post_title'] = $post_title;
				$newItems[$count]['pending_payout_value'] = $item["pending_payout_value"];
				$newItems[$count]['each_pay'] = $each_pay;
				$newItems[$count]['parent_permlink'] = $item["parent_permlink"];
				
				
				$nT = str_replace("T"," ",$item["created"]);
				$nT = strtotime($nT);
				$newItems[$count]['created'] = $this->fancy_date($nT);
				
				
				
				
				
			}
				
$count++;
}

$newItems['total'] = $total_price;
$newItems['usd'] = $this->getSBD();
}

return $newItems;
    }
	
	
	Public function fancy_date($timestamp) 
	{
		
		$wDate = $timestamp + 604800;

		$diff = $wDate-time();
//$diff should be positive and not 0
if( 1 > $diff ){
   exit('Target Event Already Passed (or is passing this very instant)');
} else {
   $d = $diff / 86400 % 7;
   $h = $diff / 3600 % 24;
   $m = $diff / 60 % 60; 
   $s = $diff % 60;

   /***
   if ($d != 0){return " {$d} days, {$h} hours, {$m} minutes and {$s} secs away!";}
   else if ($h != 0) {return "{$h} hours, {$m} minutes and {$s} secs away!";}
   else if ($m != 0) {return "{$m} minutes and {$s} secs away!";}
   else if ($s != 0) {return "{$s} secs away!";}
   **/
   
   return " {$d} days, {$h} hours, {$m} minutes and {$s} secs away!";
}
	
	}
	
	
	Public function getSBD(){
$sbdDetails = file_get_contents('sbd.txt');
$sbd = json_decode($sbdDetails, true); // decode the JSON feed
return round($sbd[0]["price_usd"],3);
	}
	
	Public function getSTEEM(){
$steemDetails = file_get_contents('steem.txt');
$steem = json_decode($steemDetails, true); // decode the JSON feed
return round($steem[0]["price_usd"],3);
	}
	
	
	
	Public function getCURR(){
$currDetails = file_get_contents('curr.txt');
$curr = json_decode($currDetails, true); // decode the JSON feed
$ret = array();
$ret['NGN'] = round($curr["quotes"]["USDNGN"],3);
$ret['EUR'] = round($curr["quotes"]["USDEUR"],3);
$ret['GBP'] = round($curr["quotes"]["USDGBP"],3);
$ret['ZAR'] = round($curr["quotes"]["USDZAR"],3);
$ret['GHS'] = round($curr["quotes"]["USDGHS"],3);
$ret['CAD'] = round($curr["quotes"]["USDCAD"],3);
return $ret;
	}
	
	
	Public function fillSBD(){
	$sbdDetails = file_get_contents('https://api.coinmarketcap.com/v1/ticker/steem-dollars/');
$sbdfile = fopen("sbd.txt", "w") or die("Unable to open file!");
fwrite($sbdfile, $sbdDetails);
	fclose($sbdfile);}
	
  }
  


  
