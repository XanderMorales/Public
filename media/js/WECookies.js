<!--
function WECookie(action,name,value,expires,path,domain,secure){
	this.name = name;
	if(action == 'set'){
		this.value = value || null;
		this.expires = (expires)?expires:'-1';
		this.path = path || '/';
		this.domain = domain || document.domain;
		this.secure = secure || '';
		this.today = new Date();
		this.expr = new Date(this.today.getTime() + (this.expires * 86400000));
	}
	if(action == 'set'){ this.set_cookie = WESetCookie(this); }
	else if(action == 'get'){ this.cookie_value = WEGetCookieValue(this); }
	else if(action == 'all'){ this.dump = WEGetAllCookies(); }
}
function WESetCookie(WECookie){
	this.milk = WECookie.name + "=" + escape(WECookie.value);
	this.milk += ";expires=" + WECookie.expr.toGMTString();
	this.milk += ";path=" + WECookie.path;
	this.milk += ";domain=" + WECookie.domain;
	this.milk += WECookie.secure;
	document.cookie = this.milk;
}
function WEGetCookieValue(WECookie){
    this.get = document.cookie.indexOf(WECookie.name + "=");
    this.index =(this.get != -1)?document.cookie.indexOf("=", this.get) + 1:0;
    this.endstr = document.cookie.indexOf(";", this.index);
    if(this.endstr == -1){ this.endstr = document.cookie.length; }
    return (this.index != 0)?unescape(document.cookie.substring(this.index, this.endstr)):null;
}
function WEGetAllCookies(){
	this.a = new Array();
	this.split_cookie = document.cookie.split(";");
	for(count=0; count < this.split_cookie.length; count++){
		this.get_cookie_name = this.split_cookie[count].indexOf('=');
		this.cookie_name = this.split_cookie[count].substring(this.get_cookie_name,0);
		this.new_name = this.cookie_name.replace(' ','');
		this.a.push(this.new_name);
	}
	return this.a;
}
//-->