<?php
/**
 * 验证码处理
 * Some rights reserved：abc3210.com
 * Contact email:admin@abc3210.com
 */
class CheckcodeAction extends Action {

    public function index() {
        import("Image");
        //import('ORG.Util.Image');
        
        $code_len = I('get.code_len',4,'intval');
        if ($code_len > 8 || $code_len < 2) {
            $code_len = 4;
        }
        
        $width = I('get.width',100,'intval');
        $size = I('get.font_size',0,'intval');
        // if (isset($_GET['font_size']) && intval($_GET['font_size']))
        //     $checkcode->font_size = intval($_GET['font_size']);
        $height = I('get.height',34,'intval');
        $mode = I('get.mode',0,'intval');
       if ($mode > 7 || $mode < 0) {
            $mode = 5;
        }
        switch ($mode) {
            case 6:
                Image::GBVerify($code_len  , 'png', $width, $height);
                break;
            case 7:
                Image::showAdvVerify('png', $width , $height,  'verify' );
                break;
            default:
                Image::buildImageVerify($code_len,$mode,'png',$width,$height,'verify',$size);
                break;
        }
      

    }

}