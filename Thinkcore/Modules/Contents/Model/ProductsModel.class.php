<?php

/**
 * 產品管理模型
 */
class ProductsModel extends CommonModel {


    //自动验证 array(验证字段,验证规则,错误提示,[验证条件,附加规则,验证時間])
    protected $_validate = array(
        array('ProductNameCH', 'require', '请输入中文名'),
        array('SortID', 'require', '请选择分类'),
        array('ProductModel', 'require', '请填写型号'),
        array('Sizes', 'require', '请填写规格'),
        array('SmallPic', 'require', '默认小图不能为空'),
        array('BigPic', 'require', '默认大图不能为空'),
       
    );
    //自动完成 array(填充字段,填充内容,[填充条件,附加规则])
    protected $_auto = array(
       array('AddTime', 'formatAddtime', 1, 'callback'),
       array('UpdateTime', 'formatAddtime', 3, 'callback'),

       array('ViewFlagCH', 'formatStatus', 2, 'callback'),
       array('ViewFlagEN', 'formatStatus', 2, 'callback'),
       array('NewFlag', 'formatStatus', 2, 'callback'),
       array('CommendFlag', 'formatStatus', 2, 'callback'),

       array('ProductNo', 'formatProductNo',1,'callback'),
       array('ClickNumber', 0,1),
       array('N_Price', 0,1),
       array('P_Price', 0,1),
       array('Stock', 10000,1),
       array('UnitCH', '块',1),
       array('MakerCH', '广东#lazyme#陶瓷有限公司',1),
       array('MakerEN', '#lazyme# Ceramics Group',1),       
    ); 
  
    public function formatAddtime() {
        return date("Y-m-d H:i:s");
    }

    public function formatProductNo() {
        $now = date('Y-m-d H:i:s',time());
        $nowArr1 = explode(" ",$now);
        $nowArr2 = explode(":",$nowArr1[1]);
        $ProductNo=$nowArr2[0].$nowArr2[1].$nowArr2[2]."-".rand(100,900);
        return  $ProductNo;
    }

    public function formatStatus($str){
        $str = empty($str) ? 0 : $str ;
        return $str;
    }

    //解析 一套产品所含之破列表
    public function parseItems($data){
        if(!$data['ProductModel']){
            return false;
        }
        $pro_models= explode(',', $data['ProductModel']) ;
        $pro_sizes= explode(',', $data['Sizes']);
        $pro_thumb= explode(',', $data['SizeSmallpic']);
        $pro_pic= explode(',', $data['SizeBigpic']);

    }

   
     //替换，尺寸排大小，$reSizeType: 1大在前，-1小在前，0不变,2反转
    public function reSize($sizeStr,$delimiter='X',$reSizeType=0){
        if(strpos($sizeStr,$delimiter)==-1){
            return $sizeStr;
        }else{
            $strArr = explode($delimiter,$sizeStr);
            $strArr[0]=intval($strArr[0]);
            $strArr[1]=intval($strArr[1]);
            if($reSizeType=="1"){
                if($strArr[0]-$strArr[1]<0){
                    $sizeStr =  $strArr[1].$delimiter.$strArr[0];
                }else{
                    $sizeStr =  $sizeStr;
                }
            }elseif($reSizeType=="-1"){
                if($strArr[0]-$strArr[1]>0){
                    $sizeStr =  $strArr[1].$delimiter.$strArr[0];
                }else{
                    $sizeStr =  $sizeStr;
                }
            }elseif($reSizeType=="2"){
                $sizeStr =  $strArr[1].$delimiter.$strArr[0];
            }else{
                $sizeStr =  $sizeStr;
            }
            return $sizeStr ;
        }
    }


}

