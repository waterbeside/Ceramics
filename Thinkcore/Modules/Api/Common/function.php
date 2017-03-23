<?php 
function fixSrcDomain($str) {
    $str = str_replace('src="/', 'src="http://#/', $str);
    return $str;
    
}

function fixPicDomain($url){
	$domain = "http://#";
	if(strpos($url, 'http')===false){
		if(strpos($url, '/')===0){
			$url =$domain.$url;
		}else{
			str_replace("../","",$url);
			$url = $domain.'/'.$url;
		}
	}; 
	return $url;
}
