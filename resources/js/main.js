/*
 * Main.js Social custom javascript. For external javascript, edit site.json and add the uri.
 */
 
var Social = {

	// vars
	name: "Social javascript library",
	version: "1.17",
	instanceid: 1e5,
	cancel: null,
	messagecode: 1e5,
	timelinemaxlength: 255,
	csp: ["Access-Control-Allow-Origin","*"],
	
	tinyEvents: function(ev) {
		
		switch(ev) {
			case 'categories':
			document.addEventListener("DOMContentLoaded", categoryEvents);
			break;
			case 'navigation':
			document.addEventListener("DOMContentLoaded", navigationEvents);		
			break;
		}
		return;
	},
	
	xhr: function() {

		var objxml = null;
		let ProgID = ["Msxml2.XMLHTTP.6.0", "Msxml2.XMLHTTP.3.0", "Microsoft.XMLHTTP"];

		try {
			objxml = new XMLHttpRequest();
		} catch (e) {
			for (var i = 0; i < ProgID.length; i++) {
				try {
					objxml = new ActiveXObject(ProgID[i]);
				} catch (e) {
					continue;
				}
			}
		}
		return objxml;
	},
	
	message: function(str) {
		this.showAndroidToast(this.htmlspecialchars(str,'full') + '\n' + '-'.repeat(32) + '\n' + this.messagecode);
		if(this.messagecode < this.math('maxint')) {
			this.messagecode++;
		}
	},

	hide: function(id) {
		this.dom(id,'none');
	},

	show: function(id) {
		this.dom(id,'block');
	},
	
	htmlspecialchars: function(str,method='full',encoding='utf-8') {
		
		switch(method) {
			
			case 'full':
			var f 	= ['<','>','!','$','%','\'','(',')','*','+',':','=','`','{','}','[',']'];
			var r 	= ['&#60;','&#62;','&#34;','&#36;','&#37;','&#39;','&#40;','&#41;','&#42;','&#43;','&#58;','&#61;','&#96;','&#123;','&#125;','&#91;','&#93;'];
			break;
			
			case 'uri':
			var f 	= ['<','>','\''];
			var r 	= ['&#60;','&#62;','&#39;'];
			break;
		}
		
		for (var i = 0; i < f.length; i++) {
			str = String(str).replace(f[i], r[i]);
		}
		 
		return str;
	},
	
	duplicatearray: function(a,b) {
		a.length = 0;
		a.push.apply(a, b);
		return a;
	},
	
	redirect: function(uri,dir=0) {
		
		if(dir==1) {
			window.reload();
		} else {
			if(!uri) {
				document.location = Social.htmlspecialchars(location.href,'uri');
				} else {
				document.location = Social.htmlspecialchars(uri,'uri');
			}
		}
	},
	
	math: function(method,e=1,mod=1) {
		
		var result = false;
		let i = 0;
		
		switch(method) {
			
			case 'int':
			result = parseInt(e); 
			break;
			
			case 'float':
			result = parseFloat(e);
			break;	

			case 'fixed':
			result = e.toFixed(mod);
			break;	
			
			case 'rand':
			result = Math.random(1,Number.MAX_SAFE_INTEGER);
			break;
			
			case 'maxint':
			result = Number.MAX_SAFE_INTEGER;
			break;		
			
			case 'uuid':
			result = Math.random().toString(16).slice(2, 10);
			break;			
			
		}
		return result;
	},	
	
	rnd: function(method='rand',e=null,len=null,seed=null) {
		
		var r = null;
		switch(method) {
			case 'rand':
			this.r = Math.random(1,Number.MAX_SAFE_INTEGER);
			break;
			case 'uuid':
			this.r = Math.random().toString(16).slice(2, 14);
			break;			
			case 'bytes':
			this.r = Math.random();
			break;			
		}
		return this.r;
	},
	
	showcommentbar: function(id) { 
		this.dom(id,'display','block');
	},
	
	showTimelineOptions: function(id) {
		this.dom(id,'display','block');
	},
	
	closeTimelineOptions: function(id) {
		this.dom(id,'display','none');
	},
	
	timelinePost: function(id,loc) {
		
		this.processText('timelinepost-textarea','post-message');
		
		var message = this.dom(id,'get');
		
		if(message == 'Reply...') {
			this.dom(id,'set',message.replace('Reply...',''));
		}
		
		if(message == 'Post...') {
			this.dom(id,'set',message.replace('Post...',''));
		}
		
		opt = document.getElementById('timelinepost-textarea');
		if(opt.draggable == true) { 

			if(message.length > 1800) {
				this.showAndroidToast('Post is too long, there is a maximum of 1800 characters.');
				} else {
				this.dom(loc,'html',(1800 - message.length));
				document.getElementById('progress-charcount').style = 'width:' + (message.length / 1800 * 100)+ '%;';
			}
			
		} else {
		
			if(message.length > this.timelinemaxlength) {
				this.showAndroidToast('Post is too long, there is a maximum of 255 characters.');
				} else {
				this.dom(loc,'html',(this.timelinemaxlength - message.length));
				document.getElementById('progress-charcount').style = 'width:' + (((message.length / 100) * (message.length / 100) * 1.56) * 10)+ '%;';
			}
		}
	},

	timelineoptions: function(host,method,uid,pid,csrf,id=false) {
		
		switch(method) {
			case 'heart':
			var opt = 'heart';
			var uri = host + 'opt/request/'+opt+'/'+uid+'/'+pid+'/'+csrf+'/';
			break;
			case 'star':
			var opt = 'star';
			var uri = host + 'opt/request/'+opt+'/'+uid+'/'+pid+'/'+csrf+'/';
			break;
			case 'hide':
			var opt = 'hide';
			var uri = host + 'opt/request/'+opt+'/'+uid+'/'+pid+'/'+csrf+'/';
			break;
			case 'hidechat':
			var opt = 'hidechat';
			var uri = host + 'opt/request/'+opt+'/'+uid+'/'+pid+'/'+csrf+'/';
			break;
			case 'block':
			var opt = 'block';
			var uri = host + 'opt/request/'+opt+'/'+uid+'/'+pid+'/'+csrf+'/';
			break;
			case 'share':
			var opt = 'share';
			document.location = host + 'share/'+opt+'/'+uid+'/'+pid+'/'+csrf+'/';
			break;	
			case 'delete':
			var opt = 'delete';
			document.location = host + 'delete/'+opt+'/'+uid+'/'+pid+'/'+csrf+'/';
			break;
			case 'flag':
			var opt = 'flag';
			document.location = host + 'flag/'+opt+'/'+uid+'/'+pid+'/'+csrf+'/';
			break;
			
		}
		
		if(method == 'heart' || method == 'star' || method == 'hide' || method == 'hidechat' || method == 'block') { 
		
			var path = 'resources/images/icons/';
			var req = Social.xhr();

			req.open("GET", uri, true);
			req.withCredentials = true;
			
			req.setRequestHeader('Access-Control-Allow-Origin', '*');
			req.setRequestHeader("Content-Type", "application/json;charset=UTF-8");
			
			req.onreadystatechange = function() {
				if (req.readyState == 4 && req.status == 200) {
					
					 if(req.responseText) {
						 try {
						 var result = req.responseText.split(':');
							 if(result[0] == 'true') { 
								if(method == 'heart') {
									document.getElementById(id).src = host + path + 'heart-red.png';
									document.getElementById(id + '-num').innerHTML = parseInt(result[3]); 
								} 
								if(method == 'star') {
									document.getElementById(id).src = host + path + 'star-yellow.png';
									document.getElementById(id + '-num').innerHTML = parseInt(result[3]);
								}							
							 }
						 } catch(e) {}
					 } 
				}
			}
			req.send(null);
		}
		
	},
	
	follow: function(uid,friend,csrf) {
		document.location = 'follow/'+uid+'/'+friend+'/'+csrf+'/';
	},

	unfollow: function(uid,friend,csrf) {
		if (confirm('Are you sure you want to unfollow?')) {
			document.location = 'unfollow/'+uid+'/'+friend+'/'+csrf+'/';
		} 
	},

	fileUploads:  function(id,title,buttonId) {
		
		if(id) {
			try { 
				var titleText = title.replace('fakepath\\','');
				titleText = titleText.split('\\');
				document.getElementById(id).src = 'https://www.twigpage.com/resources/images/icons/file-selected.png';
				document.getElementById(id).title = titleText[1];
				document.getElementById(id).alt = titleText[1];
			} catch(e) { }
			
			try {
				document.getElementById(buttonId).addEventListener("click", function () {
				Social.uploadProgress('https://www.twigpage.com/init/progress.php','progress-bars','progress-bar-upload');
				}, true);
			} catch(e) { }
		}
	},
	
	calendar: function() {
		window.open('/contents/html/board.php',"Calendar","top=200,left=500,width=550,height=550");
	},

	checkTwig: function(id) {
		
		if(document.getElementById(id).value == '' || document.getElementById(id).value.length <= 3) { 
			
			this.showAndroidToast('Post cannot be empty... try writing more words.');
			
			return false;
			
			} else { 
		}
	},
	
	// Shows a toast inside the twigpage app
	showAndroidToast: function(toast) {
		if(window.innerWidth > 700) {
			window.alert(toast);
		} else {
			try {
			Android.showToast(toast);
			} catch(e) {}
		}
		return;
    },
	
	// Checking the device pixel density
	androidDensity: function() {
		try {
			if (window.devicePixelRatio >= 1.5) {
				AndroidDevice.density('high');
				} else {
				AndroidDevice.density('low');
				} 
		} catch(e) {}
	},

	// Shows a upload progress bar when updating settings.
	progressAndroid: function(toast) {
		try {
        AndroidProgress.uploadSettings(toast);
		} catch(e) {}
		return true;
	},

	// Close dialogs
	dismissAndroid: function() {
		try {
        AndroidProgress.dismiss();
		} catch(e) {}
		return true;
	},
	
	errorInput: function(id) {
		
		if(id != false) {
			document.getElementById(id).id = 'input-error';
		}
		
	},
	
	checkLogin: function() {
		
		let username = document.forms['login']['username'].value;
		let pass     = document.forms['login']['password'].value;
		
		if(username == '') {
			this.errorInput('login-username');
			this.showAndroidToast('Username cannot be empty.');
			return false;
		} else if(pass == '') {
			this.errorInput('login-password');
			this.showAndroidToast('Password cannot be empty.');
			return false;			
		} else {
		}		
		
	},
	
	checkSignupForm: function() {
		
		let username = document.forms['signup']['username'].value;
		let pass     = document.forms['signup']['password'].value;
		let captcha  = document.forms['signup']['captcha'].value;
		let email 	 = document.forms['signup']['email'].value;
		
		if(email == '') {
			this.errorInput('signup-email');
			this.showAndroidToast('Email cannot be empty.');
			return false;
			} else if(username == '') {
			this.errorInput('signup-username');
			this.showAndroidToast('Username cannot be empty.');
			return false;			
			} else if(pass == '') {
			this.errorInput('signup-password');
			this.showAndroidToast('Password cannot be empty.');
			return false;			
			} else if(captcha == '') {
			this.errorInput('signup-captcha');
			this.showAndroidToast('Captcha answer cannot be empty.');
			return false;			
		} else {
			// return true;
		}
	},
	
	createCommentForm: function(div,locate,csrf,commentid,uidid,toid,atuser) {

		if(document.getElementById(div).style.display == 'block') {
			return;
		}

		const options = document.createElement('form');
		options.name = 'post';
		options.action = locate;
		options.method = 'POST';
		
		opt = document.createElement('input');
		opt.type = 'hidden';
		opt.name = 'PHP_SESSION_UPLOAD_PROGRESS';
		opt.value = '123';
		options.appendChild(opt);		
		
		
		opt = document.createElement('input');
		opt.type = 'hidden';
		opt.name = 'csrf';
		opt.value = csrf;
		options.appendChild(opt);

		opt = document.createElement('input');
		opt.type = 'hidden';
		opt.name = 'comment-id';
		opt.value = commentid;
		options.appendChild(opt);

		opt = document.createElement('input');
		opt.type = 'hidden';
		opt.name = 'uid-id';
		opt.value = uidid;
		options.appendChild(opt);

		opt = document.createElement('input');
		opt.type = 'hidden';
		opt.name = 'to-id';
		opt.value = toid;
		options.appendChild(opt);

		opt = document.createElement('input');
		opt.type = 'hidden';
		opt.name = 'at-user';
		opt.value = '@' + atuser;
		options.appendChild(opt);

		opt = document.createElement('textarea');
		opt.name = 'post-message';
		opt.placeholder = 'Reply...';
		opt.className = 'timelinepost-textarea';
		opt.id = 'timelinepost-textarea-1';
		opt.rows = 3;
		opt.draggable = "false";
		
		opt.addEventListener("keydown", function () {
				message = document.getElementById('timelinepost-textarea-1').value;
				if(message == 'Reply...' || message == 'Post...') { 
					document.getElementById('timelinepost-textarea-1').value = message.replace('Reply...','');
				}
				if(message.length > 255) {
					this.showAndroidToast('Post is too long, there is a maximum of 255 characters.');
					} else {
					document.getElementById('charcounter').innerHTML = parseInt(255 - message.length) + ' characters left.' ;
				}
			
		}, true);
		
		options.appendChild(opt);

		opt = document.createElement('span');
		opt.id ='charcount';
		opt.className = 'charcount-reply';
		
		opt2 = document.createElement('span');
		opt2.id ='charcounter';
		opt2.innerHTML = '255 characters left.';	
		opt2.setAttribute('aria-hidden', true);
		opt.appendChild(opt2);
		options.appendChild(opt);

		opt = document.createElement('input');
		opt.type = 'submit';
		opt.name = 'post';
		opt.value = 'Reply';
		opt.className = 'profile-reply-button';
		
		opt.addEventListener("change", function () {
			Social.uploadProgress('https://www.twigpage.com/init/progress.php','progress-bars','progress-bar-upload');
		}, true);
	
		options.appendChild(opt);	
		
		document.getElementById(div).appendChild(options);
		document.getElementById(div).style.display = 'block';
		
	},
	
	dom: function(id,method,value='') {

		try {
			if(id) {
			
				switch(method) {

					case 'get':
					return document.getElementById(id).value;
					break;	
					
					case 'set':
					document.getElementById(escape(id)).value = this.htmlspecialchars(value,'full');
					break;
					
					case 'none':
					document.getElementById(escape(id)).style.display = 'none';
					break;

					case 'block':
					document.getElementById(escape(id)).style.display = 'block';
					break;
					
					case 'html':
					document.getElementById(escape(id)).innerHTML = this.htmlspecialchars(value,'full');
					break;
					
					case 'gethtml':
					document.getElementById(escape(id)).innerHTML;
					break;	
					
					case 'display':
					document.getElementById(id).style.display = value;
					break;	
					
					case 'fontWeight':
					document.getElementById(id).style.fontWeight = value;
					break;	
					
					case 'className':
					document.getElementById(escape(id)).style.fontWeight = this.htmlspecialchars(value,'full');
					break;				
				}
			
			} else {
				this.message('DOM constructor could not populate the requested action.');
			}
		} catch(e) {
			//this.message(this.htmlspecialchars(e,'full'));
		}
	
	},
	
	returner: function(data) {
		this.showAndroidToast(this.htmlspecialchars(data,'full'));
		return this.htmlspecialchars(data,'full');
	},

	json: function(uri) {
	 Social.fetchJSON(uri,function(response) {
		var obj =  JSON.parse(response);
		return obj;
	 });
	},
 
 	uploadProgress: function(uri,id,div) {

		try {
			if(document.getElementById(div).style.display != 'block') {
				document.getElementById(div).style.display = 'block';
			}
		} catch(e) {}

		var req = Social.xhr();

		req.open("GET", uri, true);
		req.withCredentials = true;
		
		req.setRequestHeader('Access-Control-Allow-Origin', '*');
		req.setRequestHeader("Content-Type", "application/json;charset=UTF-8");
		
		req.onreadystatechange = function() {
			if (req.readyState == 4 && req.status == 200) {
				 if(req.responseText < 95) {
					 try {
					 document.getElementById(id).style = 'width:' + req.responseText + '%!important;';
					 } catch(e) {}
					 setTimeout(() => {
						  Social.uploadProgress(uri,id);
						}, 10000)
				 } 
				 
				 if(req.responseText >= 95) {
					 try {
					 document.getElementById(div).style = 'display:none!important;';
					 } catch(e) {}
				 }
			}
		}
		req.send(null);
	},

	pingMessenger: function(method,id,uid,toid,csrf,photo) {
		
		if(method == 'fetch') {
			this.postMessenger('fetch',id,uid,toid,csrf,photo);
		}

		if(method == 'typing') {
			
		}
		
	},
	
	messengerUpdates: function(event,method,id,uid,toid,csrf,photo) {

		try {
			var scrolldocument = document.getElementById(id);
			scrolldocument.scrollTop = scrolldocument.scrollHeight;
			} catch(e) {}
			
			setInterval(() => {
			Social.pingMessenger(method,id,uid,toid,csrf,photo);
			}, 5000);	
		
	},
	
	scroller: function(scrollerMethod,method,id,uid,toid,csrf,photo) {
		
		switch(scrollerMethod) {
			case 'bottom':
			if(id != false) { 
				var scrolldocument = document.getElementById(id);
				try {
				scrolldocument.scrollTop = scrolldocument.scrollHeight;
				} catch(e) {}
				document.addEventListener("change", Social.messengerUpdates(event,method,id,uid,toid,csrf,photo));
				} else {
				window.scrollTo(0, window.innerHeight);
			}
			break;
		}
	},

	stopMessenger: function() {
		
		try {
		document.removeEventListener("change", Social.messengerUpdates);
		} catch(e) {}
		
	},
	
 	postMessenger: function(method,id,uid,toid,csrf,photo) {
		
		var req = Social.xhr();
		var data = [];
		
		if(method == 'post') {
			
			var uri = '/messenger/message-caller.php';
			data.push('uid=' + encodeURIComponent(uid));
			data.push('&toid=' + encodeURIComponent(toid));
			data.push('&csrf=' + encodeURIComponent(csrf));
			data.push('&postmessage=' + encodeURIComponent(document.getElementById(id).value));
			data.push('&method=' + encodeURIComponent(method));
			document.getElementById(id).value='';
			
		}

		if(method == 'fetch') {
			var uri = '/messenger/fetch.php';
			data.push('&csrf=' + encodeURIComponent(csrf));
			data.push('&toid=' + encodeURIComponent(toid));
			data.push('&method=' + encodeURIComponent(method));
		}		

		req.open("POST", uri, true);
		req.withCredentials = true;
		req.setRequestHeader('Access-Control-Allow-Origin', '*');
		req.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded; charset=UTF-8');
			
		req.onreadystatechange = function() {
			
			if (req.readyState == 4 && req.status == 200) {
				
				 if(req.responseText) {
					 
							 try {
								 
							 const obj = JSON.parse(req.responseText);
							 
								 var j =0;
								 var i = obj[j].id;
								 
								 if(document.getElementById('messenger-chat' + i) == null) { 
									 var message = obj[j].message;
									 var mixedmedia = obj[j].mixedmedia;
									 var statusid = obj[j].status;
									 var blocked = obj[j].blocked;
									 var time = obj[j].time;
									 
									 var element1 = document.createElement('div');
									 element1.className = 'messenger-chat';
									 element1.id = 'messenger-chat' + i;
									 
									 var element2 = document.createElement('div');
									 element2.className = 'messenger-photo';
									 element2.id = 'messenger-photo' + i;
									 
									 var element3 = document.createElement('div');
									 element3.className = 'messenger-text';
									 element3.innerHTML = message;
									 element3.id = 'messenger-text' + i;
									 
									 var element4 = document.createElement('div');
									 element4.className = 'messenger-image';
									 element4.id = 'messenger-image' + i;
									 element4.style = "background:url('https://www.twigpage.com/"+photo.replace('//','/')+"') !important; background-size: cover!important; background-color: #fff!important;";
									 
									 document.getElementById('messenger').appendChild(element1);
									 document.getElementById('messenger-chat' + i).appendChild(element2);
									 document.getElementById('messenger-chat' + i).appendChild(element3);
									 document.getElementById('messenger-photo' + i).appendChild(element4);
									 this.scroller('bottom','fetch','messenger',uid,toid,csrf,photo);
								 }
								 
							 } catch(e) {} 
					
				 } 
			}
		}
		req.send(data);
	},
	
	fetchJSON: function(uri,callback) {

		var req = Social.xhr();

		req.open("GET", uri, true);
		req.withCredentials = true;
		
		req.setRequestHeader('Access-Control-Allow-Origin', '*');
		req.setRequestHeader("Content-Type", "application/json;charset=UTF-8");
		
		req.onreadystatechange = function() {
			if (req.readyState == 4 && req.status == 200) {
				callback(req.responseText);
			}
		}
		req.send(null);
	},
	
	fetchHTML: function(method,uri,data=[],id,r=false) {

		var req = this.xhr();
		var res = '';

		if(method == 'POST') {
			if(data != null) {
				var requestMethod = 'POST';
			}
		} else {
			var requestMethod =  'GET';
		}
		
		req.open(requestMethod, uri, true);
		req.withCredentials = true;
		req.setRequestHeader('Access-Control-Allow-Origin', '*');

		if(requestMethod == 'POST' ) {
			
			req.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded; charset=UTF-8');
			req.send(data);
			
			req.onreadystatechange = function() {
				
				if (req.readyState == 4 && req.status == 200) {
					this.res = req.responseText;
					if(id) {
					Social.dom(id,'html',this.res);
					}
					if(r) {
					Social.redirect(r);
					}
				}
			}
		
			} else {
			req.setRequestHeader("Content-Type", "application/json;charset=UTF-8");
			req.send(null);
		}
	},

	resizeArea: function (id,px) {
		document.getElementById(id).style.height = px + 'px';
		document.getElementById('charcounter-init').innerHTML = '1800';
		opt = document.getElementById('timelinepost-textarea');
		opt.draggable = "true";
		opt.addEventListener("keydown", function () {
		message = document.getElementById('timelinepost-textarea').innerHTML;
		if(message.length > 1800) {
			this.showAndroidToast('Post is too long, there is a maximum of 1800 characters.');
			} else {
			document.getElementById('charcounter-init').innerHTML = parseInt(1800 - message.length);
		}
		});
				
	},
	
	processText: function(from,to) {
		var twig = document.getElementById(from).innerHTML;
		document.getElementById(to).value = twig;
	},
	
	styling: function(method,textareaId) {
		
				var selection = window.getSelection();

				if(method =='bold') { 
					range = document.getSelection().getRangeAt(0);
					var clone = range.cloneContents();
					range.deleteContents();
					newNode = document.createElement("b");
					newNode.appendChild(document.createTextNode(clone.textContent));
					range.insertNode(newNode);
				} 
				
				if(method =='emphasize') { 
					range = window.getSelection().getRangeAt(0);
					var clone = range.cloneContents();
					range.deleteContents();
					newNode = document.createElement("em");
					newNode.appendChild(document.createTextNode(clone.textContent));
					range.insertNode(newNode);
				} 
				
				if(method =='blockquote') { 
					range = window.getSelection().getRangeAt(0);
					var clone = range.cloneContents();
					range.deleteContents();
					newNode = document.createElement("blockquote");
					newNode.appendChild(document.createTextNode(clone.textContent));
					range.insertNode(newNode);
					var textfragment = document.getElementById(textareaId).innerHTML;
					document.getElementById(textareaId).innerHTML = textfragment + '<br>';
					
				} 
				
				if(method =='code') { 
					range = window.getSelection().getRangeAt(0);
					var clone = range.cloneContents();
					range.deleteContents();
					newNode = document.createElement("code");
					newNode.appendChild(document.createTextNode(clone.textContent));
					range.insertNode(newNode);
					newNode = document.createElement("p");
					range.insertNode(newNode);
					var textfragment = document.getElementById(textareaId).innerHTML;
					document.getElementById(textareaId).innerHTML = textfragment + '<br>';
				} 	
		this.timelinePost('post-message','charcounter-init');
		this.hide('style-guide');
	},
	
	//--> end of Social javascript logic.
	
};

/* Cache-control.
 * Setting a fixed instanceid when main.js is loaded. 
 * the instanceid prevents json caching for recently updated files, 
 * but also prevents caching too much on individual json files.
*/
Social.instanceid = Social.rnd('uuid');