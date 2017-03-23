function selectSort(obj,options){

  	defaults={
		 modalTarget:"choseSort" 
		,title:'选择栏目'
		,resultTarget:[
        	{target:".J-input-SortID",field:"ID",separator:','},
        	{target:".J-input-SortNameCH",field:"SortNameCH",separator:','},
        	{target:".J-input-SortPath",field:"SortPath",separator:','}
      	]
		,width: 600 
		,height: 420
		
		,url:'/index.php?g=Web&m=Productsort&a=public_selectsort&loadsuccess=load_sortlist_success'
		,alowSelectRoot:false
		,multi:false
		,param:{}

	}
	var options = $.extend(defaults,options)
    var $btn = $(obj)
    var defaultID_val = $btn.closest('div').find(".J-input-sortid").val();

    var title = $btn.attr('data-title') ? $btn.attr('data-title') :options.title;
    var width = $btn.attr('data-width') ? $btn.attr('data-width') :options.width;
    var url = $btn.attr('data-url') ? $btn.attr('data-url') :options.url;
    var treeTab = options.modalTarget+"_treeTab"
    var modal_content_html = '<div id="modal-selectuser-content" style="min-height:250px">';
    	modal_content_html += '<table id=\"'+treeTab+'\" ></table>';
     	modal_content_html += '</div>';

    GV.MODAL['selectsort'] = $.scojs_modal({
    	title:title
      //,remote: url
    	,cache:true
    	,btns:[]
     	,width:width+30      	
      	,target:options.modalTarget
      	,content:modal_content_html
      	

    })
    

    //console.log(ml+","+$("#modal_selectsort").index())
    //$("#modal_selectsort").css({'z-index':})
    GV.MODAL['selectsort'].show();  
    $("#"+treeTab).treegrid({
		//animate:true,
		collapsible:true,
		idField:'ID',
		treeField: 'SortNameCH',
		dnd:true,
		fitColumns: true,
		url: url,	
		height: options.height,
		width: width,
		columns:[[
			{field:'ID',title:'ID',width:60},
			{field:'SortNameCH',title:'分类名称',width:180},
			{field:'SortNameEN',title:'name (en)',width:100},
			{field:'ViewFlagCH',title:'中',width:30,formatter:selectSort_formatViewFlagCH},
			{field:'ViewFlagEN',title:'英',width:30,formatter:selectSort_formatViewFlagEN},
			{field:'orders',title:'序',width:30},
			{field:'linktype',title:'链',width:62,formatter:selectSort_formatLinkType},
			{field:'SortPath',title:'序',width:50},
			
		]],
			
		onClickRow:function(row){
			
			if(options.alowSelectRoot || row.ID!=0){
				fillSelected(row,{'resultTarget':options.resultTarget,'resultTargetWrap':$(obj).closest('div')})
			}
			
			if(!options.multi){
				GV.MODAL['selectsort'].close();
			}
		},
	
		onLoadSuccess:function(){
			var idTarget =  options.resultTarget[0].target;
			var defaultID_val = $(obj).closest('div').find(idTarget).val();
			defaultID_val = $.trim(defaultID_val) =="" ? 0 :defaultID_val;
			$("#"+treeTab).treegrid('select',defaultID_val);
		}
	})

         
    var $us_modal =  $(GV.MODAL['selectsort'].options.target);
    var $m_last = $('.modal:visible:last');
    if($m_last.prop('id')!=$us_modal.prop('id')){
      var zi = $m_last.css('z-index');
      $us_modal.css({'z-index':zi+1})
    }
}



function selectSort_formatViewFlagCH(value,row){
	var ViewFlagCH 
	if(typeof(row.ViewFlagCH)!="undefined"){
		ViewFlagCH = row.ViewFlagCH
	}
	ViewFlagCH = ViewFlagCH=="1" ? "<span style='color:green'>√</span>" : "<span style='color:red'>×</span>";
	return ViewFlagCH ;	
}

function selectSort_formatViewFlagEN(value,row){
	var ViewFlagEN 
	if(typeof(row.ViewFlagEN)!="undefined"){
		ViewFlagEN = row.ViewFlagEN
	}
	ViewFlagEN = ViewFlagEN=="1" ? "<span style='color:green'>√</span>" : "<span style='color:red'>×</span>";
	return ViewFlagEN ;	
}	

function selectSort_formatLinkType(value,row){
	s = value;
	if (value==2){
	    s="<font color=\"blue\">外部</font>";
	  }else if (value==3){
	    s="<font color=\"#a66\">聚合</font>";
	  }else{
	    s="<font color=\"green\">内部</font>";
	  }
	  return s ;
}	
