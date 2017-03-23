<?php

/**
 * Menu(菜单管理)
 * Some rights reserved：abc3210.com
 * Contact email:admin@abc3210.com
 */
class MenuAction extends AdminbaseAction {

    protected $Menu;

    function _initialize() {
        parent::_initialize();
        $this->Menu = D("Menu");
    }

    /**
     *  显示菜单
     */
    public function index() {
        $result = $this->Menu->getAllDatas();
        import("Tree");
        $tree = new Tree();
        $tree->icon = array('&nbsp;&nbsp;&nbsp;│ ', '&nbsp;&nbsp;&nbsp;├─ ', '&nbsp;&nbsp;&nbsp;└─ ');
        $tree->nbsp = '&nbsp;&nbsp;&nbsp;';
        foreach ($result as $r) {

            $r['str_manage'] = '<a data-width="600" data-trigger="modal"   data-title="在【'.$r['name'].'】下添加子菜單：" href="' . U("Menu/add", array("parentid" => $r['id'])) . '"><i class="fa fa-plus"></i> 添子菜单</a>
             | <a data-width="600" data-trigger="modal"   data-title="修改菜單：'.$r['name'].'" href="' . U("Menu/edit", array("id" => $r['id'], "menuid" => $_GET['menuid'])) . '"><i class="fa fa-pencil"></i> 修改</a>
              | <a onclick="deleteItem(this,{url:\''.U("Menu/delete", array("id" => $r['id'], "menuid" => $this->_get("menuid"))).'\'})" href="javascript:void(0);" data-loading-text="刪.."><i class="fa fa-trash"></i> 删除</a> ';              
            $r['status'] = $r['status'] ? "显示" : "不显示";
            $array[] = $r;
        }

        $tree->init($array);
        $str = "<tr>        
	<td ><input name='listorders[\$id]' type='text' size='3' value='\$listorder' class='form-control input-sm field_listorders' style='height:18px;padding:0px 3px;'></td>
	<td >\$id</td>
	<td >\$spacer\$name <i class='fa fa-\$icon' style='color:#666'></i></td>
    <td >\$status</td>
	<td class='options'>\$str_manage</td>
	</tr>";
        $categorys = $tree->get_tree(0, $str);
        $this->assign("categorys", $categorys);
        $this->display();
    }

    /**
     *  添加
     */
    public function add() {
        if (IS_POST) {
            $name = I('post.name');

            if ($this->Menu->create()) {
                if ($this->Menu->add()) {
                    $this->success("菜單【{$name}】新增成功！", U("Menu/index"));
                } else {
                    $eMsg = "菜單【{$name}】新增失败！";
                    $this->addLogs($eMsg,0);
                    $this->error($eMsg);
                }
            } else {
                $this->error($this->Menu->getError());
            }
        } else {
            import("Tree");
            $tree = new Tree();
            $parentid = (int) $this->_get("parentid");
            $result = $this->Menu->getAllDatas();
            foreach ($result as $r) {
                $r['selected'] = $r['id'] == $parentid ? 'selected' : '';
                $array[] = $r;
            }
            $str = "<option value='\$id' \$selected>\$spacer \$name</option>";
            $tree->init($array);
            $select_categorys = $tree->get_tree(0, $str);
            $this->assign("select_categorys", $select_categorys);
            $this->display();
        }
    }

    /**
     *  删除
     */
    public function delete() {
        $id = (int) $this->_get("id");
        $count = $this->Menu->where(array("parentid" => $id))->count();
        if ($count > 0) {
            $this->error("该菜单下还有子菜单，无法删除！");
        }
        if ($this->Menu->delete($id)) {
            $this->success("删除菜单【ID={$id}】成功！");
        } else {
            $eMsg = "菜單【ID={$id}】删除失败！";
            $this->addLogs($eMsg,0);
            $this->error($eMsg);
        }
    }

    /**
     *  编辑
     */
    public function edit() {
        if (IS_POST) {
            $id = I('post.id',0,'intval');
            $parentid = I('post.parentid',0,'intval');
            if(!$id){
                $this->error('Lost ID');
            }
            if(in_array($parentid, $this->Menu->getChildrenIds($id))||$parentid==$id){
                $this->error('不能移动到自己的子菜单');
            }
            $data = $this->Menu->create();
            if ($data) {
                if ($this->Menu->save() !== false) {
                   $this->success("菜單更新成功！(ID=".$data['id'].")", U("Menu/index"));
                } else {
                   $eMsg = "菜單更新失败！(ID=".$data['id'].")";
                   $this->addLogs($eMsg,0);
                   
                }
            } else {
                $this->error($this->Menu->getError());
            }
        } else {
            import("Tree");
            $tree = new Tree();
            $id = (int) $this->_get("id");
            $rs = $this->Menu->where(array("id" => $id))->find();
            $result = $this->Menu->getAllDatas();
            foreach ($result as $r) {
                $r['selected'] = $r['id'] == $rs['parentid'] ? 'selected' : '';
                $array[] = $r;
            }
            $str = "<option value='\$id' \$selected>\$spacer \$name</option>";
            $tree->init($array);
            $select_categorys = $tree->get_tree(0, $str);
            $this->assign("data", $rs);
            $this->assign("select_categorys", $select_categorys);
            $this->display();
        }
    }

    //排序
    public function listorders() {
        $status = parent::listorders($this->Menu);
        if ($status) {
            $this->success("排序更新成功！");
        } else {
            $this->error("排序更新失败！");
        }
    }

   

}