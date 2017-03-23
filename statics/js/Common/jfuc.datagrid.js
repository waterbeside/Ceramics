/*
 * jQuery treegrid Plugin 仿easyui的datagrid，不過功能少點，相對精簡，樣式使用bootstrap,圖標使用FA;
 * Copyright 2013, 紫蘇醬(Loch Ken) <454831746@qq.com>
 
 */
(function($) {
	var methods = {
		defaults : {
			idField:'id'
			,loadMsg:'加載中'
			,cache:false
			,height:'auto'
			,method:'get'
			,pageNumber:1
			,pageSize:10
			,pageList:[10,20]
			,columns:[[
				{field:'id',title:'ID',width:'8%'}
				,{field:'title',title:'名稱'}
			]]
		},

		
		init:function(params){
			var options = $.extend({}, methods.defaults, params);

			
			methods.target = this;
			return this.each(function () {
				//console.log(this.options)	
				//console.log(this.options.columns[0])
				var $this = $(this);
				$this.jDatagrid('setTreeContainer',$(this));
				$this.data('findCache',null);
				$this.jDatagrid('setSettings',options);
				var html_thead = $(this).jDatagrid('_domThead');
				$(this).html(html_thead);
				$(this).append($(this).jDatagrid('_domTbodyWrap'));
				if(options.url){
					$(this).jDatagrid('load',options.params);	
				}else if(options.datas){
					$(this).jDatagrid('loadData',options.datas);
				}
				
				
			})
		},

		load:function(params){
			var $this = $(this);
			var options = $(this).jDatagrid('getSetting');
			
			$.ajax({
				type: options.method,
				dataType: "json",
				cache:options.cache,
				async:false,
				data:params,
				url:options.url,
				beforeSend:function(){
					if(options.onBeforeLoad){
						options.onBeforeLoad(options);
					}
				},
				success:function(datas){
					$this.children('tbody').html('');
					$this.jDatagrid('loadData',datas);
					
				}
			})
		},

		reload:function(params){
			var $this = $(this);
			var options = $this.jDatagrid('getSetting');
			var defaults = {page:1};
			    defaults  = typeof(options.params) == "undefined" ? defaults : $.extend({}, options.params, defaults);
			var dataParams = $.extend({}, defaults, params);
			$(this).jDatagrid('load',dataParams);
		},

		loadData:function(datas){
			var $this = $(this);
			var options = $(this).jDatagrid('getSetting');
			$this.jDatagrid('setData',datas);
			var rows = datas.rows;
			$this.jDatagrid('_domTbody',{'datas':rows,'deep':0});
			var $tbody = $this.children('tbody');
			var $tr = $tbody.children('tr');
			$tr.each(function(){

				var $row = $(this);
				var itemId = $row.attr('data-id');
				
				
				$(this).click(function(e){
					
					//$(this).addClass('J-datagrid-row-selected').siblings('tr').removeClass('J-datagrid-row-selected');
					var thisid = $(this).attr('data-id');
					$this.jDatagrid('select',thisid);
					//console.log($this.jDatagrid('find',thisid));
					
					if(typeof(options.onClickRow)=="function"){
						options.onClickRow($this.jDatagrid('getSelectedData'));
					}
					
				});
			});
			if(options.onLoadSuccess){
				options.onLoadSuccess(datas);
			}
			//methods.response = {}
			//if(methods.options.callback){ methods.options.callback(this.response); }else{ return(methods.response); }
		},

		//取得设置
        getSetting: function(name) {
            if (!$(this).jDatagrid('getTreeContainer')) {
                return null;
            }
            if(typeof(name)=='undefined'){
            	return $(this).jDatagrid('getTreeContainer').data('settings');
            }else{
            	return $(this).jDatagrid('getTreeContainer').data('settings')[name];	
            }
            
        },
		//添加设置
        setSettings: function(settings) {
            $(this).jDatagrid('getTreeContainer').data('settings', settings);
        },
        //取得数据主体
        getTreeContainer: function() {
            return $(this).data('jDatagrid');
        },
        //设置数据主体
        setTreeContainer: function(container) {
            return $(this).data('jDatagrid', container);
        },


        //设置加载的树数据
        setData: function(datas) {
            $(this).jDatagrid('getTreeContainer').data('datas', datas); 
        },
        //取得所有树数据
        getData: function() {
            return $(this).jDatagrid('getTreeContainer').data('datas');
        },

		//设置选择的节点
        setSelected: function(obj) {
            $(this).jDatagrid('getTreeContainer').data('selected', obj); 
        },
        //取得节点
        getSelected: function() {
            return $(this).jDatagrid('getTreeContainer').data('selected');
        },


		//選擇節點
		select:function(id){
			var options = $(this).jDatagrid('getSetting');
			if(!id){
				return false;
			}
			var $row  = $(this).children('tbody').children('tr[data-id='+id+']');
			$row.addClass('J-datagrid-row-selected').siblings('tr').removeClass('J-datagrid-row-selected');
			var datas = $(this).jDatagrid('find',id);
			$(this).jDatagrid('setSelectedData',datas);
			//console.log($(this).jDatagrid('getSelectedData'));
			$(this).jDatagrid('setSelected',$row);
			if(typeof(options.onSelect)=="function"){
				options.onSelect($(this).jDatagrid('getSelectedData'));
			}
			return $row;
		},
		//取消選擇節點
		unselect:function(id){
			if(typeof(id)=="undefined"){
				$(this).children('tbody').children('tr').removeClass('J-datagrid-row-selected');	
			}else{
				$(this).children('tbody').children('tr[data-id='+id+']').removeClass('J-datagrid-row-selected');	
			}
			$(this).jDatagrid('setSelected',null);
		},
		
		//设置已选节点的数据
		setSelectedData:function(datas){
			$(this).jDatagrid('getTreeContainer').data('selectedDatas', datas); 
		},
		//取得已选节点的数据
		getSelectedData:function(){
			return $(this).jDatagrid('getTreeContainer').data('selectedDatas'); 	
		},


		//取節點對像数据
		find:function(id){
			var datas = $(this).jDatagrid('getData');
			return $(this).jDatagrid('_find',id,datas);
		},
		_find:function(selectid,datas,deep){
			var $this = $(this);
			var $row  = $(this).children('tbody').children('tr[data-id='+selectid+']');
			var deep = typeof(deep)=="undefined" ? 0 : deep ;
			$(datas).each(function(index,item){
				if(item[$this.jDatagrid('getSetting','idField')]==selectid){
					item.$node = $row;
					$this.data('findCache',item);
				}else{
					if(item.children && item.children.length>0){
						var deep_n = deep+1;
						$this.data('findCache',$this.jDatagrid('_find',selectid,item.children,deep_n));	
					}
				}
			})
			return $this.data('findCache');
		},

		_domThead:function(){

			var colsArray = $(this).jDatagrid('getSetting','columns');
			var cols = colsArray[0];
			var html_thead = '<thead><tr>';
			for(var i=0;i<cols.length;i++){
				html_thead += '<th '+(cols[i].width ? 'width='+cols[i].width :'')+'>'+cols[i].title+'</th>';
			}
			html_thead +="</tr></thead>" ;
			return html_thead;
		},
		_domTbodyWrap:function(){
			return '<tbody></tbody>';
		},
		_domTbody:function(param){
			var $this = $(this);
			var colsArray = $this.jDatagrid('getSetting','columns');
			var cols = colsArray[0];
			var datas = param.datas;
			var $tbody = $this.children('tbody');
			$(datas).each(function(index,item){
				//$this.o2a(item);
				var html_td = '<tr  data-id="'+item[$this.jDatagrid('getSetting','idField')]+'">'; 
				for(var i=0;i<cols.length;i++){
					var cellHtml = cols[i].formatter ? cols[i].formatter(item[cols[i].field],item) : item[cols[i].field];
					
						html_td += '<td field="'+cols[i].field+'"><div class="cellwrap"><span class="cell">'+cellHtml+'</span></div></td>';	
					
					
				}
				html_td +="</tr>";
				$tbody.append(html_td);
				
			})
		},
		o2a : function(o){
			var arr = [] ;
			for(var n in o){  
				arr[n] = o[n] ;
			}
			return arr;
		},

	}

	$.fn.jDatagrid = function (method) {

        // 方法调用
        if (methods[method]) {
            return methods[method].apply(this, Array.prototype.slice.call(arguments, 1));
        } else if (typeof method === 'object' || !method) {
            return methods.init.apply(this, arguments);
        } else {
            $.error('Method' + method + 'does not exist on jQuery.jDatagrid 。<br/> 方法'+method+'不存在於jQuery.jDatagrid也！');
        }

    };
    
})(jQuery);