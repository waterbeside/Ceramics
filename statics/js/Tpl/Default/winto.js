var gbl_bodyHeight=0;
var gbl_bodyWidth=0;
$.browser = $.support;
$.browser.mozilla = /firefox/.test(navigator.userAgent.toLowerCase());
$.browser.webkit = /webkit/.test(navigator.userAgent.toLowerCase());
$.browser.opera = /opera/.test(navigator.userAgent.toLowerCase());
$.browser.msie = /msie/.test(navigator.userAgent.toLowerCase());
//取得ie浏览器版本号
function getBrowserVersion(){
	var agent = navigator.userAgent.toLowerCase() ;
	var regStr_ie = /msie [\d.]+;/gi ;
	if(agent.indexOf("msie") > 0){
		return  parseInt((agent.match(regStr_ie)+"").split("msie")[1].split(";")[0])		
	}else{
		return "" ; 	
	}
	
}

function getBodySize(){
	gbl_bodyHeight=document.documentElement.clientHeight;
 	gbl_bodyWidth=document.documentElement.clientWidth;	
 	return {'height':gbl_bodyHeight,'width':gbl_bodyWidth} ;
}

 //获取字符长度
function getByteLen(val) {
	var len = 0;
	return val.replace(/[^\x00-\xff]/g,'**').length;
} 



//验证电话号码
function checkTel(inputID){
	var cellphone=/^([\d-+]*)$/;	
	var tel = $("#"+inputID).val();
	if(!cellphone.test(tel)||tel.length<7||tel.length>18){
		return {"status":0,"info":"请输入正确的电话号码"}
	}else{
		return {"status":1,"info":""}
	}
}



//len>0时，验证input是否不小于len这个个数。当len=0时，验证input不能为空。
function checkLen(inputID,len,msg){
	if(len>0){
		var inputLen = getByteLen($("#"+inputID).val())
		if(inputLen < len){
			msg = msg?msg:"不得少于"+len+"位"
			return {"status":0,"info":msg}	
		}else{
			return {"status":1,"info":""}	
		}
	}else{
		var inputVal = $.trim($("#"+inputID).val())
		if(inputVal ==""){
			msg = msg?msg:"不能为空"
			return {"status":0,"info":msg}
		}else{
			return {"status":1,"info":""}	
		}
	}
}

//验证两次密码是否一致
function checkPw(pwInput,checkPwInput){
	var $chkPWD = $("#"+checkPwInput)
	var ch_pwd = $chkPWD.val()
	var pwd = $("#"+pwInput).val()
	if($.trim(pwd)==""){
		return {"status":0,"info":"密码不能为空"}
	}else if(ch_pwd != pwd ){
		return {"status":0,"info":"两次密码不一致"}
	}else{
		return {"status":1,"info":""}
	}
}

//检测是否移动端
function checkMobile(){
	var isiPad = navigator.userAgent.match(/iPad/i) != null;
	if(isiPad){
		return false;
	}
	var isMobile=navigator.userAgent.match(/iphone|android|phone|mobile|wap|netfront|x11|java|operamobi|operamini|ucweb|windowsce|symbian|symbianos|series|webos|sony|blackberry|dopod|nokia|samsung|palmsource|xda|pieplus|meizu|midp|cldc|motorola|foma|docomo|up.browser|up.link|blazer|helio|hosin|huawei|novarra|coolpad|webos|techfaith|palmsource|alcatel|amoi|ktouch|nexian|ericsson|philips|sagem|wellcom|bunjalloo|maui|smartphone|iemobile|spice|bird|zte-|longcos|pantech|gionee|portalmmm|jig browser|hiptop|benq|haier|^lct|320x320|240x320|176x220/i)!= null;
	if(isMobile){
		return true;
	}
	return false;
}

// getRequest
function getRequest(cx){
	var cxo =""
	var url=window.location.search;
	if(url.indexOf("?")!=-1)   
	{   
	  var str   =   url.substr(1)   
	  strs = str.split("&");   
	  for(i=0;i<strs.length;i++)   
	  {   
		if([strs[i].split("=")[0]]==cx) cxo=unescape(strs[i].split("=")[1]);
	  }   
	}	
	return cxo;
}


//加入收藏
function addMe() {
	var url = document.location.href; 
	var wTitle = document.title; 
	 try {
		window.external.addFavorite(url, wTitle);
	 }
	 catch (e) {
		 try {
			window.sidebar.addPanel(wTitle, url, "");
		 }
		 catch (e) {
			alert('添加失败\n您可以尝试通过快捷键 ctrl + D 加入到收藏夹~') 
		 }
	 }
}

//设为首页
function setHome(url) { 
	if (document.all){ 
		document.body.style.behavior='url(#default#homepage)'; 
		document.body.setHomePage(url); 
	}else if (window.sidebar){ 
		if(window.netscape){ 
			try{ 
				netscape.security.PrivilegeManager.enablePrivilege("UniversalXPConnect"); 
			}catch (e){ 
				alert( "该操作被浏览器拒绝，如果想启用该功能，请在地址栏内输入 about:config,然后将项 signed.applets.codebase_principal_support 值该为true" ); 
			} 
		} 
		if(window.confirm("你确定要设置"+url+"为首页吗？")==1){ 
			var prefs = Components.classes['@mozilla.org/preferences-service;1'].getService(Components.interfaces.nsIPrefBranch); 
			prefs.setCharPref('browser.startup.homepage',url); 
		} 
	} 
} 

//確定跳轉
function confirmurl(url,message) {
    if(confirm(message)) redirect(url);
}

function redirect(url,win) {    
    var lct = typeof(win)!="undefined" ? win.location : location;
    //console.log(lct);
    lct.href = url;
}

//回頂
function goTop(){
	$("html,body").animate({scrollTop:0},800);			
}

function navSlideDown(obj,childrenTag){
	var slideTimer;
	var slideTimer2;
	var $ul = $(this).children(childrenTag);
	obj.hover(function(){
		//clearTimeout(slideTimer2)
		if($(this).find(".nCon").length>0){
			$(this).addClass("hover").children(childrenTag).slideDown("fast");
		}else{
			$(this).addClass("hover").addClass("hover2").children(childrenTag).slideDown("fast");
		}			
	},function(){
		$this = $(this);
		$this.children(childrenTag).stop(false,true);
		$this.removeClass("hover").removeClass("hover2").children(childrenTag).hide();
	})
}


(function($){
$.jNotify = {
		defaults : {
			position:{"left":0,"bottom":100}
			,fade : true 
			,fade_out_speed: 1000
			,auto_close:5000
			,close_btn : true 
			,wraperWidth : "auto"
			,iconDefault : {'info':'fa-info-circle','error':'fa-exclamation-circle','success':'fa-check-circle','warning':'fa-warning','default':'fa-chevron-circle-right','debug':'fa-bug'}
		},
		options :{},
		timer :[],
		response:{},
		setting:function(options){
			this.defaults =  $.extend({}, this.defaults, options);
		},
		add:function(params){
			
			this.options = $.extend({}, this.defaults, params);
			var msg = this.options.msg ; 
			var msgType = typeof(this.options.type) != 'undefined' ?  this.options.type :'default'  ;
			var iconArray = this.o2a(this.options.iconDefault);
			 //console.log(msgType)
			
			var icon = typeof(this.options.icon) != 'undefined' ? this.options.icon : iconArray[msgType] ;
			//console.log(iconArray['default'])
			var iconHtml = " <i class=\"fa "+icon+"\"></i>" ;
			var msgStr = iconHtml + msg ;
			var qid = Math.floor(Math.random()*9999999);
			var itemId = "j-jNotify-item-"+ qid;
			var $domItem = this._domConstruct(itemId,msgStr) ;
			if (!isNaN(this.options.wraperWidth)){
				$domItem.width(this.options.wraperWidth);
			};
			if(this.options.close_btn){
				$domCloseBtn = this._domCloseBtn();
				$domCloseBtn.appendTo($domItem).click(function(){
					$.jNotify.close(itemId,false)
				})
			}
			$domItem.addClass("j-jNotify-item-"+msgType).show();
			if(!isNaN(this.options.position.left)){$domItem.css({"float":"left"})}
			if(!isNaN(this.options.position.right)){$domItem.css({"float":"right"})}
			if(this.options.auto_close>0){
				var retention_time = this.options.auto_close ;
				this.timer['_item_timer'+qid] = setTimeout(function(){$.jNotify.close(itemId,$.jNotify.options.fade)},retention_time)
			}
			this.response = {
				'qid':qid
				,'itemId':itemId
				,'type': msgType
			}
			if(this.options.callback){ this.options.callback(this.response); }else{ return(this.response); }

		},
		//DOM
		//構造DOM外層:wraper
		_domWrapper:function(){
			if($('#j-jNotify-wrapper').length == 0){
				var $wraper = $("<div id=\"j-jNotify-wrapper\" class=\"j-jNotify-wrapper\"></div>") ;
				$wraper.appendTo('body').css({'position':'fixed'});
				if(!isNaN(this.options.position.top)){
					var pTop = this.options.position.top ;
					$wraper.css({'top':pTop});
				}
				if(!isNaN(this.options.position.bottom)){
					var pBottom = this.options.position.bottom ;
					$wraper.css({'bottom':pBottom});
				}
				if(!isNaN(this.options.position.left)){
					var pLeft = this.options.position.left ;
					$wraper.css({'left':pLeft});
				}
				if(!isNaN(this.options.position.right)){
					var pRight = this.options.position.right ;
					$wraper.css({'right':pRight});
				}
				return $wraper;
			}
		},
		//關閉按鈕
		_domCloseBtn:function(){
			 $domCloseBtn = $("<a class=\"j-jNotify-close\" href=\"javascript:void(0);\">×</a>");
			 return $domCloseBtn;
		},
		//構造DOM元素:item
		_domConstruct:function(id, str){
			this._domWrapper();
			var $domItem = $("<div class=\"j-jNotify-item\" id=\""+id+"\" ></div>").html(str).hide().appendTo($('#j-jNotify-wrapper'));
			return $domItem;

		},
		o2a : function(o){
			var arr = [] ;
			for(var n in o){  
				arr[n] = o[n] ;
			}
			return arr;
		},
		close : function(itemId,fade){
			if(fade){
				//console.log(this.options.fade_out_speed)
				$("#"+itemId).fadeOut(this.options.fade_out_speed)
				setTimeout(function(){
					$("#"+itemId).remove()
				},this.options.fade_out_speed)
			}else{
				$("#"+itemId).remove();	
			}
			
		},
		closeAll : function(){
			$(".j-jNotify-item").remove();		
		}
		
	}
})(jQuery);

(function($){

	$.fn.jDropBtn = function(options){
	defaults={
		event:"click"
		,effect: "show"
		,speed : 0
	}	
	var options=$.extend(defaults,options);
	this.each(function(){
		var optHtml = $(this).attr("data-options");
		if(typeof(optHtml)!="undefined" || $.trim(optHtml)!=""){
			var opt = eval("({"+optHtml+"})"); 
			alert(opt)
			if(typeof(opt)!="undefined"){
				options=$.extend(options,opt);
			}
		}
		//console.log(options.event)
		var $this = $(this);
		var $dropBtn = $(this).children(".u-drop-btn");
		var $dropMenu = $(this).children(".u-drop-menu");
		switch(options.event){
			case "hover": 
				$(this).hover(function(){
					dropMenuAction($this,1)
				},function(){
					dropMenuAction($this,0)
				})
				break;
			default:
				$dropBtn.click(function(event){
					event.stopPropagation();
					if($dropMenu.is(":visible")){
						dropMenuAction($this,0)
					}else{
						dropMenuAction($this,1)
					}
				})
				$dropMenu.click(function(event){
					event.stopPropagation();
				})
				$('body').click(function(){
					dropMenuAction($this,0)
				})
		}
	})
	function dropMenuAction($obj,action){
		var action = action || 0 ;
		var $dropMenu = $obj.children(".u-drop-menu");	
		var speed = options.speed;
		action ? $obj.addClass("current") :  $obj.removeClass("current") ;
		switch(options.effect){
			case "slide"||"slideDown":
				action ? $dropMenu.slideDown(speed) : $dropMenu.slideUp(speed);
				break;
			case "fade":
				action ? $dropMenu.fadeIn(speed) : $dropMenu.fadeOut(speed);
				break;
			default:
				action ? $dropMenu.show(speed) : $dropMenu.hide(speed);
		}
	}
}
})(jQuery);



(function($){
    $.fn.inputFeedback =function(options){
        if(typeof(options)=="string"){
            this.each(function(){
                callback = $(this).data('callback');
                switch(options){
                    case 'reset':
                        var $inputWrap = typeof(callback)=="undefined" || typeof(callback.$inputWrap)=="undefined" ? $(this).parent() : callback.$inputWrap; 
                        var $helpBox =  $inputWrap.find('.help-inline').length > 0 ? $inputWrap.find('.help-inline') : $inputWrap.find('.help-block');
                        $helpBox.show();
                        $inputWrap.find('.J-feedback-msg').hide();
                        $inputWrap.removeClass("has-info has-success has-warning has-error has-feedback")
                        $inputWrap.find(".form-control-feedback").remove();
                    break;
                }
            });

        }else{
            defaults = {
                addWay:"append"
                ,info:""
                ,status:1
                ,inputIcon:1
                ,styleArray:[
                    {'name':'error','ico':'fa-times','ico_o':'fa-times-circle'}
                    ,{'name':'success','ico':'fa-check','ico_o':'fa-check-circle'}
                    ,{'name':'warning','ico':'fa-warning','ico_o':'fa-info-circle'}
                    ,{'name':'info','ico':'','ico_o':'fa-info'}
                ]
            }

            var options = $.extend(defaults,options);
            this.each(function(){
                var $inputWrap = typeof(options.inputWrap)=="undefined" ? $(this).parent() : options.inputWrap;
                var $addTarget =  typeof(options.addTarget)=="undefined" ? $(this).parent() : options.addTarget;
                
                if($inputWrap.find('.help-inline').length > 0){
                	var mbClass = 'help-inline';
                	var $helpBox = $inputWrap.find('.help-inline');
                }else{
                	var mbClass = 'help-block';
                	var $helpBox = $inputWrap.find('.help-block');
                }
                
                var fa_html = "<span class=\"fa form-control-feedback\"></span>"
                var currentStyle = options.styleArray[parseInt(options.status)] ;

                $inputWrap.removeClass("has-success has-warning has-error has-feedback has-info");
                $inputWrap.find(".form-control-feedback").remove();
                $inputWrap.find(".J-feedback-msg").hide();
                $helpBox.hide();
                  
                var callback = {};
                    callback.options = options; 
                    callback.$inputWrap = $inputWrap;  
                    callback.$addTarget = $addTarget; 

                if(options.status<4 ){
                    $inputWrap.addClass('has-'+currentStyle.name).addClass("has-feedback");
                    if(options.inputIcon){
                        if($inputWrap.find(".form-control-feedback").length < 1 ){
                            $inputWrap.append(fa_html);
                        }
                        $inputWrap.find(".form-control-feedback").addClass(currentStyle.ico);
                    }
                    var length_msgBox = $inputWrap.find(".J-feedback-msg").length;
                    if($.trim(options.info)!=""){
                        if(!length_msgBox) {
                            var msgBoxHtml = "<span class=\"J-feedback-msg feedback-msg "+mbClass+"\"></span>";
                            switch(options.addWay){
                                case 'append':
                                    $addTarget.append(msgBoxHtml);
                                    break;
                                case 'before':
                                    $addTarget.before(msgBoxHtml);
                                    break;
                                case 'after':
                                    $addTarget.after(msgBoxHtml);
                                    break;
                                default:
                                $addTarget.append(msgBoxHtml) ;    
                            }
                            
                        }
                        var $msgbox = $inputWrap.find(".J-feedback-msg");
                        $msgbox.removeClass("feedback-msg-success feedback-msg-warning feedback-msg-error feedback-msg-info").addClass('feedback-msg-'+currentStyle.name);
                        callback.$msgbox = $msgbox; 
                        var icoHtml ="<i class=\"fa "+currentStyle.ico_o+"\"></i>";
                        $msgbox.html(icoHtml+" "+options.info).show();
     
                    }
                    return true ;
                }else{
                    $helpBox.show();
                    return true ;
                }
                $(this).data('callback',callback);
                if(typeof(options.success)=="function"){
                    success(callback);
                }
            })
        }
    }

})(jQuery);


//submit_ajax
//提交表单
function submit_ajax(obj,success,before,action){
    var submit_data = $(obj).serializeArray();
    var submint_url = action || $(obj).attr("action");
    var subMarker = [] ;
    subMarker['loading_wrap'] = $(obj).closest('.modal-content').length > 0 ? $(obj).closest('.modal-content') : $('body') ;
    subMarker['loading_btn'] = $('.modal-btn-submit').length > 0 ? $('.modal-btn-submit') : $('.J-btn-submit');
    var autoTips = typeof(before)!='undefined' && $.isNumeric(before) && before ==0 ? 0 : 1 ;
    $.ajax({
        dataType:"json"
        ,type:"post"
        ,url:submint_url
        ,data:submit_data
        ,beforeSend:function(){
        	subMarker['loading_btn'].button('loading');
        	if(typeof(before)=="function"){
                if(!before(subMarker)){ return false; }               
            } 
       
        }
        ,success:function(json){

            if(json.status){
            	if(autoTips){
            		$.jNotify.add({msg:json.info,type:"success"});	
            	}
                

                if(typeof(success)=="function"){
                    if(!success(json)){
                        subMarker['loading_btn'].button('reset');
                        return false;
                    }
                    
                }                
                if(typeof(json.reload)!="undefined"&&json.reload){
                	if(autoTips){
            			alert(json.info);	
            		}
                    location.href = location.pathname + location.search;
                    subMarker['loading_btn'].button('reset');
                }else if(typeof(json.url)!="undefined"&&json.url!=""){
                    if(autoTips){
            			alert(json.info);	
            		}
                    redirect(json.url);
                    subMarker['loading_btn'].button('reset');
                }
               subMarker['loading_btn'].button('reset');  
            }else{
            	if(autoTips){
                	$.jNotify.add({msg:json.info,type:"error"});
            	}
                if(typeof(success)=="function"){
                    success(json);
                }
                subMarker['loading_btn'].button('reset');
                return false;
            }

            return false;
        }
        ,error: function(XMLHttpRequest, textStatus, errorThrown) {
            return false;
        }
      
    })
    return false;
 }