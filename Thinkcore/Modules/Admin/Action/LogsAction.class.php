<?php

// +----------------------------------------------------------------------
// | 後臺日誌
// +----------------------------------------------------------------------
// | Copyright (c) 2015 http://www.5mell.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: Loch Kan <454831746@qq.com>
// +----------------------------------------------------------------------



class LogsAction extends AdminbaseAction {


    //操作日志查看
    public function index($loginAction=0) {
        if (IS_POST) {
            $this->redirect('index', $_POST);
        }
        $uid = I('uid');
        $start_time = I('start_time');
        $end_time = I('end_time');
        $ip = I('ip');
        $status = I('status');
        $m = I('field_m');
        $g = I('field_g');
        $a = $loginAction ? $loginAction : I('field_a');
        $where = array();
        if (!empty($uid)) {
            $where['uid'] = array('eq', $uid);
        }
        if (!empty($start_time) && !empty($end_time)) {
            $start_time = strtotime($start_time);
            $end_time = strtotime($end_time) + 86399;
            $where['time'] = array(array('GT', $start_time), array('LT', $end_time), 'AND');
        }
        if (!empty($ip)) {
            $where['ip '] = array('like', "%{$ip}%");
        }
        if ($status != '') {
            $where['status'] = (int) $status;
        }
        if ($m != '') {
            $where['m'] = $m;
        }
        if ($c != '') {
            $where['g'] = $g;
        }
        if (!empty($a)) {
            $where['a'] = $a;
        }
        $count = M("Log")->where($where)->count();
        $Page = $this->page($count, 20);
        
        $data = M("Log")->where($where)->limit($Page->firstRow.','.$Page->listRows)->order(array("logid" => "desc"))->select();
        $this->assign("Page", $Page->show());
        $this->assign("data", $data);
        $this->display('index');
    }



    //删除操作日志
    public function deletelog() {
        $days = I('get.days',0,"int");
        if($days>0){
            if($days<30){
                $this->error("日誌至少保留一個月");
            }
            if (D("Log")->deleteDaysago($days)) {
                $this->success("删除"+$days+"天前的後臺日誌成功！");
            } else {
                $this->error("删除"+$days+"天前的後臺日誌失敗！");
            }    
        }
        
    }

    //删除兩個月前的操作日志
    public function delete2monthlog() {
        if (D("Log")->deleteDaysago()) {
            $this->success("删除兩個月前的後臺日誌成功！");
        } else {
            $this->error("删除兩個月前的後臺日誌失敗！");
        }        
    }

    //后台登陆日志
    public function loginlog() {

        $this->index('tologin');
    }

}
