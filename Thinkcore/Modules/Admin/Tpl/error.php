<!DOCTYPE html>
<!--[if lt IE 9 ]><html class="ie ie8_lt"><![endif]-->
<!--[if IE 9 ]><html class="ie ie9"><![endif]-->
<!--[if (gt IE 9)|!(IE)]><html><![endif]-->

<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta name="renderer" content="webkit">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<title>跳转提示</title>

<link rel="stylesheet" href="__CSS_PATH__/Common/font-awesome.min.css" />
<!--[if lt IE 8]>
  <link rel="stylesheet" href="__CSS_PATH__/Common/font-awesome-ie7.min.css" />
<![endif]-->
<link rel="stylesheet" href="__CSS_PATH__/Common/bootstrap.min.css" >


<!--[if lt IE 9]>
  <script src="__JS_PATH__/Common/html5shiv.js"></script>
  <script src="__JS_PATH__/Common/rem.min.js"></script>
  <script src="__JS_PATH__/Common/respond.min.js"></script>
  <script src="__JS_PATH__/Common/excanvas.js"></script>
<![endif]-->


<style type="text/css">
*{ padding: 0; margin: 0; }
body{ background: #fff; font-family: '微软雅黑'; color: #333; font-size: 16px; }
.panel { margin:20% auto 0; float: none;}
.panel h2 { text-align: center;}
.panel h2 i {color:#900; font-size:60px}
</style>
</head>
<body>
  <div class="container">
    <div class="panel panel-default col-sm-8" >    
      <div class="panel-body">
        <div class="row">
          <div class="col-sm-3">
            <h2><i class="fa fa-times"  ></i></h2>
          </div>
          <div class="col-sm-9">
            <h3><?php echo($error); ?></h3>
            <p class="detail"></p>
            <p class="jump">
            页面自动 <a id="href" href="<?php echo($jumpUrl); ?>">跳转</a> 等待时间： <b id="wait"><?php echo($waitSecond); ?></b>
            </p>  
          </div>  
        </div>
      </div>
    </div>  
  </div>
  

<script type="text/javascript">
(function(){
var wait = document.getElementById('wait'),href = document.getElementById('href').href;
var interval = setInterval(function(){
  var time = --wait.innerHTML;
  if(time <= 0) {
    location.href = href;
    clearInterval(interval);
  };
}, 1000);
})();
</script>
</body>
</html>