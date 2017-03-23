var GV = {
    MODAL:[],
    VAR:[]
}


/************************************/
// 瀏覽器窗口相關函數
/************************************/
$.browser = {}
$.browser.mozilla = /firefox/.test(navigator.userAgent.toLowerCase());
$.browser.webkit = /webkit/.test(navigator.userAgent.toLowerCase());
$.browser.opera = /opera/.test(navigator.userAgent.toLowerCase());
$.browser.msie = /msie/.test(navigator.userAgent.toLowerCase());


function getBrowserVersion(){
    var agent = navigator.userAgent.toLowerCase() ;
    var regStr_ie = /msie [\d.]+;/gi ;
    if(agent.indexOf("msie") > 0){
        return  parseInt((agent.match(regStr_ie)+"").split("msie")[1].split(";")[0])        
    }else{
        return "" ;     
    }
}

function getClientSize(){
    GV.VAR['clientHeight']=document.documentElement.clientHeight;
    GV.VAR['clientWidth']=document.documentElement.clientWidth; 
    return {'height':GV.VAR['clientHeight'],'width':GV.VAR['clientWidth']} ;
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

//重新刷新页面，使用location.reload()有可能导致重新提交
function reloadPage(win) {
    var lct = typeof(win)!="undefined" ? win.location : location;
    lct.href = lct.pathname + lct.search;
}

//打開窗口
function openwinx(url,name,w,h) {
    var sw = screen.width
    if(!w) w= sw-150 < 990 ? sw : sw-150; 
    if(!h) h=screen.height-95;
    var l = (sw-w)/2
    window.open(url,name,"top=100,left="+l+",width=" + w + ",height=" + h + ",toolbar=no,menubar=no,scrollbars=yes,resizable=yes,location=no,status=no");
    //window.open(url,name)
}

// getRequest
function getRequest(cx){
    var cxo =""
    var url=window.location.search;
    if(url.indexOf("?")!=-1)   
    {   
      var str   =   url.substr(1)   
      strs = str.split("&");   
      for(i=0;i<strs.length;i++){   
        if([strs[i].split("=")[0]]==cx) cxo=unescape(strs[i].split("=")[1]);
      }   
    }   
    return cxo;
}


//加載js
function loadJs(path,check,async){
    if(!path || path.length === 0){
        throw new Error('argument "path" is required !');
    }
    var isAsync = typeof(async)=="undefined" ? false : typeof(async)=="function" ? true : async  ;
    var isCache = true; 
    if(typeof(check)=="string" && check == "not cache"){
       isCache = false;
    } 

    if(typeof(check)=="undefined" || isCache==false){
        $.ajax({
            url: path
            ,dataType: "script"
            ,async:isAsync
            ,cache:isCache
            ,success:function(){
                if(typeof(async)=="function"){
                    async();
                }
            }
        })  
    }
    //loadjscssfile(filename,'js')
}




//禁止默認事件
function stopDefault(e) {
    if ( e && e.preventDefault ){
        e.preventDefault();
    }else{
       window.event.returnValue = false; 
    }
} 

//禁止冒泡
function stopPropagation(evt) {
 var e=(evt)?evt:window.event;
 if (window.event) {
  e.cancelBubble=true; //ie
 }else{
  e.stopPropagation();
 }
}


/************************************/
// 加載相關函數
/************************************/

//加載css
function loadCss(path){
    if(!path || path.length === 0){
        throw new Error('argument "path" is required !');
    }
    var head = document.getElementsByTagName('head')[0];
    var link = document.createElement('link');
    link.href = path;
    link.rel = 'stylesheet';
    link.type = 'text/css';
    head.appendChild(link);
    //loadjscssfile(filename,'css')
}

//加載js或css
function loadjscssfile(filename,filetype){
    if(filetype == "js"){
        var fileref = document.createElement('script');
        fileref.setAttribute("type","text/javascript");
        fileref.setAttribute("src",filename);
    }else if(filetype == "css"){
        var fileref = document.createElement('link');
        fileref.setAttribute("rel","stylesheet");
        fileref.setAttribute("type","text/css");
        fileref.setAttribute("href",filename);
    }
   if(typeof fileref != "undefined"){
        document.getElementsByTagName("head")[0].appendChild(fileref);
    }
}



/**
 * 全选checkbox
 * @param string name 列表check名称,如 uid[]
 */
function selectall(obj,name) {
    if ($(obj).prop("checked")) {
        $("input[name='"+name+"']").each(function() {
            $(this).prop("checked",true);
        });
    } else {
        $("input[name='"+name+"']").each(function() {
            $(this).prop("checked",false);
        });
    }
}


/************************************/
// cookies相關函數
/************************************/

// 设置cookies
function setCookie(name,value,days){ 
    if(days==null || days==0) days=1;
    var exp = new Date(); 
    exp.setTime(exp.getTime() + days*24*60*60*1000); 
    document.cookie = name + "="+ escape (value) + ";expires=" + exp.toGMTString(); 
} 

//读取cookies 
function getCookie(name) 
{ 
    var arr,reg=new RegExp("(^| )"+name+"=([^;]*)(;|$)");
 
    if(arr=document.cookie.match(reg))
 
        return unescape(arr[2]); 
    else 
        return null; 
} 

//删除cookies 
function delCookie(name) 
{ 
    var exp = new Date(); 
    exp.setTime(exp.getTime() - 1); 
    var cval=getCookie(name); 
    if(cval!=null) 
        document.cookie= name + "="+cval+";expires="+exp.toGMTString(); 
}



/************************************/
// 類對象、字符串操作相關函數
/************************************/

//對像轉數組
function o2a(o){
    var arr = [] ;
    for(var n in o){  
        arr[n] = o[n] ;
    }
    return arr;
}
//字符串轉obj
function s2o(s){
    return obj = eval('(' + s + ')');
}

//判定是否数组
function isArray(obj) {   
  return Object.prototype.toString.call(obj) === '[object Array]';    
}  

 //获取字符长度
function getByteLen(val) {
    var len = 0;
    return val.replace(/[^\x00-\xff]/g,'**').length;
} 

 //获取字符长度
function strlen(str) {
 return ($.browser.msie && str.indexOf('\n') != -1) ? str.replace(/\r?\n/g, '_').length : str.length;
}


function getUtf8_String(str,len){
    var strlen = 0; 
    var s = "";
    for(var i = 0;i < str.length;i++){
        if(str.charCodeAt(i) > 128){
            strlen += 3;
        }else{ 
            strlen++;
        }
        s += str.charAt(i);
        if(strlen >= len){ 
            return s ;
        }
    }
    return s;
}



function subString(str, len, hasDot){ 
    var newLength = 0; 
    var newStr = ""; 
    var chineseRegex = /[^\x00-\xff]/g; 
    var singleChar = ""; 
    var strLength = str.replace(chineseRegex,"**").length; 
    for(var i = 0;i < strLength;i++){ 
        singleChar = str.charAt(i).toString(); 
        if(singleChar.match(chineseRegex) != null){ 
            newLength += 2; 
        }else{ 
            newLength++; 
        } 
        if(newLength > len){ 
            break; 
        } 
        newStr += singleChar; 
    } 
     
    if(hasDot && strLength > len){ 
        newStr += "..."; 
    } 
    return newStr; 
} 


//js去掉所有html标记的函数： 
function delHtmlTag(str){
return str.replace(/<[^>]+>/g,"");
}


//追加值到队列字符串後邊
function joinQueueString(add,target,separator,action){
    separator = separator || "," ;
    action = action || "add" ;
    var target = $.trim(target);
    var add = $.trim(add);
    var s = ""
    if(target.indexOf(separator)>-1){
        var targetArray = target.split(separator);
        $.each(targetArray,function(i,item){
            if(item!="" ) s = item==add ? s : s=="" ? item : s+separator+item;
        })
    }else{
        s =  target == add ? "" : target;
    }
    s = $.trim(action) == 'add'  ? s+separator+add : s ;
    if (s.substr(0,1)==separator) s=s.substr(1)
    return s;
}

//從隊列字符串裡刪除值
function leaveQueueString(add,target,separator){
    separator = separator || "," ;
    return joinQueueString(add,target,separator,"leave");
}

//把值加入到input中。
// data 字段对应的值以对象形式设置在，
// options.resultTarget 接收赋值的元素，设置某input对应的字段。
// options.resultTargetWrap 接收赋值的元素所在的位置。
// options.isJoin 是否采用追加模式。

function fillSelected(data,options){
    defaults = {
      isJoin:0 
      //,resultTarget:{".J-input-userid":"userid",".J-input-username":"username"}
      ,resultTarget:[
        {target:".J-input-userid",field:"userid",separator:','},
        {target:".J-input-username",field:"username",separator:','}
      ]
      ,separator:','
      
    }
    var options = $.extend({},defaults,options);
    var nodeArray = o2a(data);
    var separator = options.separator;
    var rt_settingType = typeof(options.resultTarget[0])=="undefined" ? 0 : 1
    $.each(options.resultTarget,function(i,item){

        if(rt_settingType){
            var target = item.target
            var $targetInput = options.resultTargetWrap && options.resultTargetWrap.length>0 ?  options.resultTargetWrap.find(target) : $(target);
            var field = item.field
                separator = typeof(item.separator)=="undefined" ? separator : item.separator ;
        }else{
            var $targetInput = options.resultTargetWrap && options.resultTargetWrap.length>0 ?  options.resultTargetWrap.find(i) : $(i);            
            var field = item
        }
        var tagName = $targetInput.prop('tagName').toLowerCase();
        

        var tagType = 0 ;
        if(tagName=='input'||tagName=='textarea'){
            tagType = 0;
        }else if(tagName=='div'||tagName=='span'||tagName=='a'||tagName=='b'){
            tagType = 1
        }
        if(options.isJoin==0){
            var val_reset = nodeArray[field] ;
        }else{
            var val_target = tagType ? $targetInput.html() : $targetInput.val();
            var val_add = nodeArray[field];
            
            var val_reset = options.isJoin==1 ? joinQueueString(val_add,val_target,separator) : leaveQueueString(val_add,val_target,separator) ;
        }
        if(tagType){
            $targetInput.html(val_reset)
        }else{
            $targetInput.val(val_reset);
          
        }
      
    })
    if(typeof(options.fn)=="function"){
        options.fn(data);
    }
} 


function getValFromDom(options){
    defaults = {      
      resultTarget:[
        {target:".J-input-userid",field:"userid"},
        {target:".J-input-username",field:"username"}
      ]
      ,separator:','
      
    }
    var options = $.extend(defaults,options);
    var separator = options.separator;
    var rt_settingType = typeof(options.resultTarget[0])=="undefined" ? 0 : 1
    var defaultsVal = {}
    $.each(options.resultTarget,function(i,item){
        if(rt_settingType){
            var target = item.target
            var $targetInput = options.resultTargetWrap && options.resultTargetWrap.length>0 ?  options.resultTargetWrap.find(target) : $(target);
            var field = item.field
        }else{
            var $targetInput = options.resultTargetWrap && options.resultTargetWrap.length>0 ?  options.resultTargetWrap.find(i) : $(i);            
            var field = item
        }
        var tagName = $targetInput.prop('tagName').toLowerCase();
        var tagType = 0 ;
        if(tagName=='input'||tagName=='textarea'){
            tagType = 0;
        }else if(tagName=='div'||tagName=='span'||tagName=='a'||tagName=='b'){
            tagType = 1
        }

        if(tagType){
           var  val_o = $targetInput.text();
        }else{
           var  val_o = $targetInput.val();
        }
        defaultsVal[field] = val_o ;
        
    })
    return defaultsVal;

}




/************************************/
// dom\交互相關函數
/************************************/

//倒计时
function timer(intDiff,options){
    defaults = {
        showWrap:$('.timerWrap'),
        timeType:0 //如果为0，intDiff=剩余时间，如果为1，intDiff=结束时间戳

    }
    var options = $.extend(defaults,options);
    var now =new Date().getTime();
    var laveTime = options.timeType == 1 ? intDiff-Math.floor(now/1000) : intDiff;
    var day=0,
        hour=0,
        minute=0,
        second=0;//时间默认值        
    if(laveTime > 0){
        day = Math.floor(laveTime / (60 * 60 * 24));
        hour = Math.floor(laveTime / (60 * 60)) - (day * 24);
        minute = Math.floor(laveTime / 60) - (day * 24 * 60) - (hour * 60);
        second = Math.floor(laveTime) - (day * 24 * 60 * 60) - (hour * 60 * 60) - (minute * 60);
    }
    if (minute <= 9) minute = '0' + minute;
    if (second <= 9) second = '0' + second;
    if(day<2){
        options.showWrap.addClass('exigent');
    }
    var $d = options.showWrap.find('.d').length>0 ? options.showWrap.find('.d') : $("<span class='d'></span>").appendTo(options.showWrap);
    var $h = options.showWrap.find('.h').length>0 ? options.showWrap.find('.h') : $("<span class='h'></span>").appendTo(options.showWrap);
    var $m = options.showWrap.find('.m').length>0 ? options.showWrap.find('.m') : $("<span class='m'></span>").appendTo(options.showWrap);
    var $s = options.showWrap.find('.s').length>0 ? options.showWrap.find('.s') : $("<span class='s'></span>").appendTo(options.showWrap);
    $d.html('<b>'+day+'</b>天');
    $h.html('<b>'+hour+'</b>时');
    $m.html('<b>'+minute+'</b>分');
    $s.html('<b>'+second+'</b>秒');
    
    if(typeof(options.fn)=="function"){
        var callback = {};
        callback.laveTime = laveTime;
        callback.options = options;
        if(options.fn(callback)==false){
            return false;
        }
    }
    laveTime--;
    if(laveTime>=0){
        var lavetimer = setTimeout(function(){    
                intDiff = options.timeType == 1 ? intDiff : laveTime;
                timer(intDiff,options);
        }, 1000);
    }
} 

function checkLen(obj,len,msg,returnType){
    returnType = returnType || 0 ;
    var returnData = {};
    var $input = $(obj);
    if(len>0){
        var inputLen = getByteLen($input.val())
        if(inputLen < len){
            msg = msg?msg:"不得少于"+len+"位";
            return returnType ? $.jNotify.add({'msg':msg,'type':'error'}) : {"status":0,"info":msg} ;
        }else{
            return {"status":1,"info":""}
        }
    }else{
        var inputVal = $.trim($input.val());
        if(inputVal ==""){
            msg = msg?msg:"不能为空";
            return returnType ? $.jNotify.add({'msg':msg,'type':'error'}) : {"status":0,"info":msg} ;
        }else{
            return {"status":1,"info":""}   
        }
    }
}

//回到錯誤的input
function goToErrorInput(formObj){
        var $form = $(formObj).button('reset')
        var $errorInput = $form.find(".tip-error").eq(0)
        var errorTop = $errorInput.offset().top-50
        $("html,body").animate({scrollTop:errorTop},500);
        return false    
}



//load
function loading_cover($target,action,fa){
    action = typeof(action)=="undefined" ? "add" :action ; 
    fa = typeof(fa)=="undefined" ? "circle-o-notch" :action ; 
    var load_html = "<div  class=\"js-loading-wrap\"><i class=\"fa fa-"+fa+" fa-spin js-loading\"></i></div>";
    if(action=="removeall" || action=="removeAll"){
        $target.find(".js-loading-wrap").remove();
    }else if(action=="remove"){
        $target.children(".js-loading-wrap").remove();
    }else if(action=="add"){
        $target.append(load_html);
        var $wrap = $target.children(".js-loading-wrap");
        var $loadspin =$wrap.children(".js-loading");
        var t_h = $wrap.height();
        var s_h = $loadspin.height();
        var m_t = (t_h-s_h)/2 ;
        $loadspin.css({"margin-top":m_t});
    }
}


//bootstrap Modal 改進
function createModal(options){
    defaults = {
        title:"標題"
        ,modal_id:"js_myModal"
        ,cache:false
        ,size:""
        ,width:""
    }
    var options = $.extend(defaults,options);
    var model_html = "";
    model_html += '<div class="modal" id="'+options.modal_id+'">';
    model_html += '<div class="modal-dialog" >'
    model_html += '    <div class="modal-content">'
    model_html += '      <div class="modal-header">'
    model_html += '        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>'
    model_html += '        <h4 class="modal-title">'+options.title+'</h4>'
    model_html += '      </div>'
    model_html += '     <div class="modal-body"></div>'
    //model_html += '      <div class="modal-footer">'
    //model_html += '        <button type="button" class="btn btn-default" data-dismiss="modal">取消</button>'
    //model_html += '        <button type="button" class="btn btn-primary savebtn"><i class="fa fa-save"></i> 保存</button>'
    //model_html += '      </div>'
    model_html += '    </div>'
    model_html += '  </div>'
    model_html += '</div>'
    if($("#"+options.modal_id).length>0){
        $("#"+options.modal_id).modal({keyboard:true});
    }else{      
        $('body').append(model_html);   
    }
    if(options.width!=""){
        $("#"+options.modal_id).find(".modal-dialog").width(options.width); 
    }
    
    $("#"+options.modal_id).find(".modal-title").html(options.title);
    var $modal = $("#"+options.modal_id);
    var $modal_body = $modal.find(".modal-body");
    $modal.modal('show');
    $(document).off('focusin.modal');
    $.ajax({
        dataType:"html"
        ,type:"get"
        ,cache:false
        ,url:options.url
        ,data:{}
        ,beforeSend:function(){$modal.find(".modal-body").append("<div  class=\"js-modal-loading-wrap\"><i class=\"fa fa-circle-o-notch fa-spin js-loading\"></i></div>")}
        ,success:function(data){
            $modal_body.html(data);
        }

    })  
        
}

/*
 options.url 請求路徑，如果無，則從form上的action上取，
 options.data 提交參數，如果無，則從form內的input取。
*/
function deleteItem(obj,options){
    
    defaults = { 
        confirm:"汝確定要刪除?"
        ,itemTag:"tr"
        ,batch:false //是否批量
    }
    var options = $.extend(defaults,options);
    var data  = {};
    if(typeof(options.data)!="undefined"){
        var data = options.data;
    }
    var url = '';
    if(options.batch){
        if(typeof(options.form)!="undefined"){
            var $form = options.form;
        }else{
            var $form = $(obj).parents('form');
        }
        if(typeof(options.url)=="undefined" && options.url==""){
            url = $form.attr('action')
        }else{
            url = options.url
        }
        data = $.extend($form.serializeArray(),data);

    }else{
        if(typeof(options.url)=="undefined" && options.url==""){
            $.jNotify.add({msg:"無url",type:"error"})
            return false;    
        }else{
            url = options.url
        }    
    }
    
    if(confirm(options.confirm)){
      if(typeof($.fn.button)!="undefined"){
        $(obj).button('loading');
      }
      var msg_beforesend = options.batch ? "批量刪除中.." : "刪除中.." ;
      var loadingMarker = $.jNotify.add({msg:msg_beforesend,type:"info",auto_close:0})

      //console.log($form.serializeArray())
      $.post(options.url,data,function(json){
        if(typeof($.fn.button)!="undefined"){
            $(obj).button('reset');
        }

        $.jNotify.close(loadingMarker.itemId,1000);
        if(json.status){
            var $listWrap = $(obj).closest('.table_list');
            $.jNotify.add({msg:json.info,type:"success"})
            if(options.batch){
                $.each(json.ids,function(i,item){
                    $form.find(options.itemTag+"[data-id="+item+"]").remove();
                })
            }else{
                $(obj).closest(options.itemTag).remove();    
            }

            if(typeof(options.success)=="function"){
               options.success(json)
            }else{
                if(typeof(json.url)!="undefined"&&json.url!=""){
                    redirect(json.url)
                }else if($listWrap.find('.pages').length>0){
                    if(typeof(json.reload)!="undefined"&&json.reload){
                        alert(json.info);
                        reloadPage();
                    }
                    
                }
            } 
        }else{
          $.jNotify.add({msg:json.info,type:"error"})
        }

      })
    }
}

//選擇樹
function getTreeSelect(options,btns){
  
    defaults = { 
        title:"請選擇"
        ,treeName:"cateTree"
        ,separator:','
        ,autoClose:false
        ,batch:false
        ,cache:true 
        ,width:500
        ,top:30
        ,url:"/index.php?m=Content&c=Content&a=public_select"
        ,idField:'catid'
        ,treeField: 'catname'
        ,columns:[[
          {field:'catid',title:'ID',width:'10%'}
          ,{field:'catname',title:'欄目'}
        ]]
        ,resultTarget:[
            {target:"input[name=catid]",field:"catid",separator:','},
            {target:"input[name=catname]",field:"catname"}
        ]
    }
    var options = $.extend({},defaults,options);
    var tableId = "J-table-"+options.treeName;
    var modalId = "J-Modal-"+options.treeName;
    var cache = {}
        
    

    if(options.url==""){
        $.jNotify.add({'type':'debug','msg':'参数错误'})
        return false;
    }
    if(typeof($.fn.jTreegrid)=="undefined"){
        $.ajax({url: GV.JS_ROOT+"jfuc/jfuc.treegrid.js",dataType: "script",async:false,cache:true})  
    }
    if($("#"+tableId).length>0){
        $cateTree = $("#"+tableId);
    }else{
        $cateTree = $("<table  id=\""+tableId+"\" class=\"J-tree table table-bordered table-hover table-tree table-condensed\"></table>")  
    }

    defaultBtns = [
        {id:'refresh',onclick:function(btn,modal){
            $cateTree.jTreegrid('reload')
        }}
        ,{id:'close',icon:'fa fa-check',title:"確定",btnClass:"btn btn-primary"}
    ]
    var btns = !options.batch && options.autoClose ? false : $.extend({},defaultBtns,btns);
    var callbackData = {}
        callbackData.options    = $.extend({},options)
        callbackData.btns       = $.extend({},btns)
        callbackData.$treeDom    = $cateTree

    $cateTree.jTreegrid({
        idField:options.idField
        ,treeField:options.treeField
        ,url:options.url
        ,cache:options.cache
        ,columns:options.columns
        ,onBeforeLoad:function(){GV.VAR['loadmark_cateTree'] = $.jNotify.add({'msg':'加載欄目數據中'})}
        ,onLoadSuccess:function(datas){


            $.jNotify.close(GV.VAR['loadmark_cateTree'].itemId);
            var defaultsID = 0 ;
            var rt_settingType = typeof(options.resultTarget[0])=="undefined" ? 0 : 1 ;
            var separator = options.separator;
            cache.atFirstSelected =getValFromDom(options);
            var atFirstVal = cache.atFirstSelected;            
            defaultsID = atFirstVal[options.idField] ;

            if(typeof(options.onLoadTreeSuccess)=="function"){
                options.onLoadTreeSuccess(datas,callbackData);
            }

            if(options.batch){
                
                $.each(options.resultTarget,function(i,item){
                    if(item.field==options.idField){
                        separator = typeof(item.separator)!="undefined" ? item.separator : separator ;
                        return false
                    }
                })

                var defalutsArray = defaultsID.split(separator);

                $.each(defalutsArray,function(i,item){
                    if(item){
                        var $row = $cateTree.jTreegrid('select', item);
                        $row.addClass('J-multiple-row-selected');    
                    }
                })
            }else{
                if($.trim(defaultsID)!=""){
                    defaultsID = parseInt(defaultsID);
                    $cateTree.jTreegrid('select', defaultsID); 
                }    
            }
            

            
        }
        ,onClickRow:function(node){

            $row = node.$node
            
            cache.prevSelected = getValFromDom(options)
            callbackData.atFirstSelected = cache.atFirstSelected
            callbackData.prevSelected = cache.prevSelected

            if(typeof(options.beforeClickRow)=="function"){
                options.beforeClickRow(node,callbackData);
            }
            if(typeof(options.onClickRow)=="function"){
                options.onClickRow(node,callbackData);
            }else{

                var isJoin  = 0
                if(options.batch){
                    isJoin  = 1 ;
                    
                    if($row.hasClass('J-multiple-row-selected')){
                        $row.removeClass('J-multiple-row-selected');
                        isJoin = -1 ;
                    }else{
                        $row.addClass('J-multiple-row-selected');
                    }
                    $row.removeClass('J-datagrid-row-selected');
                }
                
                fillSelected(node,{isJoin:isJoin,resultTarget:options.resultTarget,resultTargetWrap:options.resultTargetWrap})


                if(options.autoClose){
                    GV.MODAL[modalId].close();    
                }
            }
            
            if(typeof(options.onAfterClickRow)=="function"){
                options.onAfterClickRow(node,callbackData)
            }
        }
    });

//console.log($cateTree.jTreegrid('getData', 2));
    GV.MODAL[modalId] = $.scojs_modal({
        target:"#"+modalId
        ,cache:options.cache
        ,title:options.title
        ,content:$cateTree
        ,width:options.width
        ,top:options.top
        ,btns:btns
        ,onLoadSuccess:function(Modal){
            var atFirstVal = getValFromDom(options);
            cache.atFirstSelected = atFirstVal;
            callbackData.atFirstSelected = cache.atFirstSelected
        }
    })

  GV.MODAL[modalId].show();  
}


//ajax提交数据
function postAjax(url,postData,success,before,bTips){
    var loadTipReturn = ""
    $.ajax({
        type:"post",
        dataType:"json",
        url:url,
        data:postData,
        beforeSend:function(){
            bTips= typeof(bTips)=="undefined"?"提交中...":bTips;
            loadTipReturn = $.jNotify.add({msg:bTips,type:"info",auto_close:0})
            if(typeof(before)=="function"){
                before(json);
            }
        },
        success:function(json){ 
            $.jNotify.close(loadTipReturn.itemId,1000)  
            if(typeof(success)=="function"){
                success(json);
            }
        },error: function(XMLHttpRequest, textStatus, errorThrown) {
            $.jNotify.close(loadTipReturn.itemId,1000);
            console.log(XMLHttpRequest);
            $.jNotify.add({msg:XMLHttpRequest.status+","+textStatus,type:"debug"})
            //alert(XMLHttpRequest.status);
            //alert(XMLHttpRequest.readyState);
            //alert(textStatus);
        }
    })
}

function submitAjaxLoading(subMarker,action){
    var action = action || 0;  
    if(action){
        subMarker['loading_tip'] = $.jNotify.add({msg:"提交中",type:"info",auto_close:0})
        if(typeof($.fn.button)!="undefined"){
            subMarker['loading_btn'].button('loading');
        }   
        subMarker['loading_wrap'].jShowLoading();
    }else{
        $.jNotify.close(subMarker['loading_tip'].itemId,1000);
        if(typeof($.fn.button)!="undefined"){
            subMarker['loading_btn'].button('reset');
        }  
        subMarker['loading_wrap'].jShowLoading('remove');
    }
}

//submit_ajax
//提交“編輯”和“添加”表單
function submit_ajax(obj,success,before,action){
    var submit_data = $(obj).serializeArray();
    var submint_url = action || $(obj).attr("action");
    var subMarker = [] ;
    subMarker['loading_wrap'] = $(obj).closest('.modal-content').length > 0 ? $(obj).closest('.modal-content') : $('body') ;
    subMarker['loading_btn'] = $('.modal-btn-submit').length > 0 ? $('.modal-btn-submit') : $('.J-btn-submit');
    $.ajax({
        dataType:"json"
        ,type:"post"
        ,url:submint_url
        ,data:submit_data
        ,beforeSend:function(){
            
            submitAjaxLoading(subMarker,1);
            if(typeof(before)=="function"){
                before(json);
            }
        }
        ,success:function(json){
            if(json.status){
                $.jNotify.add({msg:json.info,type:"success"})

                if(typeof(success)=="function"){
                    if(!success(json)){
                        submitAjaxLoading(subMarker,0);
                        return false;
                    }
                    
                }                
                if(typeof(json.reload)!="undefined"&&json.reload){
                    alert(json.info);
                        location.href = location.pathname + location.search;
                        submitAjaxLoading(subMarker,0);
                }else if(typeof(json.url)!="undefined"&&json.url!=""){
                    alert(json.info);
                        redirect(json.url);
                        submitAjaxLoading(subMarker,0);
                }
                submitAjaxLoading(subMarker,0);   
            }else{
                $.jNotify.add({msg:json.info,type:"error"})
                if(typeof(success)=="function"){
                    success(json);
                }
                submitAjaxLoading(subMarker,0);
                return false;
            }

            return false;
        }
        ,error: function(XMLHttpRequest, textStatus, errorThrown) {
            submitAjaxLoading(subMarker,0);
            console.log(XMLHttpRequest);
            $.jNotify.add({msg:XMLHttpRequest.status+","+textStatus,type:"debug"})
            //alert(XMLHttpRequest.status);
            //alert(XMLHttpRequest.readyState);
            //alert(textStatus);
        }
      
    })
    return false;
 }

 //列表页功能按钮提交
function submitListForm(btn,action,ajax){
    var ajax = ajax || 0 ;
    var $btn = $(btn);
    var $form = $btn.closest('form');
    
    if(ajax==1 || typeof(ajax)=="function"){
        if(typeof(ajax)=="function"){
            submit_ajax($form[0],ajax,0,action);
        }else{
            submit_ajax($form[0],0,0,action);
        }
        return false;
    }else{
      $form.attr('action',action);
      $form.submit();
    }
}




////ajax false or true
function upStatus(obj,setting){
        var defaults={
            show : ["<span style='color:red'>×</span>","<span style='color:green'>√</span>"]
            ,name : 'status'
            ,id : 0 
            ,loading : "<img src=\"/statics/images/Common/loading.gif\" width=15 />"
        }
        var options =  $.extend({}, defaults, setting);
        if(options.id==0){
            console.log('setBooleanBtn 參數錯誤')
            return false;
        }
        var $obj= $(obj);
        if(typeof(options.url)=="undefined"){
            var requesturl = window.location.pathname ;
            var m = $.trim(getRequest('m')) ;
            var m_url = m ==""?"": "m="+m+"&" ;
            var c = $.trim(getRequest('c')) ;
            var c_url = c ==""?"": "c="+c+"&";
            options.url = requesturl+"?"+m_url+c_url+"a=up_status";
        }

        var value = $obj.attr('data-value')
        if(value==-1){
            alert("提交中，请稍后");   
        }else{
            $.ajax({
                type: "POST",
                dataType:'json',
                url: options.url,
                cache:false,
                data: {value:value,id:options.id,field:options.name},
                dataType : "json",
                beforeSend:function(){
                    $obj.html(options.loading);
                    $obj.attr("data-value",-1);
                },
                success: function(data) {
                    if(data.status==1){   
                        var reHtml = value == 1  ? options.show[0] : options.show[1];
                        $obj.html(reHtml);
                        $obj.attr("data-value",data.result);
                    }else{
                        alert(data.info)
                        //var reHtml  = value =="1" ? "<span style='color:green'>√</span>" : "<span style='color:red'>×</span>";
                        $obj.html(o_html);
                        $obj.attr("data-value",value);
                    }
                }
    
            });
        }
}


function setBooleanBtn(setting){
    var defaults={
        show : ["<span style='color:red'>×</span>","<span style='color:green'>√</span>"]
        ,on : 'click'
        ,returnStr : 0 
    }
    var options =  $.extend({}, defaults, setting);
    if(typeof(options.value)=="undefined"){
        console.log('setBooleanBtn 參數錯誤')
        return false;
    }

    var on_event = options.on == "click" ? "onclick" : "ondblclick";
    var setting_str = JSON.stringify(setting);
    var btnHtml = "<a href='javascript:void(0);' data-value='"+options.value+"' "+on_event+"='upStatus(this,"+setting_str+")'>";
        btnHtml += options.value =="1" ? options.show[1] : options.show[0];
        btnHtml += "</a>";
    if(options.returnStr){
        return btnHtml;
    }else{
        document.write(btnHtml);
        return false;
    }
}


/************************************/
// 自定義jquery插件類
/************************************/


(function($){
//jNotify
    $.jNotify = {
        defaults : {
            position:{"right":0,"bottom":100}
            ,fade : true 
            ,fade_out_speed: 1000
            ,auto_close:4000
            ,close_btn : true 
            ,wrapperWidth : "auto"
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
            var iconDefault = this.options.iconDefault //this.o2a(this.options.iconDefault);
             //console.log(msgType)
            
            var icon = typeof(this.options.icon) != 'undefined' ? this.options.icon : iconDefault[msgType] ;
            //console.log(iconArray['default'])
            var iconHtml = " <i class=\"fa "+icon+"\"></i>" ;
            var msgStr = iconHtml + msg ;
            var qid = Math.floor(Math.random()*9999999);
            var itemId = "j-jNotify-item-"+ qid;
            var $domItem = this._domConstruct(itemId,msgStr) ;
            if (!isNaN(this.options.wrapperWidth)){
                $domItem.width(this.options.wrapperWidth);
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
        //構造DOM外層:wrapper
        _domWrapper:function(){
            if($('#j-jNotify-wrapper').length == 0){
                var $wrapper = $("<div id=\"j-jNotify-wrapper\" class=\"j-jNotify-wrapper\"></div>") ;
                $wrapper.appendTo('body').css({'position':'fixed'});
                if(!isNaN(this.options.position.top)){
                    var pTop = this.options.position.top ;
                    $wrapper.css({'top':pTop});
                }
                if(!isNaN(this.options.position.bottom)){
                    var pBottom = this.options.position.bottom ;
                    $wrapper.css({'bottom':pBottom});
                }
                if(!isNaN(this.options.position.left)){
                    var pLeft = this.options.position.left ;
                    $wrapper.css({'left':pLeft});
                }
                if(!isNaN(this.options.position.right)){
                    var pRight = this.options.position.right ;
                    $wrapper.css({'right':pRight});
                }
                return $wrapper;
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
// 
    $.fn.jShowLoading = function(options){
        if(typeof(options)=="string"){
            this.each(function(){
                switch(options){
                    case 'remove':
                    if($(this).data("loading")==1){
                        var loadingClass= $(this).data('class_of_loading');
                        $(this).find("."+loadingClass).remove();
                        $(this).data("loading",0)
                    }
                    break;
                }
            })

        }else{
            defaults = {
                position:"absolute",
                opacity:"0.5",            
                background:"#FFFFFF",
                zIndex:"999",
                content:"<img src=\"/statics/images/Common/loading.gif\" />",
                className:'J-show-loading'
            }
            var options = $.extend(defaults,options);
            this.each(function(){
                options.css = typeof(options.css)!="undefined" ? options.css : {};
                options.css = $.extend({"position":options.position,"opacity":options.opacity,"background":options.background,"z-index":options.zIndex,left:0,right:0,top:0,bottom:0},options.css)
                var $loading_wrap = $("<div class=\""+options.className+"\"></div>").css(options.css).appendTo(this);
                var $loading = $("<span>"+options.content+"</span>")
                $loading_wrap.html($loading);
                var top=($loading_wrap.height()-$loading.height())/2
                var left=($loading_wrap.width()-$loading.width())/2
                $loading.css({"position":"absolute","left":left,"top":top});
                $(this).data("class_of_loading",options.className);
                $(this).data("loading",1);

                
            }); 
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
                        $inputWrap.removeClass("has-success has-warning has-error has-feedback")
                        $inputWrap.find(".form-control-feedback").remove();
                    break;
                }
            });

        }else{
            defaults = {
                addWay:"append"
                ,msg:""
                ,status:1
                ,inputIcon:false
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
                var $helpBox =  $inputWrap.find('.help-inline').length > 0 ? $inputWrap.find('.help-inline') : $inputWrap.find('.help-block');
                var fa_html = "<span class=\"fa form-control-feedback\"></span>"
                var currentStyle = options.styleArray[parseInt(options.status)] ;

                $inputWrap.removeClass("has-success has-warning has-error has-feedback");
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
                    if($.trim(options.msg)!=""){
                        if(!length_msgBox) {
                            var msgBoxHtml = "<span class=\"J-feedback-msg feedback-msg\"></span>";
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
                        $msgbox.removeClass("feedback-msg-success feedback-msg-warning feedback-msg-error").addClass('feedback-msg-'+currentStyle.name);
                        callback.$msgbox = $msgbox; 
                        var icoHtml ="<i class=\"fa "+currentStyle.ico_o+"\"></i>";
                        $msgbox.html(icoHtml+" "+options.msg).show();
     
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
