<?php

/**
 * 附件管理
 * Some rights reserved：abc3210.com
 * Contact email:admin@abc3210.com
 */
class ManageAction extends AdminbaseAction {

    //附件存在物理地址
    public $path = "";

    function _initialize() {
        parent::_initialize();
        //附件目录强制/d/file/ 后台设置的附件目录，只对网络地址有效
        $this->path = C("UPLOADFILEPATH");
    }

    /**
     * 附件管理 
     */
    public function index() {
        $db = M("Attachment");
        $where = array();
        $filter['filename'] = I('get.filename','','trim');
        empty($filter['filename']) ? "" : $where['filename'] = array('like', '%' . $filter['filename'] . '%');
        //时间范围搜索
        $filter['start_uploadtime'] = I('get.start_uploadtime');
        $filter['end_uploadtime']  = I('get.end_uploadtime');
        if (!empty($filter['start_uploadtime'])) {
            $where['_string'] = 'uploadtime >= ' . strtotime($filter['start_uploadtime']) . ' AND uploadtime <= ' . strtotime($filter['end_uploadtime']) . '';
        }
        $filter['fileext'] = I('get.fileext');
        empty($filter['fileext']) ? "" : $where['fileext'] = array('eq', $filter['fileext']);
        //附件使用状态
        $filter['status'] = I('get.status');
        $filter['status'] == "" ? "" : $where['status'] = array('eq', $filter['status']);

        $count = $db->where($where)->count();
        $page = $this->page($count, 20);
        $datas = $db->where($where)->limit($page->firstRow . ',' . $page->listRows)->order(array("uploadtime" => "DESC"))->select();

        foreach ($datas as $k => $v) {
            $datas[$k]['filesize'] = round($datas[$k]['filesize'] / 1024, 2);
            $datas[$k]['thumb'] = glob(dirname($this->path . $datas[$k]['filepath']) . '/thumb_*' . basename($datas[$k]['filepath']));
        }
        $this->assign("category", F("Category"));
        $this->assign("filter", $filter);
        
        $this->assign("datas", $datas);
        $this->assign("Page", $page->show('Admin'));
        $this->assign("show_header", true);
        $this->display();
    }

    

    /**
     * 删除附件 batch=true 单个删除 else 批量删除 
     */
    public function delete() {
        $Attachment = service("Attachment");
        $aid = I('request.aid');
        $batch = I('request.batch', 0, 'intval');
        if ($batch){
            if(is_array($aid)){
                $str = "";
                foreach ($aid as $k => $v) {
                    if ($Attachment->delFile((int) $v)) {
                        //删除附件关系
                        M("AttachmentIndex")->where(array("aid" => $v))->delete();
                    }
                    $str.=$v.",";  
                }
                $datas['ids'] = $aid;
                $this->success("刪除附件成功！(".$str.")","",$datas);
            }else{
                $this->error("請選擇要刪除之項");
            }
        }else{
            $aid = I('request.aid', 0, 'intval');
            if (empty($aid)) {
                $this->error("缺少參數！");
            }
            if ($Attachment->delFile((int) $aid)) {
                M("AttachmentIndex")->where(array("aid" => $aid))->delete();
                $this->success("刪除附件成功！");
            } else {
                $this->error("刪除附件失敗！");
            }
        }
    }

}

?>
