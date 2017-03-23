//计算当前型号个数，并返回数值填回表单
function countModel(){
	var num = $("#pro_1_str").find("tr").length ;
	$("#pro_1").val(num)
}
//设置个数
function setLenModel(url){
	var num_s = $("#pro_1").val();
	
		for(i=0;i<num_s;i++){
			addModel(url)	
		}
	
	
}
//添加一个型号
function addModel(url){

	var num = 0;
	$("#pro_1_str tr").each(function() {
		d_num = parseInt($(this).find(".model_num").val())
		num = d_num > num ? d_num : num ;
    });
	num = num+1
	
	add_html = '<tr class="dl_'+num+'">'
	add_html +='    <td>'
	add_html +='      <input type="hidden" name="model_num" class="model_num" value="'+num+'"/>'
	add_html +='      <input class="ProductModel form-control input-sm " id="Model_'+num+'" size="16" value="" name="ProductModel[]" type="text"> '
	add_html +='    </td>'
	add_html +='    <td>'
	add_html +='      <input class="sizeInput form-control input-sm " id="Sizes_'+num+'" size="5" value=""  name="ProductSizes[]"  type="text" onfocus="gg_list(this)">'
	add_html +='    </td>'
	add_html +='    <td>'
	add_html +='      <input class="form-control input-sm " id="SizeSmallpic_'+num+'" size="8" value=""  type="text" name="SizeSmallpic[]"  ondblclick="showpic($(\'#SizeSmallpic_'+num+'\').val(),1);">'
	add_html +='    </td>'
	add_html +='    <td>'
	add_html +='      <input class="form-control input-sm " id="SizeBigpic_'+num+'" size="10" value=""  type="text" name="SizeBigpic[]"  ondblclick="showpic($(\'#SizeBigpic_'+num+'\').val(),1);">'
	add_html +='    </td>'
	add_html +='    <td>'
	add_html +='      <a class="btn btn-sm btn-default" onclick="aj_searchImg($(\'#Model_'+num+'\').val(),'+num+',\''+url+'\');" >取图</a>'
	add_html +='      <a class="btn btn-sm btn-default" onclick="copyPic(\'SizeBigpic_'+num+'\',\'SizeSmallpic_'+num+'\');" ><i class="fa fa-copy"></i></a>'
	add_html +='      <a class="btn btn-sm btn-default" onclick="delModel('+num+');" ><i class="fa fa-times"></i></a>'
	add_html +='    </td>'
  	add_html +='</tr>'

	$("#pro_1_str").append(add_html);
	//countModel()
}

//删除一个型号
function delModel(num){
	$("#pro_1_str").find(".dl_"+num).remove();
	//countModel()
}

//复制到产品默认图
function copyPic(b,s){
	$("#BigPic").val($("#"+b).val());
	$("#SmallPic").val($("#"+s).val());
}

//查找图片文件
function aj_searchImg(keyword,num,url){

	var smode = $.trim($("input[name='smode']:checked").val());
	$.ajax({
		type:"get",
		dataType:"json",
		url:url,
		cache:false,
		data:{"keyword":keyword,"smode":smode},
		success:function(json){
			if(json.status==0){
				alert(json.info);
			}else{
				$("#SizeSmallpic_"+num).val(json.smallPic);
				$("#SizeBigpic_"+num).val(json.bigPic);
			}
		}
	})	
}


function showpic(pUrl,siteUrl){
	siteUrl = siteUrl || '';
	if(typeof(GV.VAR['webDomain'])!="undefined" && siteUrl===1 ){
		siteUrl = GV.VAR['webDomain'] ;
	}
	if($.trim(pUrl)==""){
		alert("no pic");
		return false;
	}
	var picUrl = siteUrl+$.trim(pUrl).replace("../","/");;
	var windowBoxHtml = "<div id=\"f_shoppicbox\" class=\"m-showpicbox\" >"
	windowBoxHtml += "<a class=\"close\">&times;</a><div class=\"con\"></div>"
	windowBoxHtml += "</diV>"
	var imgHtml = "<img src=\""+picUrl+"\" width=\"100%\"/>"
	if(!$("#f_shoppicbox").length>0){
		//window.open (pUrl, 'newwindow', 'height=500, width=300, top=0, left=0, toolbar=no, menubar=no, scrollbars=no, resizable=no,location=n o, status=no')
		$("body").append(windowBoxHtml);
		
	}
	$("#f_shoppicbox").show().find('.con').html(imgHtml);
	$("#f_shoppicbox").find('.close').click(function(){
		$("#f_shoppicbox").hide();
	})
	
}



function gg_list(obj){
  var $this=$(obj);
  var sizeArray = [
  		"1200X600",'900X450','800X400','660X240','600X330','600X300',
  "-",'1000X1000','900X900','800X800','600X600','330X330','300X300',
  "-",'900X600','300X150',"800X80","600X80","400X80","300X80",
  "-","660X70","600X70","450X70","400X70","300X70",
  '-','600X50',"300X50","300X25","300X20"];  
  var boxoffset =$this.offset(); 

  var add_html = "";
      add_html += "<div class=\"gg_list\" >";
      add_html += "<ul>";
  for(i=0;i<sizeArray.length;i++){
    if(sizeArray[i]=="-"){
      add_html += "</ul>";
      add_html += "<ul class=\"bl\">";
    }else{
      add_html += " <li><a href=\"javascript:void(0);\">"+sizeArray[i]+"</a></li>";
    }      
  }
  add_html += "</ul></div>";
  $("body").append(add_html);
  $gg = $(".gg_list");
  var left = boxoffset.left+$this.width()+30;
  var top = boxoffset.top-(($gg.height()-$this.height())/2);  
  $gg.css({"left":left,"top":top}).click(function(event){  
      event.stopPropagation()
  })
  $this.click(function(event){      
      event.stopPropagation()
   })   
   $gg.find("li").children("a").each(function(){
      $(this).click(function(){
        var size = $(this).text();
        $this.val(size);
        $gg.remove();
      })
    });
    $("body").click(function(){
       $gg.remove()
    })
}

function copyToMeta(str,lan){
	if(lan=='en'||lan=='EN'){
		$("#SeoKeywordsEN").val(str)
		$("#SeoDescriptionEN").val(str)	
	}else{
		$("#SeoDescriptionCH").val(str)
		$("#SeoKeywordsCH").val(str)
	}
	
	
}



function changeName(obj,options){
   defaults = {
      name:'txt'
      ,data:{}
  }
  var options = $.extend(defaults,options);

  var $this = $(obj);
  if(typeof(options.url)=="undefined"){
    return false;
  }
  if($this.find('input').length>0){
    return false;
  }
  var postData = {};
  if(typeof(options.id)!="undefined"){
    postData.id = options.id;
  }
     

  var $txt = $this.find('.txt');
  var txt_o = $.trim($txt.text());
  var inputHtml = '<input class="form-control input-sm"  value="'+txt_o+'" >'
  var $input = $(inputHtml);
  $txt.hide();
  $input.appendTo($this).focus();
  $input.keydown(function(event){ 
    var e = event || window.event; 
    var k = e.keyCode || e.which; 
    if(k==27){
      $input.remove();
      $txt.show();
      return false;
    }
    if(k==13){
      var txt_n = $.trim($input.val());
      if(txt_n==txt_o){
        $input.remove();
        $txt.show();
        return false;
      }
      postData.txt = txt_n;
      postData.name = options.name;
      postData = $.extend(options.data,postData);
      $input.prop('disabled',true);
      postAjax(options.url,postData,function(json){
        if(json.status){
          $.jNotify.add({msg:json.info,type:"success"})
          $txt.html(txt_n);
          $input.remove();
          $txt.show();  
        }else{
          $input.prop('disabled',false);
          $.jNotify.add({msg:json.info,type:"error"})
        }
      })
      return false
    }
/*
    console.log(k); 
    console.log($input.val()); */

  }); 

}