<?php

/**
 * 网站配置信息管理
 * Some rights reserved：abc3210.com
 * Contact email:admin@abc3210.com
 */
class ConfigAction extends AdminbaseAction {


    /**
     * 网站基本设置
     */
    public function index() {        
        $this->group();

    }

    /**
     *  邮箱参数
     */
    public function mail() {
        $this->group();
    }

    /**
     *  附件参数
     */
    public function attach() {
        $this->group();
    }

 

    public function group(){
        if (IS_POST) {
            $config = I('post.config');
            
            if($config && is_array($config)){
                foreach ($config as $name => $value) {
                    //echo $name."=>".$value."<br>";
                    $map = array('name' => $name);
                    D('Config')->where($map)->setField('value', $value);
                }
            }
            F('Config',null);
            $this->success('設置保存成功！');
        } else {
            
            $configList = D('Config')->Where(array('group' => ACTION_NAME, ))->select();
            $this->assign('list', $configList)->display("index");
        }
    }

}