<?php

// +----------------------------------------------------------------------
// | 网站
// +----------------------------------------------------------------------
// | Copyright (c) 2015 http://www.5mell.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: Loch Kan <454831746@qq.com>
// +----------------------------------------------------------------------



class ProductsAction extends BaseAction {

    public $lang = 'CH';
    public $siteInfo = array();
    //初始化
    protected function _initialize() {
        parent::_initialize();
        $this->lang = defined('LANGUAGE') && in_array(LANGUAGE, array('CH','EN')) ?  LANGUAGE :  (I('get.lang','','strtolower') == 'en' ? 'EN' : 'CH');        
        $this->siteInfo = AppframeAction::$Cache['Config'];
    }


    
    //产品中心
    public function lists(){
        $pageSize = 24;
        $page = I('get.'.C('VAR_PAGE'),1,'intval');        

        $catid = isset($_GET['catid']) ? I('get.catid',0,'intval') : I('get.SortID',0,'intval') ; 
        $notID          = I('get.notid');
        $filterType     = I('get.filtertype');
        $flagType       = I('get.flagType'); 
        $keyword        = I('get.keyword'); 
        $LANG           = $this->lang ;


             
        $sizes = I('get.sizes','','trim');

        $filter = array();
        $filter['catid'] = $catid ;
        $filter['sizes'] = $sizes ? reBSsize($sizes,1) : $sizes;
        $filter['notID'] = $notID ;
        $filter['filterType'] = $filterType ;
        $filter['flagType'] = $flagType ;
        
        $DB_Productsort = D('Contents/Productsort');

        $allCatList = $DB_Productsort->getPublicListArray($this->lang,1);
        // $bigCatList = array(); 
        // foreach ($allCatList as $key => $value) {
        //     if($value['ParentID'] == 0 ){                
        //         $bigCatList[$value['ID']] =$value;
        //     }
        // }
        //缓存  
        $return = D('Public/Products')->getListsPage(array('filter'=>$filter,'page'=>$page,'pageSize'=>$pageSize,'LANG'=>$LANG));
        $datas = $return['datas'] ;
        $count = $return['count'] ;
        $showPages = $return['showPages'] ;
        $catInfo = $return['catInfo'] ;
        $bigCatInfo = $return['bigCatInfo'] ;
        $SEO = $return['SEO'] ;
           //$filter = $return['filter'] ;
        
        $this->assign('showPages', $showPages);
        $this->assign('allCatList', $allCatList); 
        $this->assign('filter', $filter); 
        $this->assign('sizesList', C('sizes_list')); 
        $this->assign('catInfo', $catInfo); 
        $this->assign('catid', $catid); 
        $this->assign('datas', $datas); 
        $this->assign('SEO', $SEO); 
        $this->display($this->parseTpl('list_products'));
    }

    //产品内页
    public function shows(){
        $id          = I('get.id',0,'intval');
        if(!$id){$this->error('内容不存在'); }
        $DB_Product = D('Public/Products');
        $datas = $DB_Product->getShowDatas(array('id'=>$id,'LANG'=>$this->lang));
        if(!$datas){$this->error('该产品不存在，或已淘汰');}

        $seoDesc = $datas['SeoDescription'.$this->lang] ? $datas['SeoDescription'.$this->lang] : strip_tags($catInfo['SortDesc'.$this->lang]);
        $seoKeywords = $datas['SeoKeywords'.$this->lang] ? $datas['SeoKeywords'.$this->lang] : $datas['ProductModel'].','.$datas['ProductName'];
        $SEO = seo("product",$datas['SortID'], $datas['ProductModel'].'-'.$datas['ProductName'.$this->lang]."-产品中心", $seoDesc, $seoKeywords);
        
        
        $this->assign('datas', $datas); 
        $this->assign('SEO', $SEO); 
        $this->display($this->parseTpl('show_products'));
       
    }

    //效果图
    public function effects(){
        $roomtype = str_replace(' ','',I('get.roomtype','','trim'));
        $pid = I('get.pid',0,'intval');
        $catid = I('get.catid',0,'intval');

        $DB_Effectpic = D('Contents/Effectpic');
        $roomtype_list = $DB_Effectpic->getRoomtypes();
        
        // $DB_Productsort = D('Contents/Productsort');
        // $allCatList = $DB_Productsort->getPublicListArray($this->lang,1);

        $where['ViewFlag'.$this->lang] =  1 ;

        if($roomtype && in_array($roomtype, $roomtype_list)){
            $where['roomtype'] = $roomtype;
        }

        

        $SEO = seo("effectpic",0, "空间效果图", $this->siteInfo['CFG_SITE_DESCRIPTION'], $this->siteInfo['CFG_SITE_KEYWORD']);

        $count = $DB_Effectpic->where($where)->count();
        $Page = $this->page($count, 24);        
        $datas = $DB_Effectpic->where($where)->limit($Page->firstRow.','.$Page->listRows)->order(array("ID" => "DESC"))->select();

        $this->assign("showPages", $Page->show());

        $this->assign("roomtype", $roomtype);
        $this->assign("roomtype_list", $roomtype_list); 
        $this->assign("datas", $datas); 
        $this->assign("SEO", $SEO); 
        $this->display($this->parseTpl('list_effects'));

    }


    public function ebooklist(){
        $catid = 17 ;
        $DB_Others = D('Contents/Others');
        $DB_Otherssort = D('Contents/Otherssort');

        $allCateList = $DB_Otherssort->getCateList(1);
        $catInfo = $allCateList[$catid];
        $SEO = seo("others",$catid, '',$catInfo['SortName'.$this->lang],$catInfo['SortName'.$this->lang]);  


        $map['ViewFlag'.$this->lang] = 1 ;
        $map['SortID'] = $catid ;

        $count = $DB_Others->where($map)->count();
        $Page = $this->page($count, 18);        
        $datas = M("Others")->where($map)->limit($Page->firstRow.','.$Page->listRows)->order(array("ID" => "ASC"))->select();  


        $this->assign("showPages", $Page->show());        
        $this->assign("catid", $catid); 
        $this->assign("catInfo", $catInfo);  
        $this->assign("datas", $datas);         
        $this->assign("SEO", $SEO); 

        $this->display($this->parseTpl('list_ebook'));
    }


    //画册细览
    public function ebookshow() {
          
        $id = I('get.id',0,'intval');
        if(!$id){
            $this->error('画册不存在，或已删除');
        }
        $DB_Others = D('Contents/Others');
        
        
        $datas = $DB_Others->where(array('ID'=>$id,'ViewFlag'.$this->lang=>1))->find();        
        if($datas==false){
            $this->error('画册不存在，或已删除');
        }


        $catid = $datas['SortID'];
        $DB_Otherssort = D('Contents/Otherssort');
        $allCateList = $DB_Otherssort->getCateList(1);
        $catInfo = $allCateList[$catid];
        $SEO = seo("others",$catid, '',$catInfo['SortName'.$this->lang],$catInfo['SortName'.$this->lang]);

    
        $this->assign("catList", $catList); 
        $this->assign("catInfo", $catInfo);  
        $this->assign("datas", $datas); 
 
        $this->assign("SEO", $SEO); 
        $this->display($this->parseTpl('show_ebook'));
    }

}