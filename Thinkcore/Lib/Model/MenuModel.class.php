<?php

/**
 * 菜单
 * Some rights reserved：abc3210.com
 * Contact email:admin@abc3210.com
 */
class MenuModel extends CommonModel {

    //自动验证
    protected $_validate = array(
        //array(验证字段,验证规则,错误提示,验证条件,附加规则,验证时间)
        array('name', 'require', '菜单名称不能为空！', 1, 'regex', 3),
        array('app', 'require', 'g(分组项目)不能为空！', 1, 'regex', 3),
        array('model', 'require', 'm(模块名称)不能为空！', 1, 'regex', 3),
        array('action', 'require', 'a(方法名称)不能为空！', 1, 'regex', 3),
        //array('app,model,action', 'checkAction', '同样的记录已经存在！', 0, 'callback', 1),
        array('parentid', 'checkParentid', '菜单只支持四级！', 1, 'callback', 1),
    );
    //自动完成
    protected $_auto = array(
            //array(填充字段,填充内容,填充条件,附加规则)
    );

    //验证菜单是否超出三级
    public function checkParentid($parentid) {
        $find = $this->where(array("id" => $parentid))->getField("parentid");
        if ($find) {
            $find2 = $this->where(array("id" => $find))->getField("parentid");
            if ($find2) {
                $find3 = $this->where(array("id" => $find2))->getField("parentid");
                if ($find3) {
                    return false;
                }
            }
        }
        return true;
    }

    //验证action是否重复添加
    public function checkAction($data) {
        //检查是否重复添加
        $find = $this->where($data)->find();
        if ($find){
            return false;
        }
        return true;
    }


    public function getAllDatas(){
        if(F('Menu')){
            return F('Menu');
        }else{
            $result = $this->order(array("listorder" => "ASC"))->select();
            F('Menu',$result);
            return $result;
        }      
    }


     /**
     * 更新缓存
     * @param type $data
     * @return type
     */
    public function menu_cache($data = null) {
        if (empty($data)) {
            $data = $this->order(array("listorder" => "ASC"))->select();
            F("Menu", $data);
        } else {
            F("Menu", $data);
        }
        return $data;
    }

    /**
     * 按父ID查找菜单子项
     * @param integer $parentid   父菜单ID  
     * @param integer $with_self  是否包括他自己
     */
    public function admin_menu($parentid, $with_self = false) {
        //父节点ID
        $parentid = (int) $parentid;
        $result = $this->where(array('parentid' => $parentid, 'status' => 1))->order(array("listorder" => "ASC", 'id' => 'ASC'))->select();        
        if ($with_self) {
            $result2[] = $this->where(array('id' => $parentid))->find();
            $result = array_merge($result2, $result);
        }
        //权限检查
        if (session("roleid") == 1) {
            //如果角色为 1 直接通过
            return $result;
        }
        $array = array();
        //实例化权限表
        $privdb = M("Access");
        foreach ($result as $v) {
            //方法
            $action = $v['action'];
            //条件
            $where = array('g' => $v['app'], 'm' => $v['model'], 'a' => $action, 'role_id' => session("roleid"));
            if ($v['type'] == 0) {
                $where['m'] .= $v['id'];
                $where['a'] .= $v['id'];
            }
            //public开头的通过
            if (preg_match('/^public_/', $action)) {
                $array[] = $v;
            } else {
                if (preg_match('/^ajax_([a-z]+)_/', $action, $_match))
                    $action = $_match[1];
                $r = $privdb->where($where)->find();
                if ($r)
                    $array[] = $v;
            }
        }

        return $array;
    }

    /**
     * 获取菜单 头部菜单导航
     * @param $parentid 菜单id
     */
    public function submenu($parentid = '', $big_menu = false) {
        $array = $this->admin_menu($parentid, 1);
        $numbers = count($array);
        if ($numbers == 1 && !$big_menu) {
            return '';
        }
        return $array;
    }

    /**
     * 菜单树状结构集合
     */
    public function menu_json() {
/*         $Panel = M("AdminPanel")->where(array("userid" => AppframeAction::$Cache['uid']))->select();
       $items['0changyong'] = array(
            "id" => "",
            "name" => "常用菜单",
            "parent" => "changyong",
            "url" => U("Menu/public_changyong"),
        );
        foreach ($Panel as $r) {
            $items[$r['menuid'] . '0changyong'] = array(
                "icon" => "",
                "id" => $r['menuid'] . '0changyong',
                "name" => $r['name'],
                "parent" => "changyong",
                "url" => $r['url'],
            );
        }
       $changyong = array(
            "changyong" => array(
                "icon" => "",
                "id" => "changyong",
                "name" => "常用",
                "parent" => "",
                "url" => "",
                "items" => $items
            )
        );*/
        $data = $this->get_tree(0);
        return $data;
        //return array_merge($changyong, $data);
    }

    //取得树形结构的菜单
    public function get_tree($myid, $parent = "", $Level = 1) {
        $data = $this->admin_menu($myid);
        $Level++;
        if (is_array($data)) {
            foreach ($data as $a) {
                $id = $a['id'];
                $name = ucwords($a['app']);
                $model = ucwords($a['model']);
                $action = $a['action'];
                //附带参数
                $fu = "";
                if ($a['data']) {
                    $fu = "?" . $a['data'];
                }
                $array = array(
                    "icon" => $a['icon'],
                    "id" => $m."_".$id ,
                    "name" => $a['name'],
                    "parent" => $parent,
                    "url" => U("{$name}/{$model}/{$action}{$fu}", array("menuid" => $id)),
                );
                $ret[$m."_".$id] = $array;
                $child = $this->get_tree($a['id'], $id, $Level);
                //由于后台管理界面只支持三层，超出的不层级的不显示
                if ($child && $Level <= 3) {
                    $ret[$m."_".$id]['items'] = $child;
                }
            }
        }
        return isset($ret) ? $ret : false;
    }

   





    /**
     * 取得子ID
     * @param int parentid
     * @return array 
     */
    public function getChildrenIds($parentid){
        $return_ids = array();
        if(is_numeric($parentid)){
            $c_ids = $this->where(array('parentid'=>$parentid))->field('id')->select();
            if($c_ids){
                foreach ($c_ids as $key => $value) {
                    $cid = $value['id'];
                    array_push($return_ids,$cid);
                    $children_ids = $this->getChildrenIds($cid,$return_ids);
                    if(!empty($children_ids)){
                        $return_ids = array_merge($return_ids,$children_ids); 
                    }
                    
                }
            }
        }
        return $return_ids;
    }
    

    /**
     * 后台有更新/编辑则删除缓存
     * @param type $data
     */
    public function _before_write($data) {
        parent::_before_write($data);
        F("Menu", NULL);
    }

    //删除操作时删除缓存
    public function _after_delete($data, $options) {
        parent::_after_delete($data, $options);
        $this->_before_write($data);
    }

}