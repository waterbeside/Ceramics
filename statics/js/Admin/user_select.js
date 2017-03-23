


  function selectUser(obj){
    var $btn = $(obj)
    var defaultID_val = $btn.closest('div').find(".J-input-userid").val();
    var title = $btn.attr('data-title') ? $btn.attr('data-title') :'选择用户';
    var width = $btn.attr('data-width') ? $btn.attr('data-width') :'600';
    var url = $btn.attr('data-url') ? $btn.attr('data-url') :'/index.php?g=User&m=Management&a=public_selectuser&multiple=1&loadsuccess=load_userlist_success';
    GV.MODAL['selectuser'] = $.scojs_modal({
      title:title
      ,remote: url
      ,cache:true
      ,btns:[{id:"close",icon:"fa fa-check",btnClass:"btn btn-primary",title:"完成"}]
      ,width:width
      ,data:{'param':'"'+defaultID_val+'"'}
      ,target:'#modal_selectuser'
      ,onLoadSuccess:function(){
           $('#user_select_list').children('tbody').on('click',"tr",function(e){
              $(this).removeClass('J-datagrid-row-selected');
              var isJoin  = 1 ;
              if($(this).hasClass('J-multiple-row-selected')){
                $(this).removeClass('J-multiple-row-selected');
                var isJoin = -1 ;
              }else{
                $(this).addClass('J-multiple-row-selected');
              }
                
              var userid = $(this).find('td[field=id]').text();
              var username = $(this).find('td[field=username]').text();
              var realname = $(this).find('td[field=realname]').text();
              var data = {'userid':userid,'username':username,'realname':realname}
              fillSelected(data,{
                isJoin:isJoin
                ,resultTargetWrap:$btn.closest('td')
                ,resultTarget:{".J-input-userid":"userid",".J-input-username":"username",".J-input-realname":"realname"}
                ,fn:function(datas){
                    fillTheUserBox($btn.closest('td'),data)
                }
              })
           })
      }
    })
    
    
    

    //console.log(ml+","+$("#modal_selectuser").index())
    //$("#modal_selectuser").css({'z-index':})
    GV.MODAL['selectuser'].show();  
    var $us_modal =  $(GV.MODAL['selectuser'].options.target);
    var $m_last = $('.modal:visible:last');
    if($m_last.prop('id')!=$us_modal.prop('id')){
      var zi = $m_last.css('z-index');
      $us_modal.css({'z-index':zi+1})
    }
  }


  function load_userlist_success(idValue){

     hasSelected($('#user_select_list'),idValue)
  }


  function fillTheUserBox(targetWrap,data){
    var $targetWrap    = targetWrap
   
    var userid_array   = $targetWrap.find('.J-input-userid').val().split(',');
    var username_array   = $targetWrap.find('.J-input-username').val().split(',');
    var realname_array   = $targetWrap.find('.J-input-realname').val().split(',');
    
    var str  = "";
    
    $.each(userid_array,function(i,item){
      if($.trim(item)!=""){
        str += '<li data-userid="'+item+'" data-realname="'+realname_array[i]+'" data-username="'+username_array[i]+'" data-ur="user" class="type-user">';
        str += '  '+item+'：'+realname_array[i]+'<span class=\"small text-muted\"> ('+username_array[i]+')</span>'+'<a onclick="removeUserBoxItem(this)" class="close" title="移去">×</a>';
        str += '</li>';
      }
    })
    $targetWrap.find('.user_selected_box li').remove();
    $targetWrap.find('.user_selected_box').prepend(str);
  }

  function removeUserBoxItem(obj){

    var $this = $(obj)
    var $li   = $this.closest('li');
    var type  = $.trim($li.data('ur'));
    
    var $targetWrap = $this.closest('td');
    
      var userid = $li.data('userid');
      var username = $.trim($li.data('username'));
      var realname = $.trim($li.data('realname'));
      var data = {'userid':userid,'username':username,'realname':realname}
      var resultTarget = {".J-input-userid":"userid",".J-input-username":"username",".J-input-realname":"realname"}
    
    fillSelected(data,{
      isJoin:-1
      ,resultTargetWrap:$targetWrap
      ,resultTarget:resultTarget
      ,fn:function(datas){
        fillTheUserBox($targetWrap)
      }
    })
  }




  function hasSelected(targetDatagrid,data){
    var array = data.split(',');
    var $targetDatagrid = $(targetDatagrid);
    $.each(array,function(i,item){
      if(item!=""||item!=0){
       $targetDatagrid.find("tr[data-id='"+item+"']").addClass('J-multiple-row-selected'); 
      }
       
      
    })
  }

