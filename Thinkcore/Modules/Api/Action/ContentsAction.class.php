<?php

// +----------------------------------------------------------------------
// | 网站
// +----------------------------------------------------------------------
// | Copyright (c) 2015 http://www.5mell.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: Loch Kan <454831746@qq.com>
// +----------------------------------------------------------------------



class ContentsAction extends ApibaseAction {

    public $lang = 'CH';
    public $siteInfo = array();
    //初始化
    protected function _initialize() {
        parent::_initialize();
        $keyArray = C('API_KEY');
        $apikey = I('request.apikey','','trim');
        if(!in_array($apikey, $keyArray)){
            $this->error('连接失败','',true);
        }
        $this->lang = defined('LANGUAGE') && in_array(LANGUAGE, array('CH','EN')) ?  LANGUAGE :  (I('get.lang','','strtolower') == 'en' ? 'EN' : 'CH');        
        $this->siteInfo = AppframeAction::$Cache['Config'];
    }

  

    public function hits(){
        $channel = I('get.channel','','strtolower');
        $type = I('get.type','show','strtolower');
        if(!in_array($type, array('show','list'))){
             $this->apiError('0');
        }
        $id = I('get.id',0,'intval');
        if(!$id){
            $this->apiError('0');
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
            
            default:
                $this->apiError('0');
                break;
        }
        $data['ClickNumber'] = array('exp','ClickNumber+1');
        $hits = M($table)->where($map)->getField('ClickNumber');
        $hits = $hits+1;
        if($hits===false){
            $this->apiError('0');   
        }
        M($table)->where($map)->save($data);
        $this->apiSuccess('加载成功',$hits);
    }





    //新闻列表
    public function newstopic() {
                
        $num = I('get.num',1,'intval');    
        $where =array('ViewFlag'.$this->lang=>1);       
 
        $lists = M("News")->where($where)->limit($num)->order(array("ID" => "desc"))->select();     
        if($lists){
            foreach ($lists as $key => $value) {
                $lists[$key] = array('id'=>$value['ID'], 'title' => $value['NewsName'.$this->lang],'time' => $value['AddTime'] ,'catid'=>$value['SortID']);
            }
            if($num>1){
                $datas = $lists;    
            }else{
                $datas = $lists[0];
            }
            
           $this->apiSuccess('加载成功',$datas);
        }else{
            $this->apiError('没有数据');
        }


    }




    //新闻列表
    public function newslist() {
                
        $catid = isset($_GET['catid']) ? I('get.catid',0,'intval') : I('get.SortID',0,'intval') ;
        $page = I('get.'.C('VAR_PAGE'),1,'intval');
        $num = I('get.num',15,'intval');
      
        
        $where =array('ViewFlag'.$this->lang=>1);
        if($catid){
            $catList = D('Contents/Newssort')->getCateList(1,$this->lang);
            $catInfo = $catList[$catid];
            $where['SortID'] = $catid;
        }
        $count = M("News")->where($where)->count();
        $Page = $this->page($count,$num);        
        $lists = M("News")->where($where)->limit($Page->firstRow.','.$Page->listRows)->order(array("id" => "desc"))->select();     
        if($lists){
            foreach ($lists as $key => $value) {
                $lists[$key] = array(
                    'id'=>$value['ID'],
                    'title' => $value['NewsName'.$this->lang],
                    'time' => $value['AddTime'] ,
                    'catid'=>$value['SortID'],
                    'thumb'=>fixPicDomain($value['thumb']),
                    'description'=>$value['SeoDescription'.$this->lang]
                );
            }
            $datas['count']     = $count;
            $datas['lists']     = $lists;
            $datas['page']      = $page;
            $datas['pagesize']  = $num;

            $this->apiSuccess('加载成功', $datas );
        }else{
            $this->apiError('没有数据');
        }
    }


    //新闻内容
    public function newsshow() {  

        $id = I('get.id',0,'intval');
        if(!$id){
            $this->apiError('文章不存在，或已删除');
        }
        $DB_News = D('Contents/News');        
        $data = $DB_News->where(array('ID'=>$id,'ViewFlag'.$this->lang=>1))->find();       
        if($data==false){
            $this->apiError('文章不存在，或已删除');
        }

        $catid = $data['SortID'];
        $catList = D('Contents/Newssort')->getCateList(1,$this->lang);
        $catInfo = $catList[$catid];

        $datas = array(
            'id' => $data['ID'],
            'is_jump' => $data['SourceEN'] == 1 || $data['SourceEN'] == '跳转' ? 1 : 0 ,
            'time' => $data['AddTime'],
            'catid' => $data['SortID'],
            'catName' => $catInof['SortName'].$this->lang,
            'title' => $data['NewsName'.$this->lang], 
            'content' => fixSrcDomain($data['Content'.$this->lang]),
            
        );
        if($datas['is_jump']){
            $datas['url'] = $data['SourceCH'];
        }


        $this->apiSuccess('加载成功', $datas );

    }


    //关于我们
    public function about() {
        $id = I('get.id',1,'intval');        
        $catid = $id;

        $DB_About = D('Contents/About');
       
        
        $data = $DB_About->where(array('ID'=>$id,'ViewFlag'.$this->lang=>1))->find();        
        if($data==false){
            $this->apiError('文章不存在，或已删除');
        }

        $datas = array(
            'id' => $data['ID'],
            'title' => $data['AboutName'.$this->lang], 
            'content' => $data['Content'.$this->lang],    
        );

        $this->apiSuccess('加载成功', $datas );

       
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
        $lists = M("Others")->where($map)->limit($Page->firstRow.','.$Page->listRows)->order(array("ID" => "ASC"))->select();  


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
        $lists = M("Others")->where($map)->limit($Page->firstRow.','.$Page->listRows)->order(array("ID" => "ASC"))->select();  

      
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
        $lists = M("Others")->where($map)->limit($Page->firstRow.','.$Page->listRows)->order(array("ID" => "DESC"))->select();  

    }





}