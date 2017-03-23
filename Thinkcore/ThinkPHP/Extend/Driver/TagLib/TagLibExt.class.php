<?php

/**
 */
class TagLibExt extends TagLib {

    // 数据库where表达式
    protected $comparisonStr = array(
        '{notlike}' => 'NOT LIKE',
        '{notin}' => 'NOT IN',
        '{like}' => 'LIKE',
        '{eq}' => '=',
        '{neq}' => '<>',
        '{elt}' => '<=',
        '{egt}' => '>=',
        '{gt}' => '>',
        '{lt}' => '<',
        '{in}' => 'IN',
    );

 /**
     * @var type 
     * 标签定义： 
     *                  attr         属性列表 
     *                  close      标签是否为闭合方式 （0闭合 1不闭合），默认为不闭合 
     *                  alias       标签别名 
     *                  level       标签的嵌套层次（只有不闭合的标签才有嵌套层次）
     * 定义了标签属性后，就需要定义每个标签的解析方法了，
     * 每个标签的解析方法在定义的时候需要添加“_”前缀，
     * 可以传入两个参数，属性字符串和内容字符串（针对非闭合标签）。
     * 必须通过return 返回标签的字符串解析输出，在标签解析类中可以调用模板类的实例。
     */
    protected $tags = array(
        //后台模板标签
        'admintemplate' => array("attr" => "file", "close" => 0),
        //模板标签
        'template' => array("attr" => "file", "close" => 0),
        //自定列表
        'mylist' => array('attr' => 'num,where,order,catid,name,id,offset,length,key,mod', 'level' => 3),
        
        //百度分享代码
        'baidushare' => array("attr" => "class,attr,str", "close" => 0),
        
    );

    /**
     * 模板包含标签 
     * 格式
     * <admintemplate file="APP/模块/模板"/>
     * @staticvar array $_admintemplateParseCache
     * @param type $attr 属性字符串
     * @param type $content 标签内容
     * @return array 
     */
    public function _admintemplate($attr, $content) {
        static $_admintemplateParseCache = array();
        $cacheIterateId = md5($attr . $content);
        if (isset($_admintemplateParseCache[$cacheIterateId])) {
            return $_admintemplateParseCache[$cacheIterateId];
        }
        //分析Admintemplate标签的标签定义
        $tag = $this->parseXmlAttr($attr, 'admintemplate');
        $file = explode("/", $tag['file']);
        $counts = count($file);
        if ($counts < 2) {
            return false;
        } else if ($counts < 3) {
            $file_path = DIRECTORY_SEPARATOR . "Admin" . DIRECTORY_SEPARATOR . "Tpl" . DIRECTORY_SEPARATOR . $tag['file'];
        } else {
            $file_path = DIRECTORY_SEPARATOR . $file[0] . DIRECTORY_SEPARATOR . "Tpl" . DIRECTORY_SEPARATOR . $file[1] . DIRECTORY_SEPARATOR . $file[2];
        }
        //模板路径
        $TemplatePath = APP_PATH . C("APP_GROUP_PATH") . $file_path . C("TMPL_TEMPLATE_SUFFIX");
        //判断模板是否存在
        if (!file_exists_case($TemplatePath)) {
            return false;
        }
        //读取内容
        $tmplContent = file_get_contents($TemplatePath);
        //解析模板内容
        $parseStr = $this->tpl->parse($tmplContent);
        $_admintemplateParseCache[$cacheIterateId] = $parseStr;
        return $_admintemplateParseCache[$cacheIterateId];
    }


    
    /**
     * 标签：<template/>
     * 作用：引入其他模板
     * 用法示例：<template file="Member/footer.php"/>
     * 参数说明：
     *          @file	表示需要应用的模板路径。(这里需要说明的是，只能引入当前主题下的模板文件)
     * 
     * @staticvar array $_templateParseCache
     * @param type $attr 属性字符串
     * @param type $content 标签内容
     * @return array 
     */
    public function _template($attr, $content) {    	
        static $_templateParseCache = array();
        $cacheIterateId = md5($attr . $content);
        if (isset($_templateParseCache[$cacheIterateId])) {
            return $_templateParseCache[$cacheIterateId];
        }
        //检查CONFIG_THEME是否被定义
        if (!defined("CONFIG_THEME")) {
            define('CONFIG_THEME', C('DEFAULT_THEME'));
            //return;
        }
        //分析template标签的标签定义
        $tag = $this->parseXmlAttr($attr, 'template');

        $TemplatePath = TEMPLATE_PATH . CONFIG_THEME . DIRECTORY_SEPARATOR . $tag['file'];        

        //判断模板是否存在
        if (!file_exists_case($TemplatePath)) {
            //启用默认模板
            $TemplatePath = TEMPLATE_PATH . "Default" . DIRECTORY_SEPARATOR . $tag['file'];
            if (!file_exists_case($TemplatePath)) {
                return;
            }
        }
        //读取内容
        $tmplContent = file_get_contents($TemplatePath);
        //解析模板
        $parseStr = $this->tpl->parse($tmplContent);
        $_templateParseCache[$cacheIterateId] = $parseStr;
        return $_templateParseCache[$cacheIterateId];
    }



    public function _mylist($attr,$content){
        static $content_iterateParseCache = array();
        //如果已经解析过，则直接返回变量值
        $cacheIterateId = md5($attr . $content);
        if (isset($content_iterateParseCache[$cacheIterateId])) {
            return $content_iterateParseCache[$cacheIterateId];
        }
        //分析content标签的标签定义
        $tag = $this->parseXmlAttr($attr, 'content');

        $tag['catid'] = $catid = intval($tag['catid']);
        //每页显示总数
        $tag['num'] = $num = (int) $tag['num'];

        $tag['model'] = $model = $tag['model'];
       
        //数据返回变量
        $tag['return'] = $return = empty($tag['return']) ? "data" : $tag['return'];
        //sql语句的where部分
        if ($tag['where']) {
            $tag['where'] = $this->parseSqlCondition($tag['where']);
        }
        if($catid){
            $whereCatid = "SortID = ".$catid."";
            $tag['where'] = $tag['where'] ? $whereCatid." AND ".$tag['where'] : $whereCatid ;
        }
        $tag['where'] = $where = $tag['where'];
        $tag['order'] = $order = $tag['order'];
        $tag['return'] = $return = $tag['return'];
        $id    =    $tag['id'];
        $empty =    isset($tag['empty'])?$tag['empty']:'';
        $key   =    !empty($tag['key'])?$tag['key']:'i';
        $mod   =    isset($tag['mod'])?$tag['mod']:'2';
 
        
        //方法
        $tag['action'] = $action = trim($tag['action']);

        //拼接php代码
        $parseStr = '<?php ';
        $parseStr .= ' $__MYLIST__ = M("'.$model.'")->where("'.$where.'")->limit('.$num.')->order("'.$order.'")->select();' . "\r\n";
        
        //$parseStr .= ' var_dump(M("'.$model.'")->getLastSql());';
        $parseStr .= 'if( count($__MYLIST__)==0 ){ echo "'.$empty.'" ;';
        $parseStr .= '}else{ ';
        $parseStr .= 'foreach($__MYLIST__ as $'.$key.'=>$'.$id.'){ ';
        $parseStr .= '$mod = ($'.$key.' % '.$mod.' );';
        $parseStr .= '++$'.$key.';?>';
        $parseStr .= $this->tpl->parse($content);
        $parseStr .= '<?php } }?>';
        //解析模板
        $content_iterateParseCache[$cacheIterateId] = $parseStr;

        if(!empty($parseStr)) {
            return $parseStr;
        }
        return ;

    }

    /**
     * 百度分享
     * @param type $attr 属性字符串
     * @param type $content 标签内容
     */
    public function _baidushare($attr, $content) {
        static $_templateParseCache = array();
        $cacheIterateId = md5($attr . $content);
        if (isset($_templateParseCache[$cacheIterateId])) {
            return $_templateParseCache[$cacheIterateId];
        }
        
        //分析template标签的标签定义
        $tag = $this->parseXmlAttr($attr, 'baidushare');
        $tag['class'] = $class = $tag['class'];
        $tag['attr'] = $attr = $tag['attr'];
        $tag['str'] = $str = $tag['str'] ? $tag['str'] : '<i class="fa fa-share-alt"></i> 分享到 &nbsp;&nbsp;';
        
        $parseStr = '';
        $parseStr .= ' <div class="bdsharebuttonbox '.$class.'" '.$attr.'>';
        $parseStr .= '    <span class="pull-left">'.$str.'</span> ';
        $parseStr .= '    <a class="bds_more" data-cmd="more"> </a>';
        $parseStr .= '    <a class="bds_mshare" data-cmd="mshare"></a>';
        $parseStr .= '    <a class="bds_qzone" data-cmd="qzone" href="#"></a>';
        $parseStr .= '    <a class="bds_tsina" data-cmd="tsina"></a>';
        $parseStr .= '    <a class="bds_baidu" data-cmd="baidu"></a>';
        $parseStr .= '    <a class="bds_renren" data-cmd="renren"></a>';
        $parseStr .= '    <a class="bds_tqq" data-cmd="tqq"></a>';
        $parseStr .= '    <a class="bds_count" data-cmd="count"></a>';
        $parseStr .= '  </div>';
        
        //解析模板        
        $_templateParseCache[$cacheIterateId] = $parseStr;
        return $_templateParseCache[$cacheIterateId];
    }


    /**
         * 转换数据为HTML代码
         * @param array $data 数组
         */
        private static function arr_to_html($data) {
            if (is_array($data)) {
                $str = 'array(';
                foreach ($data as $key => $val) {
                    if (is_array($val)) {
                        $str .= "'$key'=>" . self::arr_to_html($val) . ",";
                    } else {
                        if (strpos($val, '$') === 0) {
                            $str .= "'$key'=>$val,";
                        } else {
                            $str .= "'$key'=>'" . new_addslashes($val) . "',";
                        }
                    }
                }
                return $str . ')';
            }
            return false;
        }

        /**
         * 检查是否变量
         * @param type $variable
         * @return type
         */
        protected function variable($variable) {
            return substr(trim($variable), 0, 1) == '$';
        }

        /**
         * 解析条件表达式
         * @access public
         * @param string $condition 表达式标签内容
         * @return array
         */
        protected function parseSqlCondition($condition) {
            $condition = str_ireplace(array_keys($this->comparisonStr), array_values($this->comparisonStr), $condition);
            return $condition;
        }

}
