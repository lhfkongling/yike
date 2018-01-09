
 var ajaxNative = (function(){
 	
 	var xhr = (function(){
		if(window.ActiveAObject){
			//适用于 IE5 IE 6
			try { return new ActiveXObject("Microsoft.XMLHTTP"); }
			catch(e) { return new ActiveXObject("Msxml2.XMLHTTP"); } 
		}
		
		if(window.XMLHttpRequest) { return new XMLHttpRequest(); }
		else { alert('对不起，您的浏览器不支持AJAX应用。') }
		
	})();	
	
	//Ajax 状态监听
	var watchChange = function (){
		if(xhr.readyState == 4  && xhr.status == 200){
			
			if(typeof responseUpdateSecondPic === 'function' ){
				  var jsonObj = eval("("+xhr.responseText+")");
				responseUpdateSecondPic(jsonObj);
			}
			console.log('回调');
			console.log(xhr.responseText);
//			document.getElementById("debug").innerHTML=xhr.responseText;	
		}
		
	}

	var uploadfailded = function (evt){
		alert("上传失败");	
	}
	
	//侦听文件上传情况
	var onprogress = function (evt){
		console.log(evt) ;
		var per = Math.floor(100*evt.loaded / evt.total );
		console.log(evt) ;
//		 document.getElementById("speed").style.width = per +'%' ;
//		 document.getElementById("speed").innerHTML=per +'%'  ;
	}

	
	//发送文件
	return function (formData,url,responseCuccess,responseFauilded){

		//打开连接
		xhr.open("POST",url,true);
		
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
	}
 })() ;