<?php

/**
 * 任务单模型
  * Author: Loch Kan （紫蘇） <454831746@qq.com>
 */
class NoticeModel extends CommonModel {

    //array(验证字段,验证规则,错误提示,[验证条件,附加规则,验证时间])
    protected $_validate = array(
        array('title', 'require', '内容不能为空'),
        array('content', 'require', '不能为空'),
        
    );
    //array(填充字段,填充内容,[填充条件,附加规则])
    protected $_auto = array(
        
        array('create_time', 'time', 1, 'function'),
        array('update_time', 'time', 3, 'function'),
        array('userid','getUserLoginID',1,'function'),
        //array('listorder', '0'),
    );

}