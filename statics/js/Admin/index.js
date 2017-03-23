//常用菜单缓存
var menu_cache = {parent: {}, iframe: {}, link: {}};
menu_cache.parent[0] = true;
menu_cache.iframe[0] = true;



/**
 * 关闭历史导航
 */
function closeHistoryMenu(obj,mid) {
    if($(obj).parent().prev('li').length)
        $(obj).parent().prev('li').click();
    else
        $(obj).parent().next('li').click();
    $(obj).parent().remove();
    $("iframe[data-mid='" + mid + "']").remove();
    menu_cache.iframe[mid] = false;
    return false;
}
/**
 * 历史导航菜单点击
 */
function historyMenu(obj, url,mid ,reload) {
    reload = typeof(reload)=="undefined" ? 0 : 1;
    
    //隐藏所有iframe
    $("#J-mainshow iframe").hide();
    if (menu_cache.iframe[mid]) {
        var frm = $("iframe[data-mid='" + mid + "']");
        frm.show();
        if(reload){
            $("iframe[data-mid='" + mid + "']").attr('src', url);
        }
        //$('#J-menu a').removeClass('active');
        //$("#J-menu a[data-mid='" + mid + "']").addClass('active');
        
    } else {
        var url_connet_sign = url.indexOf('?') >0 ? "&" : "?" ;
        var html = '<iframe data-mid="' + mid + '" src="' + url + url_connet_sign + "_=" + Math.random() + '" scrolling="auto" frameborder="0" style="height: 100%;width: 100%;"></iframe>';
        $("#J-mainshow").append(html);
        //压入缓存
        menu_cache.iframe[mid] = true;

    }

    $("#J-menu a").removeClass('active');
    $(obj).addClass('active');
    $('#J-tab li').removeClass('active');
    var $_li = $("#J-tab li[data-mid='" + mid + "']");
    $_li.addClass('active');
    if(mid){
        var midArray = mid.split("_");
        var menuid = midArray.length>1 ? midArray[1] : mid ;
        setCookie("menuid",menuid);    
    }

}



/**
 * 调用动作
 * @param obj a对象
 * @param url 链接
 * @returns {boolean}
 */
function runAction(obj, url, mid) {
    mid = mid || "";
    /**
     * 移除所有链接标签点击后的样式属性
     */
    $("#J-menu").find('a').removeClass('active');
    console.log($("#J-menu").find('a'))
    //为当前链接加上active类更改背景颜色
    $(obj).addClass('active');
    //设置iframe标签src属性，显示链接内容
    var url_connet_sign = url.indexOf('?') >0 ? "&" : "?" ;
    $("#J-ishow").attr('src',url + url_connet_sign + "_=" + Math.random());
    if(mid){
        var midArray = mid.split("_");
        var menuid = midArray.length>1 ? midArray[1] : mid ;
        setCookie("menuid",menuid);    
    }
   /* historyMenu(obj, url,mid,1)

    if ($("#J-tab li[data-mid='" + mid + "']").length) {

        $("#J-tab li[data-mid='" + mid + "']").trigger('click');

        var $_li = $("#J-tab li[data-mid='" + mid + "']");
        var $mover = $(".tab-bar-con-inner");
        var $wraper = $(".tab-bar-con ");
        var width_wrap = $wraper.width();
        var widht_ul = $("#J-tab").width();
        var widht_li = $_li.outerWidth();
        var left_active = $mover.offset().left - $_li.offset().left ;
        
        if($mover.position().left+$_li.position().left+widht_li > width_wrap){
             //console.log('x:'+ (width_wrap - $_li.position().left - widht_li))
            $mover.css({left: width_wrap - $_li.position().left - widht_li});

        }else if($_li.position().left+ $mover.offset().left < $wraper.offset().left ){
            //console.log('x:'+ (-($_li.position().left)))
            $mover.css({left: -($_li.position().left)});
        }

    } else {

        $("#J-tab a").removeClass('active');
        var a = "<li class='active' data-mid='" + mid + "'>"
            + $(obj).html() + "<a title=\"点击关闭标签\" href=\"javascript:void(0)\" class=\"j-close\"><i>×</i></a></li>";
        $(a).prependTo("#J-tab").click(function(){
            historyMenu(obj, url,mid)
        }).find(".j-close").click(function(event){
            event.stopPropagation();
            closeHistoryMenu(this,mid) 
        });
        $(".tab-bar-con-inner").css({'left':0})

    }*/

    return true;
}


$(function(){
    //$("#j-tab-home").trigger('click')
    //刷新
    $('#J-refresh').click(function (e) {
        e.preventDefault();
        e.stopPropagation();
        var mid = $("#J-tab  .active").attr('data-mid');
        //var iframe = $("iframe[data-mid='" + mid + "']");
        var iframe = $("#J-ishow")
        if (iframe[0].contentWindow) {
        
            reloadPage(iframe[0].contentWindow);
         }
    });
    //全屏/非全屏
    $('#J-fullScreen').click(function (e) {
        e.preventDefault();
        e.stopPropagation();
        var $show = $('#J-mainshow');
        var top = $show.offset().top;
        var left = $show.offset().left;
        if(top>0){
            $show.attr('data-otop',top).attr('data-oleft',left).css({top:0,left:0});  
            $(document.body).addClass('fullScreen');
            $(this).attr('title','退出全屏').find("i").removeClass("fa-arrows-alt").addClass("fa-compress");
        }else{
            var otop = $show.attr('data-otop');
            var oleft = $show.attr('data-oleft');
            $(document.body).removeClass('fullScreen');
            $show.css({top:otop+'px',left:oleft+'px'});
            $(this).attr('title','全屏模式').find("i").removeClass("fa-compress").addClass("fa-arrows-alt");
        }
    })
   //历史导航向左滚动
   var TIMER_tab_move = '';
    $("#J-move-left").hover(function () {
        var $_li =  $("#J-tab .active").parent().prev();
        var $mover = $(".tab-bar-con-inner");

        TIMER_tab_move = setInterval(function(){
            //console.log($mover.position().left)
            if($mover.position().left < 0){
                $mover.css({left:'+=' + 3});
            }
        },1)
        
    },function(){
        clearInterval(TIMER_tab_move);
    })
    $("#J-move-right").hover(function () {
        var $_li =  $("#J-tab .active").parent().prev();
        var $mover = $(".tab-bar-con-inner");
        var width_wrap = $(".tab-bar-con ").width();
        var widht_ul = $("#J-tab").width();
        TIMER_tab_move = setInterval(function(){
            //console.log($mover.position().left)
            if($mover.position().left+widht_ul > width_wrap){
                $mover.css({left:'-=' + 3});
            }
        },1)
        
    },function(){
        clearInterval(TIMER_tab_move);
    })

    //左侧菜单折叠
    $("#J-menu").on('click',"dt a",function(e){
        e.preventDefault();
        e.stopPropagation();
        var $dt = $(this).parent('dt');
        var $dd = $dt.next('dd')
        if($dd.is(':hidden')){
            $dd.show();
            $(this).find('i.fa').removeClass('fa-caret-right').addClass('fa-caret-down')
        }else{
            $dd.hide();
            $(this).find('i.fa').removeClass('fa-caret-down').addClass('fa-caret-right')
        }

    })

    $("#J-menu").on('click',"dd a",function(e){
        e.preventDefault();
        e.stopPropagation();
        var href = $(this).attr('href');
        var mid = $(this).attr('data-mid');
        runAction($(this)[0],href,mid);
    })

})