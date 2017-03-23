<?php

/**
 * 前台Action
 * Some rights reserved：abc3210.com
 * Contact email:admin@abc3210.com
 */
class ApibaseAction extends AppframeAction {

    public $TemplatePath, $Theme, $ThemeDefault;

    protected function _initialize() {
        //定义是前台
        define('IN_ADMIN', false);
        parent::_initialize();
        //前台关闭表单令牌
        C("TOKEN_ON", false);
    
    }

  

    /**
     *  获取输出页面内容
     * 调用内置的模板引擎fetch方法，
     * @access protected
     * @param string $templateFile 指定要调用的模板文件
     * 默认为空 由系统自动定位模板文件
     * @param string $content 模板输出内容
     * @param string $prefix 模板缓存前缀* 
     * @return string
     */
    protected function fetch($templateFile = '', $content = '', $prefix = '') {
        return parent::fetch($this->parseTemplateFile($templateFile), $content, $prefix);
    }


    /**
     * 操作错误跳转的快捷方法
     * @access protected
     * @param string $message 错误信息
     * @param string $jumpUrl 页面跳转地址
     * @param mixed $ajax 是否为Ajax方式 当数字时指定跳转时间
     * @return void
     */
    final public function apiError($msg='',$datas = array()) {
        parent::ajaxReturn($datas, $msg, 0);
    }

    /**
     * 操作成功跳转的快捷方法
     * @access protected
     * @param string $message 提示信息
     * @param string $jumpUrl 页面跳转地址
     * @param mixed $ajax 是否为Ajax方式 当数字时指定跳转时间
     * @return void
     */
    final public function apiSuccess($msg='',$datas = array()) {
       
        parent::ajaxReturn($datas, $msg, 1);
    }

    /**
     * 分页输出
     * @staticvar array $_pageCache
     * @param type $Total_Size 信息总数
     * @param type $Page_Size 每页显示信息数量
     * @param type $Current_Page 当前分页号
     * @param type $List_Page 每次显示几个分页导航链接
     * @param type $PageParam 接收分页号参数的标识符
     * @param type $PageLink 分页规则 
     *                          array(
     *                                  "index"=>"http://www.abc3210.com/192.html",//这种是表示当前是首页，无需加分页1
     *                                  "list"=>"http://www.abc3210.com/192-{$page}.html",//这种表示分页非首页时启用
     *                          )
     * @param type $static 是否开启静态
     * @param string $TP 模板
     * @param array $Tp_Config 模板配置
     * @return array|\Page
     */
    protected function page($Total_Size = 1, $Page_Size = 0, $Current_Page = 0, $List_Page = 6, $PageParam = '', $PageLink = '', $static = FALSE, $TP = "", $Tp_Config = "") {
        $Tp_Config = $Tp_Config ? $Tp_Config : array("listlong" => "6", "first" => '<i class="fa fa-step-backward" title="第一页"></i>', "last" => '<i class="fa fa-step-forward" title="末页"></i>', "prev" => '<i class="fa fa-caret-left" title="上一页"></i>', "next" => '<i class="fa fa-caret-right" title="下一页"></i>', "list" => "*", "disabledclass" => "disabled");
        return page($Total_Size, $Page_Size, $Current_Page, $List_Page, $PageParam, $PageLink, $static, $TP, $Tp_Config);
    }

    

}