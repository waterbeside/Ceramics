<?php

// +----------------------------------------------------------------------
// | 网站
// +----------------------------------------------------------------------
// | Copyright (c) 2015 http://www.5mell.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: Loch Kan <454831746@qq.com>
// +----------------------------------------------------------------------



class IndexAction extends BaseAction {

    public $lang = 'CH';
    public $siteInfo = array();
    //初始化
    protected function _initialize() {
        parent::_initialize();
        $this->lang = defined('LANGUAGE') && in_array(LANGUAGE, array('CH','EN')) ?  LANGUAGE :  (I('get.lang','','strtolower') == 'en' ? 'EN' : 'CH');        
        $this->siteInfo = AppframeAction::$Cache['Config'];
    }

  

    public function hits(){
        $channel = I('get.channel','','strtolower');
        $type = I('get.type','','strtolower');
        if(!in_array($type, array('show','list'))){
            exit("document.write(0);");
        }
        $id = I('get.id',0,'intval');
        if(!$id){
            exit("document.write(0);");
        }
        $map['ID'] = $id;   
    
        switch ($channel) {
            case 'news':
                $table = $type=='show' ? 'News' : 'Newssort';
                break;
            case 'product':
                $table = $type=='show' ? 'Product' : 'Productsort';
                break;
            case 'others':
                $table = $type=='show' ? 'Others' : 'Otherssort';
                break; 
            case 'down':
                $table = $type=='show' ? 'Download' : 'Downsort';
            break;                   
            default:
                exit("document.write(0);");
                break;
        }
        $data['ClickNumber'] = array('exp','ClickNumber+1');
        $hits = M($table)->where($map)->getField('ClickNumber');
        $hits = $hits+1;
        if($hits===false){
            exit("document.write(0);");    
        }
        M($table)->where($map)->save($data);
        exit("document.write('".$hits."');");
    }


    //站点首页
    public function index() {        
        //var_dump(self::$Cache['Config']);

        $SEO = seo("","", "", $this->siteInfo['CFG_SITE_DESCRIPTION'], $this->siteInfo['CFG_SITE_KEYWORD']);
        $DB_Ad = M('adsfigure');
        $bannerList = $DB_Ad->where(array('zoneid'=>1,'flag'=>1))->order('orders DESC ,adsid DESC')->limit(5)->select();        
        $DB_News = M('news');
        $topNews = $DB_News->field('ID,NewsNameCH,NewsNameEN,thumb,SeoDescriptionCH,SeoDescriptionCH')->where(array('ViewFlag'.$this->lang=>1,'NoticeFlag'=> 1))->order('ID DESC')->find();
        
        $indexNews = $DB_News->where(array('ViewFlag'.$this->lang=>1))->order('ID DESC')->limit(10)->select();


        $this->assign("topNews", $topNews);
        $this->assign("indexNews", $indexNews);
        $this->assign("SEO", $SEO);        
        $this->display($this->parseTpl('index'));
    }


    //新闻列表
    public function newslist() {
                
        $catid = isset($_GET['catid']) ? I('get.catid',0,'intval') : I('get.SortID',0,'intval') ;


        
        $where =array('ViewFlag'.$this->lang=>1);

        $catList = D('Contents/Newssort')->getCateList(1,$this->lang);
        if($catid){
            $catInfo = $catList[$catid];
            $where['SortID'] = $catid;
        }else{
            $catInfo = array();
        }

        $SEO = seo("news",$catid, "新闻资讯", $this->siteInfo['CFG_SITE_DESCRIPTION'], $this->siteInfo['CFG_SITE_KEYWORD']);

        $count = M("News")->where($where)->count();
        $Page = $this->page($count, 20);        
        $datas = M("News")->where($where)->limit($Page->firstRow.','.$Page->listRows)->order(array("ID" => "desc"))->select();

        $this->assign("showPages", $Page->show());
        $this->assign("catList", $catList); 
        $this->assign("catInfo", $catInfo); 
        $this->assign("datas", $datas); 
        $this->assign("SEO", $SEO); 
        $this->display($this->parseTpl('list_news'));
    }

    //新闻内容
    public function newsshow() {
          
        $id = I('get.id',0,'intval');
        if(!$id){
            $this->error('文章不存在，或已删除');
        }
        $DB_News = D('Contents/News');
        
        $datas = $DB_News->where(array('ID'=>$id,'ViewFlag'.$this->lang=>1))->find();        
        if($datas==false){
            $this->error('文章不存在，或已删除');
        }
        if($datas['SourceEN'] == 1 || $datas['SourceEN'] == '跳转'){
            redirect($datas['SourceCH']);
            exit();
        }
        

        $catid = $datas['SortID'];
        $catList = D('Contents/Newssort')->getCateList(1,$this->lang);
        $catInfo = $catList[$catid];
        $SEO = seo("news",$catid, $datas['NewsName'.$this->lang], $datas['SeoDescription'.$this->lang], $datas['SeoKeywords'.$this->lang]);        

        $nextItem = $DB_News->getNext($id,1,$catid,$this->lang);
        $prevItem = $DB_News->getNext($id,-1,$catid,$this->lang);

        $this->assign("catList", $catList); 
        $this->assign("catInfo", $catInfo);  
        $this->assign("datas", $datas); 
        $this->assign("nextItem", $nextItem); 
        $this->assign("prevItem", $prevItem); 
        $this->assign("SEO", $SEO); 
        $this->display($this->parseTpl('show_news'));
    }


    //关于我们
    public function about() {
            
        $id = I('get.id',1,'intval');        
        $catid = $id;
        if(!$id){
            $this->error('文章不存在，或已删除');
        }
        $DB_About = D('Contents/About');


        $catList = $DB_About->getCateList(1,$this->lang);
        foreach ($catList as $key => $value) {
            if(!in_array($value['ID'], array(1,2,3,4))){
                unset($catList[$key]);
            }
        }
        
        $datas = $DB_About->where(array('ID'=>$id,'ViewFlag'.$this->lang=>1))->find();        
        if($datas==false){
            $this->error('文章不存在，或已删除');
        }
        if(in_array($catid, array(3,4))){
            switch ($catid) {
                case 3:
                    $othersID = 48;                    
                    break;
                case 4:
                    $othersID = 50;
                    break;
             
            }           
            $subCatList =  D('Contents/Otherssort')->getChannelTree($othersID);
            $cid   = I('get.cid',0,'intval');
            
            
            $template = 'list_photo_about';
            $this->assign("subCatList", $subCatList);
            $this->assign("cid", $cid); 
        }else{
            $template = 'page_about';
        }
        
        $catInfo = $catList[$catid];
        $SEO = seo("about",$catid, $datas['AboutName'.$this->lang], $datas['SeoDescription'.$this->lang], $datas['SeoKeywords'.$this->lang]);        

        //$where['_string'] = 'FIND_IN_SET('.$value.',odor)';


        $this->assign("catid", $catid); 
        $this->assign("catList", $catList); 
        $this->assign("catInfo", $catInfo);  
        $this->assign("datas", $datas);         
        $this->assign("SEO", $SEO); 
        $this->display($this->parseTpl($template));
    }



    //工程案例
    public function cases(){
        $catid = I('get.catid',29,'intval');                
        
        $DB_Others = D('Contents/Others');
        $DB_Otherssort = D('Contents/Otherssort');

        $catList = C('menu_marketing');

        $allCateList = $DB_Otherssort->getCateList(1);
        $catInfo = $allCateList[$catid];
        $subCatList =  $DB_Otherssort->getChannelTree(29);
        $firstSubID = $subCatList[0]['ID'];
        $childID_Array = array();
        $childID_string = "";
        foreach ($subCatList as $key => $value) {
             $childID_array[] = $value['ID'];
             $childID_string .=  $value['ID'].',';
        }
        $childID_string = substr($childID_string,0,strlen($childID_string)-1); 
        $catid = in_array($catid, $childID_array) ? $catid : $firstSubID;
        $map['ViewFlag'.$this->lang] = 1 ;
        $map['SortID'] = $catid ;
        //$map['_string'] = 'FIND_IN_SET('.$catid.',SortPath)';
        $count = $DB_Others->where($map)->count();
        $Page = $this->page($count, 20);        
        $datas = M("Others")->where($map)->limit($Page->firstRow.','.$Page->listRows)->order(array("ID" => "ASC"))->select();  
        $SEO = seo("others",$catid, '工程案例', '工程案例','工程案例');        
     
        $this->assign("showPages", $Page->show());        
        $this->assign("catid", $catid); 
        $this->assign("subCatList", $subCatList); 
        $this->assign("catList", $catList); 
        $this->assign("catInfo", $catInfo);  
        $this->assign("datas", $datas);         
        $this->assign("SEO", $SEO); 
        $this->display($this->parseTpl('list_cases'));
    }

    //展厅风采
    public function room(){
        $defaultCatiID = 51 ;
        $catid = I('get.catid',$defaultCatiID,'intval');                
        
        $DB_Others = D('Contents/Others');
        $DB_Otherssort = D('Contents/Otherssort');

        $catList = C('menu_marketing');

        $allCateList = $DB_Otherssort->getCateList(1);
        $catInfo = $allCateList[$catid];
        $subCatList =  $DB_Otherssort->getChannelTree($defaultCatiID);        
        //$firstSubID = $subCatList[0]['ID'];
        $firstSubID = $defaultCatiID;
        $childID_Array = array();
        $childID_string = "";
        foreach ($subCatList as $key => $value) {
             $childID_array[] = $value['ID'];
             $childID_string .=  $value['ID'].',';
        }
        $childID_string = substr($childID_string,0,strlen($childID_string)-1); 
        $catid = in_array($catid, $childID_array) ? $catid : $firstSubID;
        $map['ViewFlag'.$this->lang] = 1 ;
        // $map['SortID'] = $catid ;
        $map['_string'] = 'FIND_IN_SET('.$catid.',SortPath)';
        $count = $DB_Others->where($map)->count();
        $Page = $this->page($count, 20);        
        $datas = M("Others")->where($map)->limit($Page->firstRow.','.$Page->listRows)->order(array("ID" => "ASC"))->select();  
        $SEO = seo("others",$catid, '总部展厅', '总部展厅','总部展厅');        
        $this->assign("showPages", $Page->show());        
        $this->assign("catid", $catid); 
        $this->assign("subCatList", $subCatList); 
        $this->assign("catList", $catList); 
        $this->assign("catInfo", $catInfo);  
        $this->assign("datas", $datas);         
        $this->assign("SEO", $SEO); 
        $this->display($this->parseTpl('list_room'));
      
    }


    //终端展厅
    public function stores(){
        $defaultCatiID = 57 ;
        $catid = I('get.catid',$defaultCatiID,'intval');                
        
        $DB_Others = D('Contents/Others');
        $DB_Otherssort = D('Contents/Otherssort');

        $catList = C('menu_marketing');

        $allCateList = $DB_Otherssort->getCateList(1);
        $catInfo = $allCateList[$catid];
        $subCatList =  $DB_Otherssort->getChannelTree($defaultCatiID);        
        $firstSubID = $subCatList[0]['ID'];        
        $childID_Array = array();
        $childID_string = "";
        foreach ($subCatList as $key => $value) {
             $childID_array[] = $value['ID'];
             $childID_string .=  $value['ID'].',';
        }
        $childID_string = substr($childID_string,0,strlen($childID_string)-1); 
        $catid = in_array($catid, $childID_array) ? $catid : $firstSubID;
        $map['ViewFlag'.$this->lang] = 1 ;
        // $map['SortID'] = $catid ;
        $map['_string'] = 'FIND_IN_SET('.$catid.',SortPath)';
        $count = $DB_Others->where($map)->count();
        $Page = $this->page($count, 20);        
        $datas = M("Others")->where($map)->limit($Page->firstRow.','.$Page->listRows)->order(array("ID" => "DESC"))->select();  
        $SEO = seo("others",$catid, '终端展厅', '终端展厅','终端展厅');        
        $this->assign("showPages", $Page->show());        
        $this->assign("catid", $catid); 
        $this->assign("subCatList", $subCatList); 
        $this->assign("catList", $catList); 
        $this->assign("catInfo", $catInfo);  
        $this->assign("datas", $datas);         
        $this->assign("SEO", $SEO); 
        $this->display($this->parseTpl('list_stores'));
    }



    //联系我们
    public function contactus(){
        $id = I('get.id',17,'intval');        
        $catid = $id;
        if(!$id){
            $this->error('文章不存在，或已删除');
        }
        $DB_About = D('Contents/About');


        $catList = $DB_About->getCateList(1,$this->lang);
        foreach ($catList as $key => $value) {
            if(!in_array($value['ID'], array(16,17,18))){
                unset($catList[$key]);
            }
        }
        
        $datas = $DB_About->where(array('ID'=>$id,'ViewFlag'.$this->lang=>1))->find();        
        if($datas==false){
            $this->error('文章不存在，或已删除');
        }
         
        switch ($catid) {
            case 16:
                $template = 'page_contact';
                break;
            case 17:
                $template = 'page_map';
                break;
         
        }
        
        $catInfo = $catList[$catid];
        $SEO = seo("about",$catid, '联系我们', '联系我们','联系我们');  
        //$where['_string'] = 'FIND_IN_SET('.$value.',odor)';


        $this->assign("catid", $catid); 
        $this->assign("catList", $catList); 
        $this->assign("catInfo", $catInfo);  
        $this->assign("datas", $datas);         
        $this->assign("SEO", $SEO); 
        $this->display($this->parseTpl($template));
    }



}