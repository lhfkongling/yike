/**
 * ajax封装
 *
 * 使用方法：
 * include jquery代码库
 * ajaxobj("/index.php/user"(请求url),{data:'senddata'}(请求数据),succFunc(回调函数),beforeFunc(发送之前函数调用),
 * 		   "get"(请求方式,默认post),false(是否异步,默认false),"json"(返回数据格式,默认json),errFunc(错误回调函数),'jsonpFunc'(跨域回调函数));
 *
 * author ：zhangxudong
 * date ：2017-03-30
 */
 
(function(){
	var ajaxObj = {
		/**
		 * 启动ajax
		 */
		initAjax : function() {
			ajaxObj.createAjax.apply( ajaxObj, arguments );
		},

		/**
		 * 参数详解
		 *
		 * [url		=>请求路径, 	data		  => 请求数据,
		 * type		=>请求类型, 	async		  => 是否异步,
		 * dataType	=>数据类型, 	success		  => 成功回调函数,
		 * error	=>错误回调函数, jsonpCallback => 跨域回调函数,
		 * beforeSend => 发送之前函数调用]
		 *
		 * 参数顺序：1->url, 2->data, 3->success,4->beforeSend,5->type,
		 *  6->async, 7->dataType,  8->error, 9->jsonpCallback
		 */
		createAjax : function() {
			this.paramFunc.apply( this, arguments );
			//console.log(this.param);

			$.ajax({
				url 	 : this.param.url,
				data 	 : this.param.data,
				type 	 : this.param.type,
				async 	 : this.param.async,
				dataType : this.param.dataType,
				beforeSend : this.param.beforeSend,
				success  : this.param.success,
				error    : this.param.error,
				jsonpCallback : this.param.jsonpCallback,	// 跨域
			});
		},

		/**
		 * 默认参数
		 */
		param : {url:'',data:'{}',success:'',beforeSend:'',type:'post',async:false,dataType:'json',error:'',jsonpCallback:''},

		/**
		 * ajax 参数处理
		 */
		paramFunc : function() {
			var args = arguments,
				length = arguments.length,
				arr = ['xml','html','script','json','jsonp','text'];
			
			for ( var i=0; i < length; i++ ) {
				switch( i ){
					case 0 :
						if ( args[i].length > 0x00 ) {
							this.param.url = args[i];
						}
						break;

					case 1 :
						if ( (typeof( args[i] ) === 'object') ) {
							this.param.data = args[i];
						}
						break;

					case 2 :
						if ( $.isFunction( args[i] ) ) {
							this.param.success = args[i];
						} else {
							this.param.success = this.success;
						}
						break;

					case 3 :
						if ( $.isFunction( args[i] ) ) {
							this.param.beforeSend = args[i];
						} else {
							this.param.beforeSend = this.beforeSend;
						}
						break;

					case 4 :
						if ( ( typeof(args[i]) === 'string' ) && ( args[i].toLowerCase() === 'get' ) ) {
							this.param.type = args[i];
						}
						break;

					case 5 :
						if ( typeof args[i] === 'boolean' ) {
							this.param.async = args[i];
						}
						break;

					case 6 :
						if ( ( typeof(args[i]) === 'string' ) && ( $.inArray( args[i].toLowerCase(), arr ) > -1 ) ) {
							this.param.dataType = args[i];
						}
						break;

					case 7 :
						if ( $.isFunction( args[i] ) ) {
							this.param.error = args[i];
						} else {
							this.param.error = this.error;
						}
						break;

					case 8 :
						if ( $.isFunction( args[i] ) ) {
							this.param.jsonpCallback = args[i];
						} else {
							this.param.jsonpCallback = this.jsonpCallback;
						}
						break;

					default :
					break;
				}
			}
		},

		/**
		 * 发送之前调用函数
		 */
		 beforeSend : function() {},

		/**
		 * 成功回调函数
		 */
		success : function( data ) {},

		/**
		 * 失败回调函数
		 */
		error : function( data ) {},

		/**
		 * 跨域回调函数
		 */
		jsonpCallback : function( data ) {},
	}

	window.ajaxobj = ajaxObj.initAjax;
 })();