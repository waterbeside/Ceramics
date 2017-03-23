
  //刷新驗證碼
  function refreshs(){
    document.getElementById('code_img').src="/index.php?m=Api&c=Checkcode&a=index&width=110&height=34&mode=2&code_len=4&"+Math.random();void(0);
  }


  //验证是否已存在
  function checkUnique(obj,f,url){
    
    var checkVal = $("#"+f).val()
    if(f=='name' && !checkTheLen('name',0,'用户名不能为空',3,'可点右侧按钮验证该用户名是否已被他人注册',0)){
        return false;
    }
    if(f=='code' && !checkFormJxCode(0)){
        return false;
    }
    $(obj).button('loading');
    $.ajax({
      type:"post"
      ,dataType:"json"  
      ,data:{"val":checkVal,'f':f}
      ,url:url
      ,cache:false
      ,success:function(data){
        if(data.status==0){
          $("#"+f).inputFeedback({'status':0,'info':data.info}) 
        }else{
          $("#"+f).inputFeedback({'status':1,'info':data.info}) 
        }   
      }
    }).always(function(){
        $(obj).button('reset')
      });   
  }

  function checkFormJxCode(returnTrue,trueMsg){
          var msg = typeof(trueMsg)=="undefined"?"可点右侧按钮验证该号码是否已被他人注册":trueMsg
          var inputID="code"
          
          if(checkLen(inputID,7,"").status==0){
            $("#"+inputID).inputFeedback({'status':0,'info':'长度过短'});
              return returnTrue       
          }else if(isNaN($("#"+inputID).val())){
            $("#"+inputID).inputFeedback({'status':0,'info':'必须全为数字'});
              return returnTrue
          }else{
            $("#"+inputID).inputFeedback({'status':3,'info':msg});   
              return 1
          }   
  }

  //验证不能为空
  function checkTheLen(inputID,len,errorMsg,successType,successMsg,returnTrue){
      if(checkLen(inputID,len,"").status==0){
        $("#"+inputID).inputFeedback({'status':0,'info':errorMsg}) 
        return  returnTrue ;  
      }else{
        $("#"+inputID).inputFeedback({'status':successType,'info':successMsg})
        return  1 ;
      }   
  }

  function goToErrorInput(obj){
      $(obj).find('.J-btn-submit').button('reset')
      var $errorInput = $(obj).closest('form').find(".has-error").eq(0)
      var errorTop = $errorInput.offset().top-130
      if($('body').hasClass('modal-open') ){
        $(obj).closest('.modal').animate({scrollTop:errorTop},500);  
      }else{
        $("html,body").animate({scrollTop:errorTop},500);  
      }
      
      return false  
  }

  function checkFormPw(returnTrue){
    checkTheLen("password",6,"密码不能少于6位",1,"",returnTrue)
    if($("#pwdconfirm").val().length>0){
      checkFormRePw()
    }   
  }
  function checkFormRePw(returnTrue){
    var rePw = checkPw("password","pwdconfirm")
    if( rePw.status==0){
      $("#pwdconfirm").inputFeedback({'status':0,'info':rePw.info})
      return returnTrue   
    }else{
      $("#pwdconfirm").inputFeedback({'status':1,'info':'两次输入密码一致'})

      return  1 ; 
    }   
  }


//当输入框重新打字时，重置样式。
function resetInputStyle(inputID){
  $("#"+inputID).keyup(function(){
    if($("#"+inputID).parents(".has-feedback").length>0){
      $("#"+inputID).inputFeedback('reset')
    }   
  })
}