

var JS_wtool = new Class_Wintool() ;
JS_wtool.winTool("fixTool")

$("#fixTool").find("a").focus(function(){
	$(this).blur()
})

if(typeof(wtool_auto)!="undefined"){
	$("#fixTool").find(".li_"+wtool_auto).children("a").trigger("click");
}

function Class_Wintool(){
	var thisClass = this;
	this.thisWidth = 35 ;
	this.id = "";
	
	this.winTool = function(id){
		var wintoolHtml = ''
			wintoolHtml += '<div class="fixTool" id="'+id+'">'
			wintoolHtml += '<div class="tbg"></div>'
			wintoolHtml += '<div class="con">'
			wintoolHtml += '    <ul>'
			wintoolHtml += '        <li><a  class="toolbtn" href="javascript:goTop()"><i class="fa fa-arrow-up"></i><b>回顶部</b></a></li>'
			wintoolHtml += '        <li class="li_share"><a  class=" toolbtn wtbtn" href="javascript:void(0);"  data-btntype="share"><i class="fa fa-share"></i><b>分 享</b></a><div class="wtShowbox sb_share"><div class="wtShowbox_inner"><div class="bdsharebuttonbox fixtool_sharebox"><a href="#" class="bds_more" data-cmd="more">分享到 </a><a title="分享到QQ空间" href="#" class="bds_qzone" data-cmd="qzone"></a><a title="分享到新浪微博" href="#" class="bds_tsina" data-cmd="tsina"></a><a title="分享到腾讯微博" href="#" class="bds_tqq" data-cmd="tqq"></a><a title="分享到人人网" href="#" class="bds_renren" data-cmd="renren"></a><a title="分享到微信" href="#" class="bds_weixin" data-cmd="weixin"></a><a title="分享到百度贴吧" href="#" class="bds_tieba" data-cmd="tieba"></a></div></div></div></li>'
			//wintoolHtml += '        <li class="li_zixun"><a class="toolbtn "  href="javascript:zinxun_btn_action()"><i class="fa fa-comment-o"></i><b>咨 询</b></a></li>'			
			//wintoolHtml += '        <li class="li_qrcode"><a class="toolbtn wtbtn" href="javascript:void(0)" data-btntype="qrcode"><i class="fa fa-qrcode"></i><b>二维码</b></a></li>'

			wintoolHtml += '        <li class="li_wechat"><a class="toolbtn wtbtn" href="javascript:void(0)" data-btntype="wechat"><i class="fa fa-wechat"></i><b>微信</b></a></li>'
			wintoolHtml += '        <li class="li_weibo"><a class="toolbtn wtbtn" href="javascript:void(0)" data-btntype="weibo"><i class="fa fa-weibo"></i><b>微博</b></a></li>'
			wintoolHtml += '        <li class="li_notice"><a class="toolbtn wtbtn" href="javascript:void(0)" data-btntype="notice"><i class="fa fa-comment"></i><b>通知</b></a></li>'
			//wintoolHtml += '        <li class="li_recommend"><a class="toolbtn wtbtn" href="javascript:void(0)" data-btntype="recommend"><i class="fa fa-heart"></i><b>推荐</b></a></li>'
			wintoolHtml += '    </ul>'
			wintoolHtml += '</div>'
			wintoolHtml += '</div>' ;
		$("body").append(wintoolHtml)	
			
		var $obj = $("#"+id);
		$obj.find(".tbg").height($obj.children(".con").height()+1)
		thisClass.id = id ;
		$obj.find(".wtbtn").click(function(event){
			event.stopPropagation();
			var $btn = $(this);
			var $li = $btn.parent("li");
			
			$(this).blur().parent("li").addClass("current").siblings("li").removeClass("current")
			var type = $(this).attr("data-btntype")
			$li.siblings("li").find(".wtShowbox").hide()
			var $wtshowBox = $btn.parent("li").find(".wtShowbox");
				
			if($wtshowBox.is(":hidden")||$wtshowBox.length==0){
				if($wtshowBox.length==0){
					var boxHtml="<div class=\"wtShowbox sb_"+type+"\"><div class=\"wtShowbox_inner\"></div></div>";
					$btn.parent("li").append(boxHtml);
				}
				$wtshowBox = $btn.parent("li").find(".wtShowbox");
				
				if($.trim($wtshowBox.find(".wtShowbox_inner").html())==""){
					switch(type){
						case "qrcode" :
							var pageName = thisClass.getPageName().toLowerCase()
							var id = thisClass.getRequest("ID")
								id = id==""? thisClass.getRequest("id"):id;
								id = id==""? thisClass.getRequest("SortID"):id;
								id = id==""? thisClass.getRequest("sortid"):id;
							var page = thisClass.getRequest("page")
							var qrStr = window.location.href;
							var model = $("#prModel").length>0?$.trim($("#prModel").val()):"";
								model = model!=""?model:thisClass.getRequest("filter")
								model = model!=""?model:thisClass.getRequest("keyword")
							var opt = {
									url :"/qrcode/winto_qrcode_json.php"
									,type:"post"
									,dataType:"html"
									,btn:$btn
									,css:{ width:200, height:194}
									,data:{"pageName":pageName,"id":id,"page":page,"qrStr":qrStr,"model":model,"dataType":"html"}
								}
								thisClass.btnAction(opt)
						break;
						case "share" :
									var opt = {
									btn:$btn
									,data:{"act":type}
									,css:{"bottom":1,"height":196,width:100}
									,ajax:0
									,html:'<div class="bdsharebuttonbox"><a href="#" class="bds_more" data-cmd="more"></a><a title="分享到QQ空间" href="#" class="bds_qzone" data-cmd="qzone"></a><a title="分享到新浪微博" href="#" class="bds_tsina" data-cmd="tsina"></a><a title="分享到腾讯微博" href="#" class="bds_tqq" data-cmd="tqq"></a><a title="分享到人人网" href="#" class="bds_renren" data-cmd="renren"></a><a title="分享到微信" href="#" class="bds_weixin" data-cmd="weixin"></a></div>'
								}	
								thisClass.btnAction(opt)
								
						break;	
						case "wechat" :
							var opt = {
									btn:$btn
									,data:{"act":type}
									,css:{"bottom":-99,"height":242}
									,ajax:0
									,html:'<div class=\"qr_pic\"><img src=\"/m/images/wintosqr_wechat.png\"></div><div class=\"qr_txt\"><b>微信公众帐号：#lazyme#陶瓷</b><br />欢迎扫描二维码并关注我们</div>'
								}	
								thisClass.btnAction(opt)
						break;
						case "weibo" :
							var opt = {
									btn:$btn
									,data:{"act":type}
									,css:{"bottom":-51,"height":242}
									,ajax:0
									,html:'<div class=\"qr_pic\"><img src=\"/m/images/wintosqr_sina.png\"></div><div class=\"qr_txt\">欢迎关注<b>#lazyme#陶瓷官方微博</b><br />点击<a href=\"http://weibo.com/winto100\">【这里】</a>查看我们的微博主页</div>'
								}	
								thisClass.btnAction(opt)
						break;
						case "notice" :
							var opt = {
									btn:$btn
									,data:{"act":type}
									,css:{"height":300,"width":460}
								}	
								thisClass.btnAction(opt)
						break;
						case "recommend" :
							var opt = {
									btn:$btn
									,data:{"act":type}
									,css:{"height":260,"width":420}
								}	
								thisClass.btnAction(opt)
						break;
					}
				}
				$wtshowBox.hide();
				$wtshowBox.show(300);	
				
			}else{		
				$obj.find(".wtbtn").parent("li").removeClass("current")
				$obj.find(".wtShowbox").hide()
			}
		})
		$obj.find("li").click(function(event){
				event.stopPropagation();
		})		
		$(document).click(function(){
			$obj.find(".wtbtn").parent("li").removeClass("current")
			$obj.find(".wtShowbox").hide()
			
		})
		
	}
	this.btnAction = function(options){
		defaults={
			ajax : 1 
			,width:200
			,target:options.btn.parent("li").find(".wtShowbox").find(".wtShowbox_inner")
			,url:"/index.php?m=Public&c=Statics&a=fixtool"
			,type:"get"
			,dataType:"html"
		}
		var options = $.extend(defaults,options)
		if((typeof(options.btn)!="undefined" )){
			var $wtshowBox = options.btn.parent("li").find(".wtShowbox");
			if((typeof(options.css)!="undefined" )){
				$wtshowBox.css(options.css);
			}
		}

		if(options.ajax==1){
			$.ajax({
				type:options.type
				,dataType:options.dataType
				,data:options.data
				,url:options.url
				,beforeSend:function(){
					options.target.html("<div class=\"wtooload\" style=\"margin-top:50px; text-align:center\"><img src=\"/statics/images/Common/loading_c.gif\"></div>")
				}
				,success:function(data){
					options.target.html(data)
				}
			}).always(function(){
				options.target.find(".wtooload").remove()
			})
		}else{
			options.target.html(options.html)
		}

	}

	this.getPageName = function(){
		var strUrl	 =	window.location.href;
			strUrl 	 =  strUrl.indexOf("?")!=-1 ?strUrl.split("?")[0] :strUrl
		var arrUrl 	 =	strUrl.indexOf("/")!=-1 ?strUrl.split("/"):"";
		var file	 =	arrUrl == "" ? "" :arrUrl[arrUrl.length-1];
		var fileName =	file =="" ? "" : file.split(".")[0]
		return fileName ;
	}
	this.getRequest = function (cx){
		var cxo =""
		var url=window.location.search;
		if(url.indexOf("?")!=-1){   
		  var str   =   url.substr(1)   
		  strs = str.split("&");   
		  for(i=0;i<strs.length;i++){   
			if([strs[i].split("=")[0]]==cx) cxo=unescape(strs[i].split("=")[1]);
		  }   
		}	
		return cxo;
	}
	this.ntPage = function(page){
		var opt = {
				btn:$(".li_notice").find(".wtbtn")
				,target:$(".li_notice").find(".wtShowbox")
				,data:{"act":"notice","page":page}
			}	
		thisClass.btnAction(opt)
	}
	this.ntID = function(id){
		var opt = {
				btn:$(".li_notice").find(".wtbtn")
				,target:$(".li_notice").find(".wtShowbox")
				,data:{"act":"notice","id":id}
			}	
		thisClass.btnAction(opt)
	}
	
}
