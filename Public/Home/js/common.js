// JavaScript Document


 mui.init({
            swipeBack:true //启用右滑关闭功能
});
var slider = mui("#slider");
slider.slider({
    interval: 5000//自动轮播周期，若为0则不自动播放，默认为0；
});

var  mz = {} ;

//请求等待状态
function beforeFunc(){
	
}
//图片验证是否存在
function checkImage(img)
{
//	console.log(img);
	if(typeof(img) != 'undefined') { if(  img.length > 0) { return img; } else { return _public+'/Home/images/no_picture.png'; }
	} else { return _public+'/Home/images/no_picture.png'; }
}
 //途径生成替换

function replace_url(regexp,replacement,stringObject){
//	console.log(regexp) ;
//	console.log(replacement) ;
//	console.log(stringObject) ;
	return stringObject.replace(regexp,replacement) ;
	
}

$(function(){
	
	
	//底部导航页面跳转
	$('#nav a').on('click',function(){
		console.log('aa') ;
		var url = $(this).attr('href');
		location.href=url;
	})
	
	//文章分类选项页面跳转
	$('.atrileCategory a').on('click',function(){
		var url = $(this).attr('href');
		location.href=url;
	})
	
	if($('#index-nav li  a span').length > 0) 
		$('#index-nav li  a span').css({'height':$('#index-nav li  a span')[0].offsetWidth + 'px','line-height':$('#index-nav li  a span')[0].offsetWidth + 'px'}) ;
	
	
	$('.action-back').click(function(){
		console.log('back');
		history.go(-1);
	})
	
	/**************************************************************
	 * 会员上传头像
	 * START
	 */
	if($('#updateFile').length > 0){
		
		$('#updateFile .img').delegate('img','click',function (){
			console.log($(this));
			picNumber = $(this).attr('id');
			$('.base64Img').show();
			console.log(picNumber);
		})
		
	}
	
//	隐藏手机号码中间四位数
	function hidePhone(phone){
		var frist = phone.substr(0,3);
		var last = phone.substr(7,4);
		return frist+'****'+last ;
	}
		
		
	/**
	 * 使用 闭包 对象  重构 分页 异步 加载 
	 * 2017-11-06
	 * LHF 
	 * */
	
	mz.pageLoad = (function(){
		
		var page = sessionStorage.listPage != undefined ?  parseInt(sessionStorage.listPage) + 1  : 1;
		
		var  isLoading = false ; //判断是否加载中 
		//加载等待状态 
		var beforeFunc = function (){
  			if($('#loading').length >0 ){
  				$('#loading').show();
  			}else{
  				$('.mui-content').append('<div id="loading"></div>');
  				$('#loading').show();
  			} 
		}
		
		//会员认证进度
		var authenticationName = function (num){
			switch(num){
				case '-1': return '认证失败'; break ;
				case '0': return '未认证'; break ;
				case '1': return '审核中'; break ;
				case '2': return '已认证'; break ;
				default: return '未认证';
			}				
		}
		
//		判断分页是否加载完成
		var endList = function (p,cp){
			isLoading = false ;
			page = p;
			if(p > cp){
				$('.addMore').addClass('end');
				$('.addMore').html('加载完了')
			}
			$('#loading').hide();
		}
		
//		房源列表 
		var sponseListHouses = function (data){
			var info = '';
		
			endList(data.page , data.countPage); 
	
			if( data.list.length > 0 ){ 
				$.each(	data.list,function(k,v){
					if( v.picture.length > 0 )
						var img = v.picture[0].url;
					else
						var img = '';
					
					if(data.tag == 1){
						info += '<div class="mui-card">\
							<a href="'+replace_url('00',v.house_id,showUrl)+'">\
								<div class="mui-card-header mui-card-media" style="height:15rem;background-image:url('+checkImage(img)+')"></div>\
								<div class="mui-card-content">\
									<div class="mui-card-content-inner">\
										<p style="color: #5c5c5c; font-weight: 500;">'+v.name+'</p>\
										<p style="color:#EC971F;">均价：'+v.average_price+'元/㎡</p>\
										<p>建筑面积：'+v.area+'㎡</P>\
										<p>地址：'+v.address+'</p>\
										<p>交房时间：'+v.give_time+'</p>\
									</div>\
								</div>\
							</a>\
						</div>';
						
					}else{
						var isShow = '';
						if(window.act == 'release'){
							if(v.release == 1){
								isShow = '<span class="fr">已发布</span>';
							}else{
								isShow = '<span class="fr">信息审核中</span>';
							}
						}
					info += '<ul class="mui-table-view">\
						<li class="mui-table-view-cell mui-media">\
							<a href="'+replace_url('00',v.house_id,showUrl)+'">\
								<div style="margin-right: 1em; width: 35%; height:5rem; display:inline-block;">\
								<img class="mui-pull-left" style="max-width: 100%; max-height:5rem; margin:0 auto;" src="'+checkImage(img)+'" /></div>\
								<div class="mui-media-body" style="line-height: 30px;display:inline-block;">\
							       '+v.name+'\
									<p class="mui-ellipsis">'+v.layout_name+'  '+v.area+'㎡   '+v.floor_name+'</p>\
									<p class="mui-ellipsis">'+isShow+'<span class="yuanjiaojuxing erfan_color_hong">'+v.sell_price+'万</span>  '+v.average_price+'元／㎡</p>\
								</div>\
							</a>\
						</li>\
					</ul>';
				
				}
			
				})
				
				$('.contentlist').append(info);
				
				if(data.page <=2)
					sessionStorage[$('.contentlist').attr('data-type')] =  info ;
				else
					sessionStorage[$('.contentlist').attr('data-type')] +=  info ;

			}else{
				if(data.page <=2){
					info = '<p class="sorry">对不起，还没有发布房源</p>';
					$('.contentlist').html(info);
				}
			}
			
			
		}
		
		
//		资讯列表
		var sponseListAtrile = function (data){
			var info = '';
			
			endList(data.page , data.countPage);
	
			if( data.list.length > 0 ){
				$.each(	data.list,function(k,v){ 
					info +='<li class="mui-table-view-cell mui-media">';
					info +='<a class="fl" style="width:30%;margin: 0rem 1rem 0 0; padding:0 " href="'+replace_url( '00' , v.artile_id , showUrl )+'"><img class="mui-pull-left" style="max-width: 100%;" src="'+checkImage(v.image)+'" /></a>';
					info +='<div class="mui-media-body" style="line-height: 35px;">';
					info += '<a href="'+replace_url('00' , v.artile_id , showUrl)+'">'+v.name +'</a>';
					info +='<p >'+ v.brief +'</p>';
					info +='<p class="mui-ellipsis">'+v.read_number+'人阅读 ';
					if(v.is_ad == 1)
					info +='<a href="'+v.ad_url+'" target="_blank" class="mui-pull-right" >广告</a>'; 
					
					info +='</p>';
					info +='</div></li>';
				})
				
				$('.mui-table-view').append(info);
				setSessionStorage(data.page,info);
			
			}else{
				if(data.page <=2){
					$('.mui-table-view').html('<p class="sorry">对不起，管理员比较懒，还没有发布文章呢</p>');
				}
			}
		}
		
		
		//预约列表
		var sponseListBespoke = function (data){
			var info = '';

			endList(data.page , data.countPage);
			
			if( data.list.length > 0 ){
				
				$.each(	data.list,function(k,v){
					var agree = '';
					switch(v.agree ){
						case '-1': agree = '预约失败' ;break;						
						case '0': agree = '已提交';break;
						case '1': agree = '预约成功';break;
						case '2': agree = '交订';break;
						case '3': agree = '付首付';break;
						case '4': agree = '按揭中';break;
						case '5': agree = '放款';break;
						default: agree = '已提交'; 
					}
					var hidphone = hidePhone(v.telephone);
					info += '<div class="list  mui-clearfix">\
						<ul class="mui-clearfix">\
							<li class="title">'+ v.build_name + '</li>\
							<li class="name">'+v.name+'</li>\
							<li><span>电话</span>'+hidphone+'</li>\
							<li class="name">'+v.address+'</li>\
							<li><span>随行</span>'+v.number+'人</li>\
							<li><span>时间</span>'+v.see_time+'</li>\
						</ul>\
						<p><span class="adviser'+v.agree+'">'+agree+'</span><a href="'+replace_url('00',v.ub_id,data.setUrl)+'" class="set">详情</a></p>\
					</div>';
			
				})
				$('.contentlist').append(info);
//				setSessionStorage(data.page,info);
			}else{
				if(data.page <=2){
					$('.contentlist').html('<p class="sorry">对不起，还没有添加预约看房信息呢</p>');
				}
			}
		}
		
		//会员子会员列表
		var sponseListChildren = function (data) {
			
			endList(data.page , data.countPage);
			
			if( data.list.length > 0 ){
				var info = '';
				$.each(	data.list,function(k,v){
					info += '<dl class="mui-clearfix">\
						<dt><img src="'+v.face_url+'" /></dt>\
						<dd>\
							<h4>'+v.name+'</h4>\
							<p>认证进度：' + authenticationName(v.authentication) + '</p>\
							<p> <a href="'+replace_url('00',v.user_id,certificateUrl)+'"> 工作证 </a> <a href="'+replace_url('00',v.user_id,bespokeUrl)+'"> 预约信息 </a></p>\
						</dd>\
					</dl>';
				});
				
				$('.contentlist').append(info);	
				
				setSessionStorage(data.page,info);
				
			}else{
				if(data.page <=2){
					$('.contentlist').append('<p class="sorry">还没推荐有子会员</p>');	
				}
			}		
		}
		
		var setSessionStorage = function (p,info){
			if(p <=2)
				sessionStorage[$('.contentlist').attr('data-type')+ $('.contentlist').attr('data-prefix')] =  info ;
			else
				sessionStorage[$('.contentlist').attr('data-type')+ $('.contentlist').attr('data-prefix')] +=  info;
		}
		
		//执行分页请求
		return function (model){
			
			//判断是否在加载中 
			if(isLoading == true ){
				return false;
			}
			isLoading = true ;
			
			
			if(model == undefined) model  = $('.contentlist').attr('data-type') ;
			
			// 拼接字符串 回调处理函数  
			var sponse = eval('sponseList'+model ) ;  
			
			// 筛选 搜索 分页从第一页重新开始 
			page = arguments[1] != undefined ?  arguments[1] :page ;
//			if(arguments[1] != undefined) page = arguments[1] ; 
			
			sessionStorage.listPage = page ;
			
			//传参 
			var data = {page:page,keyword:keyword} ;
			
			if(window.userID != undefined) data.userID = userID ;
			if(window.catid != undefined) data.catid = catid ;
			if(window.order != undefined) data.order = order ;
			if(window.sort != undefined) data.sort = sort ;
			if(window.tag != undefined) data.tag = tag ;
			if(window.fitter != undefined) data.fitter = fitter ;

//			console.log(sponse) ;
			
			ajaxobj(ajaxListPage,data,sponse,beforeFunc,'post',true);
		}
		
	})();
	
	//执行第一次调用 
	mz.dataType = $('.contentlist').attr('data-type') ;
	if($('.addMore').length > 0){
		
		if(mz.dataType == 'Bespoke'){
			mz.listInfo = sessionStorage[mz.dataType + $('.contentlist').attr('data-prefix')] = undefined; 
			sessionStorage.listPage = 1 ;
		}
		else
			mz.listInfo = sessionStorage[mz.dataType + $('.contentlist').attr('data-prefix')]; 
	
	  	if( mz.listInfo != undefined ){
	  		$('.contentlist').html(mz.listInfo); 
	  	}else{
	  	
	  		if(window.action != undefined){	  		
	  			mz.pageLoad(mz.dataType,1);
	  		}else{
	  			mz.pageLoad(mz.dataType,sessionStorage.listPage);
	  		}
	  	}
	}  
	
	//滚动到底部自动加载
	 $(window).scroll(function () {
        var scrollTop = $(this).scrollTop();
        var scrollHeight = $(document).height();
        var windowHeight = $(this).height();
        if (scrollTop + windowHeight == scrollHeight) {
			if( !$(this).hasClass('end') ) {
				mz.pageLoad(mz.dataType);
			}
      　
        }
    });
    
    
    
	//加载更多 
	$('.addMore').bind('click',function(){
		if( !$(this).hasClass('end') ) mz.pageLoad(mz.dataType);
	});
	
	//删除房源图片
	$('#picture').delegate('em','click',function(){
		if(confirm('你确定要删除这张图片吗')){
			if( $(this).prev().attr('name') == 'picture_id[]' ){
				var url = $(this).prev().prev().val() ;
				var id = $(this).prev().val() ;
			}else{
				var id = null ;
				var url = $(this).prev().val() ;
			}
			ajaxobj(imagesDelete,{id:id,url:url},responseImagesDelete) ;
			console.log(url,id) ;
			$(this).parent().remove();
		}
	})
	var responseImagesDelete = function (data){}
 
//	点击 收搜 
	$('#search').blur(function(){
		keyword = $(this).val();
		page = 1;
		$('.mui-table-view').html('');
		$('.addMore').removeClass('end').html('加载更多');
		mz.pageLoad(mz.dataType,1);
	})
		
//	排序 筛选
	$('#segmentedControl a').on('click',function(){

		$('#segmentedControl a').removeClass('mui-active');
		$(this).addClass('mui-active');
			
		if( $(this).attr('data-name')  != undefined ){
			
			order = $(this).attr('data-name') ; 
			
			if ($(this).find('span').hasClass('mui-icon-arrowdown') )
			{
				sort = 'desc';
				$(this).find('span').removeClass('mui-icon-arrowdown');
				$(this).find('span').addClass('mui-icon-arrowup');
			}else{
				sort = 'asc';
				$(this).find('span').addClass('mui-icon-arrowdown');
				$(this).find('span').removeClass('mui-icon-arrowup');
			} 
			
			$('.mui-table-view').html('');
			$('.addMore').html('加载更多').removeClass('end');
			mz.pageLoad(mz.dataType,1);
			
		}else{
			//显示筛选列表
			if ($(this).find('span').hasClass('mui-icon-arrowdown') )
			{
				$(this).find('span').removeClass('mui-icon-arrowdown');
				$(this).find('span').addClass('mui-icon-arrowup');
				$('.fitter').show();
			}else{ 
				$(this).find('span').addClass('mui-icon-arrowdown');
				$(this).find('span').removeClass('mui-icon-arrowup');
				$('.fitter').hide();
			} 
		}
	})
		
//	提交筛选信息
	$('#fitterBtn').on('click',function(){
		console.log('fitterBttn');
		page = 1;
		$('.mui-table-view').html('');
		$('#loading').removeClass('end').html('加载更多');
		$('.fitter').hide();
		$('#segmentedControl a').last().find('span').removeClass('mui-icon-arrowup');
		$('#segmentedControl a').last().find('span').addClass('mui-icon-arrowdown');
		
		var f = '{';

		$('#fitter-form input').each(function(k,v){
			if(v.name != '' && v.name != '__hash__') { f += '"'+[v.name]+'":"'+ v.value +'",'; }
		})
		
		$('#fitter-form select').each(function(k,v){
			if(v.name != '' && v.name != '__hash__') { f += '"'+[v.name]+'":"'+ v.value +'",'; }
		})
		
		fitter=f.substring(0,f.length-1) + '}';
		
		mz.pageLoad(mz.dataType,1);;
	})
	
	/**
	 * 会员发布二手房 
	 * 单价计算 
	 */
	
	$('#average_price').focus(function(){
		console.log('单价计算') ;
		var sell_price =  parseFloat($('#sell_price').val() ) * 10000;
		var area = parseInt( sell_price / parseFloat($('#area').val() ) );
		$(this).val(area ) ;
	})
	
	/**
	 * 添加属性  
	 * 前段会员中心 发布信息
	 */
	$('.important .add').on('click',function(){
		var table = $(this).attr('data-type');
		console.log('111');
		$(this).parent().parent().children().last().remove() ;
		
		$(this).parent().parent().append(
			'<li class="mui-input-row ">\
				<input type="text" name="'+table+'[val][]" value="" class="mui-input-clear" placeholder="请输入标签">\
				<input type="hidden" name="'+table+'[house_id][]"  value="'+house_id+'" />\
            	<input type="hidden" name="'+table+'[tag][]"  value="'+tag+'" />\
				<font  class="del">-</font>\
			</li>\
			</volist>\
			<li></li>'
		);
		
	})
	
	/**
	 * 删除属性  
	 * 前段会员中心 发布信息
	 */
	$('.important').delegate('.del','click',function(){
		var id = $(this).attr('data-id');
		var table = $(this).attr('data-type');
		console.log(id);
		$(this).parent().remove();
		if(undefined != id){
			ajaxobj(deleteAttr,{id:id,table:table},responceDeleteAttr);
		}
	})
	
	function responceDeleteAttr(data){}
	
	
	/**
	 * 添加在售户型 
	 * 前段会员中心 发布信息
	 */
	mz.housetype = (function(){
		
		var housetypeNum = 0;
		
		return function(Obj){
			 
			console.log('housetypeNum',housetypeNum) ;
			console.log('this',Obj) ;
			
			Obj.parent().parent().children().last().remove() ;
		
			Obj.parent().parent().append(
				'<li class="mui-input-row ">\
							<label class="input">\
								<img id="housetype' + housetypeNum + '" src="'+ _public +'/Home/images/add.png" >\
	            				<input type="hidden" name="housetype[url][]" class="pichidden"  value="" />\
							</label>\
							<font   data-type="housetype"  class="del">-</font>\
							<input type="hidden" name="housetype[build_id][]"  value="'+house_id+'" />\
			            	<input type="text" name="housetype[type][]"  value="" />\
			            	<input type="text" name="housetype[area][]"  value="" />户型：<br/>面积：\
						</li>\
				<li></li>'
			);
			
			++housetypeNum ;
		}

	})();

	$('.housetype .add').on('click',function(){mz.housetype( $(this) ) }) //base64Img ;
	
	/**
	 * 删除在售户型
	 * 前段会员中心 发布信息
	 */
	$('.housetype').delegate('.del','click',function(){
		var id = $(this).attr('data-id');
		var table = $(this).attr('data-type');
		console.log(id);
		$(this).parent().remove();
		if(undefined != id){
			ajaxobj(deleteAttr,{id:id,table:table},responceDeleteAttr);
		}
	})


	//表单上传之间字段过滤检查
	if($('#updateFile').length > 0){
		$('#updateFile').get(0).addEventListener('submit',function(ev){
			var sub = true ;
			$('#updateFile input').each(function(k,v){ 
				if( $(v).attr("type")  == "text" || $(v).attr("type")  == "number" ) {
					if($(v).attr("placeholder").indexOf('请输入') >= 0  ){
						
						if($(v).val() == ''){ 
							webToast( $(v).attr("placeholder").replace('请输入','') + '不能为空 ' , 'middle' , 2000) ;
							sub = false;
							return false;
						} 
						console.log('k:'+k,'v:'+v);
						console.log(this);
					}
				}
			}); 
			if(sub == false){
				ev.preventDefault();
			} 
		},false); 
	} 
	
	
	//报备电话
	if($('#mask').length > 0){
		$('#defaultTab').click(function(){
			$('#mask').show();
		})
		$('#mask').click(function(){
			$('#mask').hide();
		})
	}
})