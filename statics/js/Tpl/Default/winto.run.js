$(function(){
	$(".u-drop").jDropBtn();
	$(window).scroll(function(){navFix();})




	if($("#winto_nav").length>0){
		runNavDown()
		$(window).resize(function(){
			runNavDown()
		})
	}

	if($("#J-header").length>0){
		$("#J-header").dblclick(function(e){
			e.preventDefault();
			e.stopPropagation()
			goTop();
		})
	}
})


function navFix(){
	if($(this).scrollTop()>=155){
		$("#J-header").addClass("header_fix")	
	}else{
		$("#J-header").removeClass("header_fix")	
	}
}


function runNavDown(){
	var $navbar = $("#winto_nav").parent(".navbar");

	if(screen.width>758 && document.documentElement.clientWidth>758){
		$navbar.show();
		navSlideDown($("#winto_nav li"),".nCon")
		$("#winto_nav .nCon").hide();
	}else{
		$("#winto_nav li").unbind("mouseenter").unbind("mouseleave");
		$("#winto_nav .nCon").show();
		$navbar.hide();
		$("#J-menu-btn").removeClass('active');
	}
}


function showSearchbar(isClose){
	isClose = typeof(isClose)=='undefined' ? 0 : isClose;
	var $searchbar = $("#J-searchbar");
	var $btn = $("#J-searchbar-btn");
	var ie = getBrowserVersion();
	
	if( ($searchbar.is(":hidden") || $searchbar.is('.a-bounceoutR')) && !isClose){		
		if(ie!="" && ie < 10 && ie > 0){
			$searchbar.slideDown('fast')
		}else{
			$searchbar.show();
		}
		$searchbar.addClass('active a-bounceinR').removeClass('a-bounceoutR');
		$btn.addClass('active');
	}else{		
		$searchbar.removeClass('active a-bounceinR').addClass('a-bounceoutR');
		if(getBrowserVersion() < 10 && getBrowserVersion() > 0){			
			$searchbar.slideUp('fast');
		}
		$btn.removeClass('active');
	}	

	
}