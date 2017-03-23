<?php 
function ruleOutArrayByStr($datas,$ruleOutStr) {
    $array = array();
    foreach ($datas as $key => $value) {
        if(strpos($value,$ruleOutStr)===false){
            $array[] = $value;
        }
    }
    return $array;
    
}

function https_request($url,$data = null){
    $curl = curl_init();
    curl_setopt($curl, CURLOPT_URL, $url);
    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);
    curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, FALSE);
    if (!empty($data)){
        curl_setopt($curl, CURLOPT_POST, 1);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
    }
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
    $output = curl_exec($curl);
    curl_close($curl);
    return $output;
}





//显示产品小菜单
function showProductsSubMenu($datas,$pid,$activeId=0,$filterParam=array()){
    foreach ($datas as $key => $value) {
      if($value['ParentID']==$pid){
        $filterParam['catid']=$value['ID'];
        $url = U('lists',$filterParam);
        echo createCatFilterBtn($activeId,$filterParam,$value['SortNameCH']);
        $isOn = $value['ID'] == $activeId ? 'class="on"' : '';        
        //echo '<a data-catid="'.$value['ID'].'" href="'.$url.'" '.$isOn.'>'.$value['SortNameCH'].'</a>';
        unset($datas[$key]);
      }
    }
    return $datas;
}

//显示一个产品小菜单按钮
function createCatFilterBtn($activeId,$filterParam,$str="",$attr="",$className=""){    
    $url = U('lists',$filterParam);
    $isOn = $filterParam['catid'] == $activeId ? 'on' : '';
    $classStr = $className ? $isOn.' '.$className :$isOn;
    $classStr = trim($classStr) ? 'class = "'.$classStr.'" ' : "";
    return  '<a data-catid="'.$filterParam['catid'].'" href="'.$url.'" '.$classStr.' '.$attr.'>'.$str.'</a>';
    
}


