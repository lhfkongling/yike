/**
 * 后台公共函数库
 * Author：LHF
 * date ：2017-03-31
 */
 var public = '/Public',
 	 yesgif = '/Public/Admin/images/yes.gif',
 	 nogif = '/Public/Admin/images/no.gif';
 var i=0 ; //设置全局变量 判断是否允许提交
 
$(function(){
	//=============================================================================
	//前端 右上导航切换
	if($('header .menu').length > 0){
		$('header .menu').click(function(){
			$('#menu').show();						 
		})
		$('.content').click(function(){
			$('#menu').hide();						 
		})
		console.log('menu');
	}
	//=============================================================================
	//前端 下拉选择
	$('.form ul li font').click(function(){
		$(this).next('dl').show();	
	})
	$('.form ul li dl dd').click(function(){
		$(this).siblings().removeClass('checked');
		$(this).addClass('checked');
		$(this).parent().prev('font').html($(this).html());
		$(this).parent().prev().prev().val($(this).attr('data-id'));
		$(this).parent().hide();
			
	})
	//=============================================================================
	 //排序
	 $('.filter li').click(function(){
		if($(this).attr('data-order')){
			$(this).siblings().removeClass('order');
			$(this).addClass('order');
			
			order =   $(this).attr('data-order');				
			getListInfo();
		}else{
			$('#topfilter').show();
			$('.topSourch').hide();
		}
	})
	 
	 //=============================================================================
	 //第一次加载
	 if($('.addMore').length > 0){
	 	getListInfo();
	 }
	 //加载更多
	 $('.addMore').click(function(){
		 if(!$(this).hasClass('end')){
		 	page ++ ;
		    getListInfo();
		 }
	});
	
	
	
	
	//=============================================================================
	//前端 多选
	
	$('.form ul li a').on("click",function(){
	 	if($(this).hasClass('seled') ){
			
			$(this).removeClass('seled');
			$(this).find('input').removeAttr('checked');
			if($(this).find('input').attr('name')== 'image' ) {
				$('#picture').hide();	
			}
			if($(this).find('input').attr('name')== 'video') {
				$('#video').hide();	
			}
		}else{
			$(this).addClass('seled');
			$(this).find('input').attr('checked','checked');
			if($(this).find('input').attr('name')== 'image' ) {
				$('#picture').show();	
			}
			if($(this).find('input').attr('name')== 'video') {
				$('#video').show();	
			}
		} ;
	   
	});
	
	$('#topfilter dl dd a').on("click",function(){
	 	if($(this).hasClass('seled') ){
			
			$(this).removeClass('seled');
			$(this).find('input').removeAttr('checked');
			if($(this).find('input').attr('name')== 'image' ) {
				$('#picture').hide();	
			}
			if($(this).find('input').attr('name')== 'video') {
				$('#video').hide();	
			}
		}else{
			$(this).addClass('seled');
			$(this).find('input').attr('checked','checked');
			if($(this).find('input').attr('name')== 'image' ) {
				$('#picture').show();	
			}
			if($(this).find('input').attr('name')== 'video') {
				$('#video').show();	
			}
		} ;
	   
	});
	
	//=============================================================================
	//前端 点赞
	
	$('.details font').on("click",function(){
		console.log('laud');
	 	ajaxobj(ajaxLaud,{table:table,fieldID:fieldID,id:id,val:$(this).html()},responceLaud);
	});
	function responceLaud(data){
		if(data.type == true){
			 $('.details font').removeClass('laud0'); 
			 $('.details font').addClass('laud1'); 
		}else{
			 $('.details font').addClass('laud0'); 
			 $('.details font').removeClass('laud1'); 
		}
		 $('.details font').html(data.val);
	}
	
	
	//=============================================================================
	// 手机输入文本框失去焦点的时候 验证 正则 
	// data-unique 属性值 标识 是否 唯一
	$('#telephone').blur(function (){
		
		var res = chkTelephone($(this).val()) ;
		
		if( res == true && $(this).attr('data-unique') == 1 )	
		{
			ajaxobj(ajaxChkFiled,{table:table,fieldID:fieldID,field:'telephone',id:id,val:$(this).val()},responceTelephone);
		}
	})
	function responceTelephone(data){
		if(data != null) {
			webToast("手机号码已存在，请重新输入。","middle",3000);
			$('#telephone').focus();
			return false;
		}else{
			return true;
		}	
	}
	//=============================================================================
	//所在地区联动查询
	if($('#province').length > 0){
		
		getRegion('province',1); //默认第一次请求省级列表
		if(province != '') getRegion('city',province);
		if(city != '') getRegion('county',city);
		
		$('#province').change(function(){getRegion('city',$(this).val());}) //省级选择请求市级列表
		$('#city').change(function(){getRegion('county',$(this).val());}) //市级选择请求县级列表
		
		$('#addressBtn').click(function(){
			console.log($('#province').find("option:selected").text());
			var text = $('#province').find("option:selected").text() + ' ' + $('#city').find("option:selected").text() +' '+ $('#county').find("option:selected").text() ;
			$('#addressInfo').html(text);
			$(this).parent().parent().hide();
		})
		
	}
	
	//地区联动请求函数
	function getRegion(type,parent_id){
		ajaxobj(ajaxRegionUrl,{type:type,parent_id:parent_id},responceRegion);	
	}
	//地区联动请求回调函数
	function responceRegion(data){
		//console.log(data.list.length);
		if(data.list.length > 0){
			var option = '<option value="">请选择</option>';
			var region = 0, selected = '' ;
			if(data.type == 'province')
			{
				region = province ;
				$( '#city' ).html(option);
				$( '#county' ).html(option);
			}
			else if(data.type == 'city')
			{
				region = city ;
				$( '#county' ).html(option);
			}
			else if(data.type == 'county')
			{
				region = county ;
			}
			$.each(data.list,function(k,v){
				if(region == v.region_id )
					selected = 'selected="selected"';
				else
					selected = '';
				
				option +=  '<option value="'+v.region_id+'" '+selected+'>'+v.region_name+'</option>';
			})			
			$( '#'+data.type ).html(option);
		}
	}
	
	//=============================================================================
	//图片上传 
	//图片点击事件
	$('#picture img').on('click',function(){
		console.log($("input[name='image']").attr('checked')) ;
		if($("input[name='image']").attr('checked') == 'checked' )	{			  
			$(this).next().click();	
		}
		else
		  webToast("请选择图片上传","middle",1000);
	})
	
	
	//视频上传
	$('#video font').on('click',function(){
		if($("input[name='video']").attr('checked') == 'checked' )			  
		$(this).next().click();	
		else
		  webToast("请选择视频上传","middle",1000);
	
	})
	
	//文件选择事件
	$("#upfile input[type='file']").change(function(){
			console.log($(this).attr('id'));
			ajaxFileUpload($(this).attr('id'),$(this).attr('data-type'));
	})
	//文件上传函数
	function ajaxFileUpload(id,type)
	{
		
		$("#loading").show();
		$.ajaxFileUpload
		(
			{
				url:ajaxUpFileUrl,
				secureuri:false,
				fileElementId:id,
				dataType: 'json',
				data:{type:type, id:id},
				success: function (data, status)
				{
					$("#loading").hide();
					if(typeof(data.error) != 'undefined')
					{
						if(data.error != '')
						{
							webToast(data.info,"middle",3000);
							//alert(data.info);
						}else
						{
							//上传成功
							if(data.type=='images'){
								$('#'+data.file).prev().attr('src',data.info) ;
								$('#'+data.file).next().val(data.info) ;
							}
							if(data.type=='videos'){
								$('#'+data.file).next().val(data.info) ;
								if($('#img_'+data.file).length > 0) $('#img_'+data.file).attr('src',okPNG);
							}
						}
					}
				},
				error: function (data, status, e)
				{
					$("#loading").hide();
					webToast(e,"middle",3000);
				
				}
			}
		)
		
		return false;

	}
	
	//删除信息 
	$('.deleteInfo').on('click',function deleteInfo()
	{
		if(confirm('你确定要删除这条信息吗')){
			var id = $(this).attr('data-id');
			ajaxobj(delInfo,{id:id},responceDelInfo);	
		}
	} )	
	function responceDelInfo(data){
		if(data == 1){
			location.reload();
		}else{
			webToast( "删除失败","middle",3000);
		}
	}
	
	//测试弹窗使用
	selectPay();

})
/**************************************************************************************************************/


/**
* Checkbox 检查 至少选择一个
*
*/

function chkCheckbox(obj,title){
	var chk = false;
	var ids = '';
	var sp = '';
	
	//循环遍历
	obj.each(function (){
		//如果选中，则 此 value 添加到 ids 中 
		if(this.checked){
			chk = true;
			ids += sp + this.value;
			sp = ',';
		}
	});
	//判断是否至少选中一项
	if(!chk){
		webToast(title + "至少选择一项","middle",3000);
		return false;
	}else
		return true ;
}
//手机验证 正则
function chkTelephone(tel,id,url){
	
	regMobile=/^1\d{10}$/;/*验证手机号码*/
	if (!regMobile.test(tel))
	{
		webToast( "手机号码格式不正确","middle",3000);
		$('#telephone').focus();
		return false;
	}else
		return true;

}

//检查所在地区 是否已经选择
function chkAddress(){
	if($('#province').val() == ''){
		webToast( "请选择省份","middle",3000);
		return false;
	}
	if($('#city').val() == ''){
		webToast( "请选择城市","middle",3000);
		return false;
	}
	if($('#county').val() == ''){
		webToast( "请选择区/县","middle",3000);
		return false;
	}
	
	if($('#address').length > 0 && $('#address').val() == ''){
		webToast( "请填写详细地址","middle",3000);
		return false;
	}else{
		return true ;
	}
	
}
/**
* 弹窗提示 支付信息选择
**/
function selectPay(){
	if($('.tableEdit').length > 0){
		var html = '',msg = '';
		if( $("input[name='image']:checked").length == 1 )	
		 msg += '<li><span>图片：</span> <input type="text" name="img_num" val=""> 月 ， 4元/月</li>';
		 if( $("input[name='video']:checked").length == 1 )	
		 msg += '<li><span>视频：</span> <input type="text" name="video_num" val=""> 月 ， 4元/月</li>';
		html = '<div id="selectPay">\
			<ul>'
			+msg+
			'<li><span>合计：</span> <font class="count"></font></li>\
			<li> <button>确认</button></li>\
			</ul>\
		 </div>';
		$('.tableEdit').append(html);
		$('.tableEdit #selectPay').css({'left':(screen.width - 200) / 2 ,'top' : (screen.height )/2 });
	}	
}

/*
* 技工信息 提交规则
*
* 技工姓名 不能为空
* 性别 默认 男 
* 年龄 默认 20以下
* 工作年限 默认 一年以下
* 技能 至少选择一个 
* 目标酬薪 必填
* 手机号码正则验证 且 唯一
* 所在地区 必选
* 图片上传  收费 可选
* 视频上传  收费 可选
* 
*/

function checkWorkerEdit(){
	if($('#name').val() == ''){
		 webToast("技工姓名不能为空","middle",3000);
		return false ;	
	}
	if($('#pay').val() == ''){
		 webToast("目标酬薪不能为空","middle",3000);
		return false ;	
	}
	chkCheckbox( $('.skill') , '技能') ;
		 
	chkTelephone($('#telephone').val());
	
	var res = chkAddress();
	if(res == false) return false ;
	
	$("#workerEdit input[type='file']").remove();
	return true ;
}

/*
* 木炭厂家信息 提交规则
*
* 厂家名称 不能为空 且 唯一
* 产品原料 不能为空 
* 产品类别 至少选择一个
* 产量 默认第一个
* 所在地区 必选
* 联系人 不能为空  
* 手机号码正则验证 且 唯一
* 图片上传  收费 可选 
* 视频上传  收费 可选 
*/

function checkCompanyEdit(){
	
	if($('#name').val() == ''){
		 webToast("厂家名称不能为空","middle",3000);
		return false ;	
	}
	if($('#material').val() == ''){
		 webToast("产品原料不能为空","middle",3000);
		return false ;	
	}
		
	var res = chkCheckbox( $('.tier') , '类别') ;
	if(res == false) return false ;
	
	if($('#linkman').val() == ''){
		 webToast("联系人不能为空","middle",3000);
		return false ;	
	}
	
	var res = chkTelephone($('#telephone').val());
	if(res == false) return false ;
	
	var res = chkAddress();
	if(res == false) return false ;
	
	$("#companyEdit input[type='file']").remove();
	return true ;
}


/*
* 设备信息 提交规则
*
* 设备名称 不能为空 
* 设备新旧 默认第一个 
* 设备参数 不能为空
* 联系人 不能为空  
* 手机号码正则验证 
* 所在地区 必选
* 图片上传  收费 可选 
* 视频上传  收费 可选 
*/

function checkEquEdit(){
	if($('#name').val() == ''){
		 webToast("设备名称不能为空","middle",3000);
		return false ;	
	}
	if($('#param').val() == ''){
		 webToast("设备参数不能为空","middle",3000);
		return false ;	
	}
	if($('#linkman').val() == ''){
		 webToast("联系人不能为空","middle",3000);
		return false ;	
	}
	chkTelephone($('#telephone').val());
	
	var res = chkAddress();
	if(res == false) return false ;

	
	$("#equEdit input[type='file']").remove();
	return true ;
}
