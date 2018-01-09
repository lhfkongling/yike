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

	/**
	 * 会员发布二手房 
	 * 单价计算 
	 */
	
	$('#average_price').focus(function(){
		console.log('单价计算') ;
		var sell_price =  parseFloat($('#sell_price').val() ) * 10000;
		var area =  parseFloat($('#area').val() ) ;
		console.log('sell_price',sell_price /area);
		$(this).val( parseInt( sell_price /area) ) ;
	})
	

	//创建微信公众号 自定义菜单 
	$('#createMenu').click(function(){
		ajaxobj(createMenu,{},responceCreateMenu);
	})
	function responceCreateMenu(data){
		if(data == 1){
			alert('创建成功');
		}else{
			alert('创建失败'); //webToast( "创建失败","middle",3000);
		}
	}

	
	/*
	input 文本框 输入 失去焦点检验规则 
	如果 data-type="unique"  为 唯一值 不为空验证 和  需要数据库验证  data-tip 为 提示信息
	如果 data-type="notnull"  为 不为空  data-tip 为 提示信息
	*/
	$('input').blur(function(){
		
		//唯一值
		if($(this).attr('data-type') == 'unique'){
			console.log('唯一值');		
			if($(this).val() == ''){
				var tip = $(this).parent().find('span').html() +   '不为空'; 
				webToast(tip,"middle",3000);
			}else{
				var field =  $(this).attr('name'); 
				var val =  $(this).val();
				var id =  $('#id').val();
				var tip =  $(this).attr('data-tip'); ;
				
				ajaxobj(checkRepeatName,{field:field,val:val,id:id,tip:tip},responceCheckRepeatName);
			}
			
		}
		//为 不为空
		if($(this).attr('data-type') == 'notnull'){
			console.log('为 不为空');		
			if($(this).val() == ''){
				var tip = $(this).parent().find('span').html() +   '不为空'; 
				webToast(tip,"middle",3000);
			}
		}
	})
	
	
	
	//图片上传 
	//图片点击事件
	$('.picture ').delegate('img', 'click',function(){
		
			if($('.base64Img').length> 0){
				$('.base64Img').show();
				picNumber = $(this).attr('id');
			}
	})
	
//	/房源图片删除/
	$('.picture').delegate('em','click',function(){		
		
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
	
	var responseImagesDelete = function (data){		
	}
	
	
	//点击添加户型信息
	var typeNumber = 0 ; //
	$('.picture .type-list .add ').on('click',function(){
		
		$('.picture .type-list .house_type').append(
			'<p class="clearfix">\
				<font class="del">删除</font>\
      			<img id="typeNew'+typeNumber+'" src="'+_public+'/Home/images/add.png" >\
            	<input type="hidden" name="housetype[url][]" class="pichidden"  value="" />\
            	户型：<input type="text" name="housetype[type][]"  value="" /><br/>\
            	面积：<input type="text" name="housetype[area][]"  value="" />\
      		</p>'
		);
		typeNumber++ ;
	})
	
	
	//点击添加重要属性	
	$('.picture .important .add ').on('click',function(){
		$('.picture .important div').append(
			'<p class="clearfix">\
      			<font class="del">删除</font>\
            	值：<input class="val" type="text" name="important[val][]"  value="" />\
            	<input type="hidden" name="important[house_id][]"  value="'+house_id+'" />\
            	<input type="hidden" name="important[tag][]"  value="'+tag+'" />\
      		</p>'
		);
		
	})

	//点击添加次要属性
	
	$('.picture .secondary .add ').on('click',function(){
		$('.picture .secondary div').append(
			'<p class="clearfix">\
      			<font class="del">删除</font>\
            	值：<input class="val" type="text" name="secondary[val][]"  value="" />\
            	<input type="hidden" name="secondary[house_id][]"  value="'+house_id+'" />\
            	<input type="hidden" name="secondary[tag][]"  value="'+tag+'" />\
      		</p>'
		);
		
	})
	//删除属性
	$('.picture').delegate('.del', 'click',function(){
		var id = $(this).attr('data-id');
		var table = $(this).attr('data-type');

		$(this).parent().remove();
		ajaxobj(deleteAttr,{id:id,table:table},responceDeleteAttr);
		
	})
	function responceDeleteAttr(){}
	
	/**
	 * 广告位 添加广告图片文本框 
	 * 2017-10-25
	 */
	$('.tableEdit .images').delegate('.button','click',function(){
		if($(this).attr('data-type') == 'ad'){
			
			$('.tableEdit .images dl').append(
			'<dd>\
				<img src="/Public/Admin/images/default.jpg">\
				<input type="file" name="furl'+imagesCount+'" id="furl'+imagesCount+'" class="file" />\
				<input type="hidden" name="imgs_url[]" value="{$vo}" />\
				跳转路径<input type="text" name="param[]" value=""/> <br/>\
				图片名称 <input type="text" name="name[]" value=""/><br/>\
				<font>删除</font> \
			</dd>'
			);
		}
		$('.tableEdit .images dl dd:last').find('.file').click();
		imagesCount ++;
		//$(this).next().click();	
	}) 
	$('.tableEdit .images dl ').delegate(".file",'change',function(){
			var id = $(this).attr('id');
			var file = document.getElementById(id).files[0];
	
			console.log(file);
			
			var index1=file.name.lastIndexOf(".");
			var index2=file.name.length;
			var suffix=file.name.substring(index1+1,index2);//后缀名
			
			var formData = new FormData();
			formData.append("file",file); //包装文件
			formData.append( "suffix",suffix );//包装后缀名
			formData.append( "id" , id );//包装后缀名
			formData.append("rename",1); //重命名 1为重名，0为使用原来的名称
	
			//打开连接
			xhr.open("POST",updateUrl,true);
			
			//监听上传过程事件
			xhr.upload.addEventListener("progress",onprogress,false);
			//监听失败请求
			xhr.addEventListener("error",uploadfailded,false);
			
			//返回结果时间
			xhr.onreadystatechange = watchChange ;
			
			//发送信息
			xhr.send(formData);
			
			//console.log(formData);
			console.log('发送文件');
					
	})
	/**
	 * 删除广告图片 
	 *
	 */
	$('.tableEdit .images dl').delegate("font",'click',function(){
		$(this).parent().remove();	
	})
	
	/**
	 * checkbox 
	 * 全选 反选 取消
	 * 
	 */
	function checkboxFn(type){
		var val = msg = ''; 
		$('.id').each(function(k,v){			
			if(type == 1){ //全选
				$('.id')[k].checked = true;
			}else if(type == 2){ //反选
				$('.id')[k].checked = false;
			}else if(type  == 3){ //取消
				if($('.id')[k].checked == true){
					$('.id')[k].checked = false;
				}else{
					$('.id')[k].checked = true;
				}	
			}else if(type == 4){//获取值
				if($('.id')[k].checked == true){
					val += msg +  $('.id')[k].value;
					msg = ',';
				}				
			}
		})	
		return val ;
	}
	
	$('#checkbox-all').bind('click',function(){checkboxFn(1) ;})
	$('#checkbox-cancel').bind('click',function(){checkboxFn(2) ;})
	$('#checkbox-reverse').bind('click',function(){checkboxFn(3) ;})
	$('#batch').bind('click',function(){
		var ids = checkboxFn(4) ;
		if( ids == ''){
			 	webToast('请至少选择一个选项',"middle",3000);
		}else{
			var authentication = $('#authentication').val();
			var type = $('#type').val();
			if(type || authentication){
				if(confirm('你确定要批量修改这些信息吗')){
					ajaxobj(batchUserURL,{ids:ids,type:type,authentication:authentication},responceBatchUser); 
				}
			}else{
				webToast('请选择批量修改内容',"middle",3000);
			}
		} ;
	})
	/**
	 * 会员列表批量操作回调响应处理 
	 */
	function responceBatchUser (data){
		webToast(data.info,"middle",3000);
		if(data.error == 0){
			setTimeout(function(){$('#userSearch').submit();},3000);	
		}
	}
	
	
	
})

//检查字段名是否重复回调函数 
function responceCheckRepeatName(data){
	if(data['error'] == 1){
	 	webToast(data.tip,"middle",3000);
	}
	
}

//不为空提示函数
function tipForEmpty(t,type){
	var msg = '  不能为空';
	switch(type){
		case 'select':
			msg = '  必须选择';
			break;
		default:
			
	}
	
	var tip = $(t).parent().find('span').html()+msg;	
		webToast(tip,"middle",3000);
}

//文章信息体检前检查函数
function checkArtile(frm){
	var error = 0;
	if(frm.name.value == ''){
		tipForEmpty(frm.name,'input');
		return false ;
	}
	if(frm.cat_id.value == ''){
		tipForEmpty(frm.cat_id,'select');
		return false ;
	}
	if(frm.author.value == ''){
		tipForEmpty(frm.author,'input');
		return false ;
	}
	if(frm.keyword.value == ''){
		tipForEmpty(frm.keyword,'input');
		return false ;
	}
	
	if(frm.brief.value == ''){
		tipForEmpty(frm.brief,'input');
		return false ;
	}
	
	
	
	return true ;
	
}

function checkBuilding(frm){
	
	
}


//创建 AJAX 函数 
function xhrNewobject (){
	if(window.ActiveAObject){
		try
		{
			//适用于 IE5 IE 6
			return new ActiveXObject("Microsoft.XMLHTTP");	
		}
		catch(e)
		{
			return new ActiveXObject("Msxml2.XMLHTTP");
		}
			
	}
	if(window.XMLHttpRequest)
	{
		return new XMLHttpRequest();	
	}
	else
	{
		alert('对不起，您的浏览器不支持AJAX应用。')	
	}
}
//创建AJAX 对象
xhr = xhrNewobject();

//Ajax 状态监听
function watchChange(){
	if(xhr.readyState == 4  && xhr.status == 200){
		console.log('回调');
		console.log(xhr.responseText);
		var data=JSON.parse(xhr.responseText) ;
		if(data.error == 0)
		{
			$('#'+data.id).prev().attr('src',data.url) ;		
			$('#'+data.id).next().val(data.url) ;
		}
		else
			alert('上传失败') ;
	}
	
}

function uploadfailded(evt){
	alert("上传失败");	
}

//侦听文件上传情况
function onprogress(evt){
	console.log(evt) ;
	var per = Math.floor(100*evt.loaded / evt.total );
	console.log(evt) ;
//	 document.getElementById("speed").style.width = per +'%' ;
//	 document.getElementById("speed").innerHTML=per +'%'  ;
}



/**************************************************************************************************************/
