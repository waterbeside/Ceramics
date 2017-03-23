<?php

// +----------------------------------------------------------------------
// | 搜索
// +----------------------------------------------------------------------
// | Copyright (c) 2015 http://www.5mell.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: Loch Kan <454831746@qq.com>
// +----------------------------------------------------------------------



class SearchAction extends BaseAction {

    public $lang = 'CH';
    public $siteInfo = array();
    //初始化
    protected function _initialize() {
        parent::_initialize();
        $this->lang = defined('LANGUAGE') && in_array(LANGUAGE, array('CH','EN')) ?  LANGUAGE :  (I('get.lang','','strtolower') == 'en' ? 'EN' : 'CH');        
        $this->siteInfo = AppframeAction::$Cache['Config'];
    }

  




    //搜索
    public function index() {        
        if (IS_POST) {
            $this->redirect('index', $_POST);
        }

        $keyword = I('get.keyword','','trim');
        $chl = I('get.chl','product','trim');
        if($keyword==''){
            $datas =array();
        }else{
            $channelArray = array('news'=>'news','product'=>'products','down'=>'download','effect'=>'effectpic');
            if(!isset($channelArray[$chl])){
                $this->error('参数错误');
            }
            $where = array('ViewFlag'.$this->lang=>1);
            switch ($chl) {
                case 'news':
                    $urlMCA =  'Index/newsshow';
                    $titleField = 'NewsName'.$this->lang ;
                    $where[$titleField] =  array('like', '%'.$keyword.'%');
                    break;
                case 'product':
                    $urlMCA =  'Products/shows';
                    $titleField = 'ProductName'.$this->lang ;
                    $whereLike[$titleField]  = array('like', '%'.$keyword.'%');
                    $whereLike['ProductModel']  = array('like','%'.$keyword.'%');
                    $whereLike['Sizes']  = array('like','%'.$keyword.'%');
                    $whereLike['_logic'] = 'or';
                    $where['_complex'] = $whereLike;
                    break;
                case 'effect':
                    $urlMCA =  'Products/shows';
                    $titleField = 'EffectName'.$this->lang ;
                    $whereLike[$titleField] =  array('like', '%'.$keyword.'%');
                    $whereLike['ProductModel'] =  array('like', '%'.$keyword.'%');
                    $whereLike['_logic'] = 'or';
                    $where['_complex'] = $whereLike;
                    break; 
                case 'down':
                    $urlMCA =  'Reseller/shows';
                    $titleField = 'DownName'.$this->lang ;
                    $where[$titleField] =  array('like', '%'.$keyword.'%');
                    break;
                default:
                    # code...
                    break;
            }

            $table = $channelArray[$chl];
            $DB = M($table) ; 

            

            $count = $DB->where($where)->count();
            $Page = $this->page($count, 20);        
            $datas = $DB->where($where)->limit($Page->firstRow.','.$Page->listRows)->order(array("ID" => "desc"))->select();
    
            $str_t =  "<em class=\"search_keywrod_mark\">".$keyword."</em>";

            if($chl=='product'){
                $DB_Productsort = D('Contents/Productsort');
                $allCatList = $DB_Productsort->getProductsortIds();
                
            }
            
            foreach ($datas as $key => $value) {

                $title = $chl == 'product' ? $value[$titleField].' ['.$value['ProductModel'].']' : ( $chl=='effect' ? str_replace('_','、',$value[$titleField]) : $value[$titleField] ) ;
          
                $datas[$key]['title'] = str_replace($keyword,$str_t,$title);
                $datas[$key]['url'] =   $chl == 'effect' ? U($urlMCA,array('id'=>$value['ProductID'])) : U($urlMCA,array('id'=>$value['ID'])) ;

                if($chl == 'product'){
                    $datas[$key]['catInfo'] = $allCatList[$value['SortID']];
                    $datas[$key]['bigCate'] = $DB_Productsort->getBigClass($datas[$key]['catInfo']);

                    $model_array = explode(",",$value["ProductModel"]);
                    $sizes_array = explode(",",$value["Sizes"]);
                    $SizeBigpic_array = explode(",",$value["SizeBigpic"]);
                    $SizeSmallpic_array = explode(",",$value["SizeSmallpic"]);
                    $len_model = count($model_array);
                    foreach ($model_array as $k => $v) {
                        $datas[$key]["model_list"][$k]["model"]  =trim($model_array[$k]);
                        $datas[$key]["model_list"][$k]["size"]   =reBSsize(trim($sizes_array[$k]),1);
                        $datas[$key]["model_list"][$k]["pic"]    =trim($SizeBigpic_array[$k]);
                        $datas[$key]["model_list"][$k]["thumb"]  =trim($SizeSmallpic_array[$k]);
                    }

                }
            }
            $this->assign("showPages", $Page->show());
            $this->assign("count", $count);

        }
        
        

        $SEO = seo("",0, "搜索结果", $this->siteInfo['CFG_SITE_DESCRIPTION'], $this->siteInfo['CFG_SITE_KEYWORD']);
        $this->assign("keyword", $keyword);
        $this->assign("chl", $chl);
        $this->assign("datas", $datas);
        $this->assign("SEO", $SEO);
        $this->display($this->parseTpl('search_index'));
    }




}