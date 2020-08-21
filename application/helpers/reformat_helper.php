<?php
defined('BASEPATH') OR exit('No direct script access allowed');

if ( ! function_exists('reformat_date'))
{
    function reformat_date($date){
		$split = explode("-", $date);
        switch($split[1]){
			case 01:
			return $split[2]." Januari ".$split[0];
			break;
			case 02:
			return $split[2]." Februari ".$split[0];
			break;
			case 03:
			return $split[2]." Maret ".$split[0];
			break;
			case 04:
			return $split[2]." April ".$split[0];
			break;
			case "05":
			return $split[2]." Mei ".$split[0];
			break;
			case "06":
			return $split[2]." Juni ".$split[0];
			break;
			case "07":
			return $split[2]." Juli ".$split[0];
			break;
			case "08":
			return $split[2]." Agustus ".$split[0];
			break;
			case "09":
			return $split[2]." September ".$split[0];
			break;
			case "10":
			return $split[2]." Oktober ".$split[0];
			break;
			case "11":
			return $split[2]." November ".$split[0];
			break;
			case "12":
			return $split[2]." Desember ".$split[0];
			break;
			default:
			return "Tidak ada periode yang dimaksud";
			break;
		}
    }  
}

if ( ! function_exists('reformat_text'))
{
    function reformat_text($str){
		if(preg_match('/_/', $str)){
			
			$split = explode("_", $str);
		
			return ucfirst($split[0])." ".ucfirst($split[1]);
		}else{
			return ucfirst($str);
		}
    }  
}


	
	
	



    