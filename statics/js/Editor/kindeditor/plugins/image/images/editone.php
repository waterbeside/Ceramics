<?php
//EditONE copyright reserved by Zhang Hua(MSN:mz24cn@hotmail.com) at Shanghai Group Entropy IT Co., Ltd. 2007-05-08
error_reporting(0);
$do=(!isset($_GET["do"]))? "frame_set":$_GET["do"];
$PAGE=$_SERVER["PHP_SELF"];
$UA_IE=strpos($_SERVER["HTTP_USER_AGENT"], "MSIE")!==false;

$AUTH_USER=array("username"=>"admin", "password"=>"31c9a133f8397f3229a1e88e229b0251", "email"=>"support@gaya.cn");//AUTH_USER
$CONFIG=array("version"=>"V1.4RC/070516", "nLayer"=>"1", "auth_type"=>"cookie", "cookie_life"=>"43200", "session_life"=>"20", "realm"=>"EditONE@{server_name}", "start_dir"=>".", "base_dir"=>null, "encoding"=>"GB2312", "encoding_list"=>"��������|GB2312|936||ͳһ��|UTF-8|65001||��������|BIG5|950||latin1|ISO8859-1|1252||����|shift_jis|932||����|ks_c_5601|949", "wrap_word"=>"undefined", "editor_font_family"=>"����", "editor_font_height"=>"14", "editor_line_height"=>"16", "fckeditor_dir"=>"fckeditor/", "list_sort"=>"name");//CONFIG

$ROOT=$_SERVER["SERVER_NAME"].substr($PAGE,0,strrpos($PAGE,"/"))." powered by editone.gaya.cn";
$realm=str_replace("{server_name}",$ROOT,$CONFIG["realm"]);
$HTML_HEADER=<<<EOF
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<head>
<meta content='text/html; charset={$CONFIG['encoding']}' http-equiv='Content-Type'>
<title>{$realm} - Edit All in One Page!</title>
<style>
body,td {font-size:12px; color:#000000; background-color:#F1F1F1; margin-left:0px; margin-top:0px; margin-right:0px; margin-bottom:0px;
scrollbar-face-color:#F1F1F1; scrollbar-highlight-color:#F1F1F1; scrollbar-shadow-color:#F1F1F1;
scrollbar-3dlight-color:#FFC080; scrollbar-arrow-color:#000000; scrollbar-track-color:#F1F1F1;
scrollbar-darkshadow-color:#FFC080; scrollbar-base-color:#F1F1F1;}

input {font-size:12px; height:18px; vertical-align:middle; border: solid 1px #000000; }
input.box {font-size:12px; vertical-align:middle; height:18px; border: solid 0px #000000;}
select {font-size:12px; height:18px; vertical-align:middle; border: solid 0px #000000; vertical-align:middle; line-height:12px; font-family:arial}

a {color:#0000FF; text-decoration:none;}
a:hover {color:#FF3300; text-decoration:underline;}
a.gray {color:#999999; text-decoration:none}
a.gray:hover {color:#FF4C00; text-decoration:underline}

p {margin-left:2em; font-size:12px; line-height:130%; margin-top:12px; margin-bottom:12px; text-align:center;}
</style>
</head>
EOF;

switch ($do) {
	case "list" 	 : $dir=(!isset($_GET["dir"]))? $CONFIG["start_dir"]:$_GET["dir"];
					   showFileList($dir, $CONFIG["nLayer"], true); return;
	case "left_frame": showLeftFrame(); return;
	case "main_frame": showMainFrame(); return;
	case "edit"      : showEditFile(); return;
	case "save"      : responseSaveFile(); return;
	case "login"     : showLogin(); return;
	case "verify"    : showVerify(); return;
	case "logout"    : showLogout(); return;
	case "frame_set" : showFrameSet(); return;
	case "image"     : responseImageFile(); return;
	case "config"    : editConfig(); return;
	case "modify"    : modifyConfig(); return;
	case "download"  : downloadFile(); return;
	case "add"       : newFile(); return;
	case "paste"     : pasteFile(); return;
	case "rename"    : renameFile(); return;
	case "remove"    : deleteFile(); return;
	case "upload"    : uploadFile(); return;
	case "get_news"  : getNews(); return;
	default          : return;
}

function showAuthorize($fileType="HTML")
{
	global $CONFIG, $AUTH_USER, $auth, $realm;
	if ($CONFIG["auth_type"]=="none")
		return;
	elseif ($CONFIG["auth_type"]=="HTTP") {
		session_start();
		if (session_is_registered("auth") && $auth===true)
			return;
		elseif ($fileType=="HTML")
			if (isset($_SERVER["PHP_AUTH_USER"]) && $AUTH_USER["password"]==md5($_SERVER['PHP_AUTH_PW']) && $AUTH_USER["username"]==$_SERVER["PHP_AUTH_USER"]) {
				session_register("auth");
				$auth=true;
				return;
			}
			else {
				header('WWW-Authenticate: Basic realm="'.$realm.'"');
				header('HTTP/1.0 401 Unauthorized');
				print "��Ǹ���������벻��ȷ��������û��ʹ�ô������Ȩ�ޡ�����ϵ<a href='mailto:".$AUTH_USER["email"]."'>".$AUTH_USER["email"]."</a>";
				exit();
			}
		elseif ($fileType=="XMLHTTP") {
			header("Content-type: text/xml");
			print "<?xml version='1.0' encoding='{$CONFIG['encoding']}'?>\r\n<Error level='warning' message='δ��Ȩ���ʻ�Ự��ʱ�������µ�¼'/>\r\n";
		}
		else if ($fileType=="EMBED")
			print "����δ��Ȩ";
	}
	elseif ($CONFIG["auth_type"]=="cookie") {
		if ($AUTH_USER["password"]==$_COOKIE["_editone_php_hash"] && $AUTH_USER["username"]==$_COOKIE["_editone_php_user"])
			return;
		elseif ($fileType=="HTML")
			showLogin();
		elseif ($fileType=="XMLHTTP") {
			header("Content-type: text/xml");
			print "<?xml version='1.0' encoding='{$CONFIG['encoding']}'?>\r\n<Error level='warning' message='δ��Ȩ���ʻ�Ự��ʱ�������µ�¼'/>\r\n";
		}
		else if ($fileType=="EMBED")
			print "����δ��Ȩ";
	}
	elseif ($CONFIG["auth_type"]=="session") {
		session_start();
		if (session_is_registered("auth") && $auth===true)
			return;
		elseif ($fileType=="HTML")
			showLogin();
		elseif ($fileType=="XMLHTTP") {
			header("Content-type: text/xml");
			print "<?xml version='1.0' encoding='{$CONFIG['encoding']}'?>\r\n<Error level='warning' message='δ��Ȩ���ʻ�Ự��ʱ�������µ�¼'/>\r\n";
		}
		else if ($fileType=="EMBED")
			print "����δ��Ȩ";
	}
	exit();
}

function showLogin($message="")
{
	global $HTML_HEADER, $CONFIG;
	print <<<EOF
$HTML_HEADER
<body>
<p align='center'>
<form name='F' action='?do=verify' method='post'>
<table border='0' width='95%' id='table1'>
	<tr>
		<td width='100'>��</td>
		<td><font color='#FFC080' style='font-size:14px; font-family:arial black'>EditONE</font>  ����ݡ������㡢����<br>{$CONFIG['version']} <a class='gray' href='http://asp99.cn/' target='_blank'>�������°汾</a>&nbsp; <a class='gray' href='http://asp99.cn' target='_blank'>�����Bug����</a>&nbsp; <a class='gray' href='http://asp99.cn' target='_blank'>ȫ�ܿռ�</a><font color='#999999' style='font-size:12px'>����Ȩ������</font></td>
	</tr>
	<tr>
		<td>��</td>
		<td>��</td>
	</tr>
	<tr>
		<td>��</td>
		<td><p style='color:red'>{$message}</p></td>
	</tr>
	<tr>
		<td align='right'>�û�����</td>
		<td><input type='text' name='username' style='width:120px'> ����Ӣ���ַ����ɣ����Ȳ�����16�����֡�</td>
	</tr>
	<tr>
		<td align='right'>���룠��</td>
		<td><input type='password' name='password' style='width:120px'> ���Ȳ�С��5��������20��<script>document.F.username.focus()</script></td>
	</tr>
	<tr>
		<td>��</td>
		<td>��</td>
	</tr>
	<tr>
		<td>��</td>
		<td><input type='submit' value='�� ¼'></td>
	</tr>
	<tr>
		<td>��</td>
		<td>��</td>
	</tr>
	<tr>
		<td align='right'>ʹ��˵����</td>
		<td>��</td>
	</tr>
	<tr>
		<td>��</td>
		<td>1. EditONE�ʺ�վ����ʹ���е���ҳ�����ٵı༭�����Ժ�ά������������ʵ����޸Ŀ��ܻ�ʹ��ҳ���ʳ������⣬��������������޸Ĺ�����վ�ļ����ر�����һ����ϰ�ߡ�</td>
	</tr>
	<tr>
		<td>��</td>
		<td>2. EditONE֧��Internet Explorer 6.0���ϻ����IE�ں˵ġ�FireFox 1.5���ϻ����gecko�ں˵��������</td>
	</tr>
	<tr>
		<td>��</td>
		<td>3. ��ʼ��¼�û�Ϊ<b>admin</b>����ʼ����Ϊ<b>admin</b>����װ����������¼���ڡ� ���� �����޸��û��������롣�޸��ļ���Ϊһ��ֻ���Լ���֪����������һ���ð취��</td>
	</tr>
	<tr>
		<td>��</td>
		<td>4. ��Ȩ�����������������������ʹ�ô����Ӧ���ڷ���ҵ��;��δ������ȫ�ܿռ����������ٴη�����Դ������޸ĺ󷢱�����ȫ�ܿռ䲻��ʹ������ʹ�ô����������κκ�����������Bug���µ�������ʧ���κ����Ρ���ҵ����;������֧�֡�ȥ�����Ͱ�Ȩ��־�����ܶ��������ѡ�</td>
	</tr>
	<tr>
		<td>��</td>
		<td>  </td>
	</tr>
	<tr>
		<td colspan=2><iframe src="http://editone.gaya.cn/ad1.php" scrolling=no frameborder=0 style="width:100%; height:100px;"></iframe></td>
	</tr>
</table>
</form>
</p>
</body>
</html>
EOF;
}

function showVerify()
{
	global $PAGE, $AUTH_USER, $CONFIG;
	if ($AUTH_USER["password"]==md5($_REQUEST["password"]) && $AUTH_USER["username"]==$_REQUEST["username"]) {
		if($CONFIG["auth_type"]=="cookie") {
			setcookie("_editone_php_user", $AUTH_USER["username"], time()+$CONFIG["cookie_life"]*60);
			setcookie("_editone_php_hash", $AUTH_USER["password"], time()+$CONFIG["cookie_life"]*60);
		}
		else {
			session_start();
			session_register("auth");
			$auth=true;
		}
		print "<script>this.location.replace('".$PAGE."');</script>";
	}
	else
		showLogin("���벻��ȷ�����������롣");
}

function showLogout()
{
	global $AUTH_USER, $CONFIG;
	if ($CONFIG["auth_type"]=="cookie")	{
		setcookie("_editone_php_user", "", time()-31536000);
		setcookie("_editone_php_hash", "", time()-31536000);
	}
	else {
		session_start();
		session_unregister("auth");
	}
	showLogin("�����˳���Cookies��������Ự����ֹ����л����ʹ�á�");
}

function showFrameSet()
{
	global $HTML_HEADER, $CONFIG;
	showAuthorize();
	print <<<EOF
$HTML_HEADER
<frameset title="����ҷ�������Ŀ¼����ȣ�����˫���ر�/��ʾĿ¼��" name='FrameSet' cols='180,*' frameborder='YES' border='2' bordercolor='#DDDDDD' framespacing='3' onfocus='this.mainFrame.window.focus()'>
  <frame src='?do=left_frame' name='leftFrame'>
  <frame src='?do=main_frame' name='mainFrame' scrolling='no'>
</frameset>
<noframes><body bgcolor='#FFFFFF' text='#000000'>
��ʹ��֧��Frame���������
</body></noframes>
</html>
EOF;
}

function showLeftFrame()
{
	global $HTML_HEADER, $CONFIG, $ROOT, $PAGE;
	showAuthorize();
	$AbsPath=getRealPath($CONFIG["start_dir"]);
	print <<<EOF
$HTML_HEADER
<style>
div.body {color:#000000; background-color:#F1F1F1; font-size:12px; margin-left:6px; margin-top:6px; margin-right:0px; margin-bottom:5px;}
#menuSwitch {overflow:hidden; height:17px; margin-top:-5px; text-align:center; background-color:#DDDDDD; margin-left:-4px;  vertical-align:bottom; font-size:16px; color:#000000;}
div.xtree {margin-left:-20px;}
.Node {white-space:nowrap}
.Node img {margin-left:2px;}
.Node span {font-family:Verdana; font-size:12px; height:17px; border:0px solid #E0E0E0; padding:2;}
.Node span a {color:#000000; text-decoration:none}
input.selector {display:none}
span.focus {background-color:#3A6EA5; border:1px solid #333333;font-family:Verdana; font-size:12px; height:17px;}
span.focus a {color:#FFFFFF; text-decoration:none}
.menu {border:1px solid #0000FF; background-color:#FFFFFF;}
.look {text-weight:bold;}
.load {margin:-3px -4px; color:#000000; background-color:#E0E0E0; filter:alpha(opacity=50)}
.container {padding-left:15px;}
</style>
<script language="javascript">
var _isIE=(window.navigator.appName.toLowerCase().indexOf("microsoft")>=0);
var _isFF=(window.navigator.userAgent.toLowerCase().indexOf("firefox")>=0);
var _isNS=(window.navigator.appName.toLowerCase().indexOf("netscape")>=0);
var _I=null; //UltraTree instance

function AJAX(URL)
{
	this.debug=false;
	this.obj=_isIE?new ActiveXObject("Microsoft.XMLHTTP"):new XMLHttpRequest();
	this.XML=null;
	this.error=null;
	this.remote=URL;
	this.text=null;
}
AJAX.prototype.get=function(isSync)
{
	return this.post(null,isSync,true);
}
AJAX.prototype.post=function(message,isSync,isGet)
{
	try {
		this.XML=null;
		var ajax=this;
		with (this.obj) {
			onreadystatechange=function(){
				if(readyState==4) {
					if(status==200) {
						if(ajax.debug) alert("responseText:\\r\\n"+responseText+"\\r\\nContent-Type:"+getResponseHeader("Content-Type")+"\\r\\nresponseXML:"+typeof(responseXML));
						ajax.XML=responseXML;
						ajax.text=responseText;
					}
					else
						ajax.error=new Error("����HTTP����״̬��"+status);
					if(typeof(ajax.onload)=="function")
						ajax.onload(ajax.error==null);
				}
			}
			open((isGet?"GET":"POST"),this.remote+(this.remote.indexOf("?")>=0? "&":"?")+"tmp="+Math.random(),(isSync?false:true));
			if(!(isGet==true)) {
				setRequestHeader("Content-Length", message.length);
				setRequestHeader("Content-Type", "application/x-www-form-urlencoded; charset:UTF-8");
			}
			send(message);
		}
	}
	catch (E) {
		this.error=E;
	}
	return this.XML;
}
AJAX.prototype.async=function(obj)
{
	if(typeof(obj.onload)=="function")
		obj.onload(this.error==null);
	if(typeof(obj.addon)=="function")
		obj.addon(this.error==null);
	obj.addon=null;
}

function UltraTree(DIV,suffix,sort,order,imageFile)
{
	_I=this;
	this.debug=false;
	this.out=DIV;
	this.URI=suffix;
	this.XML=[];
	this.AJAX=null;
	this.status=[];
	this.sort=sort; //�б�����˳��
	this.order=order; //�б�����
	this.focus=null; //��ǰ���ڵ�
	this.objCopy=null;
	this.renaming=false;
	this.adding=null;
	this.uploading=false;
	this.img=typeof(imageFile)=="undefined"? "":imageFile;
}
UltraTree.prototype.data=function(ID,xpath,isAll)
{
	if(_isIE)
		return this.XML[ID]? (isAll?this.XML[ID].documentElement.selectNodes(xpath):this.XML[ID].documentElement.selectSingleNode(xpath)):null;
	else {
		var xpe=new XPathEvaluator();
		var nsResolver=xpe.createNSResolver(this.XML[ID].ownerDocument==null?this.XML[ID].documentElement:this.XML[ID].ownerDocument.documentElement);
		var result=xpe.evaluate(xpath,this.XML[ID],nsResolver,0,null);
		var nodes=[];
		while (res=result.iterateNext())
			nodes.push(res);
		if(!nodes||nodes.length<1) return null;
		return isAll?nodes:nodes[0];
	}
}
UltraTree.prototype.attrib=function(xpath,ID,key)
{
	if(typeof(ID)=="undefined")
		ID=this.node(this.focus);
	obj=this.data(ID,xpath);
	if(obj==null)
		return null;
	else if(typeof(key)!="undefined")
		return obj.getAttribute(key);
	else {
		var n=xpath.lastIndexOf('/');
		var name=xpath.substring(n+1),tag=null;
		var value=_isIE?obj.xml.substring(name.length+2,obj.xml.length-name.length-3):obj.textContent;
		if(xpath.substr(n+1)=="CloseTag")
			return value;
		xpath=xpath.substring(0,n+1)+"CloseTag";
		tag=this.attrib(xpath,ID);
		if (value.indexOf("<![CDATA[")==0)
			value=value.substring(9,value.length-3);
		if (tag!=null)
			value=value.replace(new RegExp("\\\\]\\\\]"+tag+"\\>","ig"),"]]>");
		return value;
	}
}
UltraTree.prototype.node=function(obj,key)
{
	var path=obj.parentNode.parentNode.id;
	var n=path.lastIndexOf("/"), nodeId=path.substr(n+1), parent=path.substring(0,n>0? n:n+1);
	if(typeof(key)=="undefined")
		return path;
	else
		return this.attrib("/Node/Node[@id='"+nodeId+"']",parent,key);
}
UltraTree.prototype.select=function(node)
{
	var span;
	if(this.focus!=null&&this.focus.tagName!="DIV")
		this.focus.parentNode.className="Node";
	this.focus=node;
	if(node!=null&&node.tagName!="DIV") {
		node.parentNode.className="focus";
		node.blur();
	}
	return node;
}
UltraTree.prototype.report=function(message,type)
{
	if (this.intervalHandler)
		 clearInterval(this.intervalHandler);
	if (this.timeoutHandler)
		 clearTimeout(this.timeoutHandler);
	switch (type) {
		case "WAIT":
			parent.window.status=message;
			this.intervalHandler=setInterval(function(){parent.window.status=parent.window.status+".";}, 500);
			break;
		case "AUTO":
			parent.window.status=message;
			this.timeoutHandler=setTimeout(function(){parent.window.status="";}, 5000);
			break;
		case "ALERT":
			alert(message);
			break;
		default:
			parent.window.status=message;
	}
}
UltraTree.prototype.findXY=function(obj)
{
	var x=0,y=0;
	while(obj!=null) {
		x+=obj.offsetLeft-obj.scrollLeft;
		y+=obj.offsetTop-obj.scrollTop;
		obj=obj.offsetParent;
	}
	return {x:x,y:y};
}
UltraTree.prototype.dispatch=function(node)
{
	this.select(node);
	nodeType=this.node(node,"type");
	cmd=CM[nodeType][0][1];
	eval(cmd);
	return false;
}
UltraTree.prototype.toggle=function(node)
{
	var path=this.node(node), DIV=node.parentNode.parentNode;
	if((this.status[path]&1)==0) {
		var el=this.data(DIV.parentNode.parentNode.id,"/Node/Node[@id='"+this.node(node,"id")+"']");
		if (el.firstChild) { // ����Ŀ¼����
			var doc=_isIE?new ActiveXObject("Microsoft.XMLDOM"):document.implementation.createDocument("text/xml", "", null);
			if (!_isIE)
				el.removeChild(el.firstChild);
			doc.appendChild(el.firstChild);
			this.XML[path]=doc;
			this.render(DIV,this.sort,this.order);
			this.status[DIV.id]|=3;
		}
		else
			this.load(DIV,this.sort,this.order);
	}
	else {
		if (DIV.lastChild.tagName!="DIV") {
			this.render(DIV,this.sort,this.order);
			this.status[path]&=0xFFFD;
		}
		DIV.lastChild.style.display=(this.status[path]&2)==0?"block":"none";
		DIV.childNodes[1].src=this.img+"?do=image&file="+((this.status[path]&2)==0?"folder_open":"folder_close")+".gif";
		this.status[path]+=(this.status[path]&2)==0?-2:2;
	}
	return false;
}
UltraTree.prototype.load=function(node,sort,order)
{
	var ajax=new AJAX(this.URI+"do=list&dir="+escape(node.id));
	ajax.onload=function() {
		_I.XML[node.id]=this.XML;
		this.async(_I);
	}
	this.onload=function() {
		node.removeChild(node.lastChild);
		this.render(node,sort,order);
		this.status[node.id]|=3;
	}
	var DIV=document.createElement("DIV");
	DIV.className="container";
	DIV.innerHTML="<font color='red'>���ڼ���...</font>";
	node.appendChild(DIV);
	ajax.get();
}
UltraTree.prototype.render=function(node,sort,order)
{
	var DIV=document.createElement("DIV"), path=node.id;
	DIV.className="container";
	if(order=="descending")
		if(_isIE)
			DIV.innerHTML=this.XML[path].transformNode(this.getXslTP(sort,order,path,true));
		else
			DIV.appendChild(this.getXslTP(sort,order,path,true).transformToFragment(this.XML[path],document));
	if(_isIE)
		DIV.innerHTML=DIV.innerHTML+this.XML[path].transformNode(this.getXslTP(sort,order,path));
	else
		DIV.appendChild(this.getXslTP(sort,order,path).transformToFragment(this.XML[path],document));
	if(order!="descending")
		if(_isIE)
			DIV.innerHTML=DIV.innerHTML+this.XML[path].transformNode(this.getXslTP(sort,order,path,true));
		else
			DIV.appendChild(this.getXslTP(sort,order,path,true).transformToFragment(this.XML[path],document));
	node.appendChild(DIV);
	node.childNodes[1].src=this.img+"?do=image&file=folder_open.gif";
}
UltraTree.prototype.getXslTP=function(sort,order,path,dirOnly)
{
	var s2;
	path+=path.length==0||path.charAt(path.length-1)=="/"? "":"/";
	if(order!="ascending"&&order!="descending")
		s2="";
	else if(sort=='name')
		s2="	<xsl:sort order='"+order+"' select='@id' data-type='text'/>";
	else if(sort=='time')
		s2="	<xsl:sort order='"+order+"' select='@time' data-type='number'/>";
	else if(sort=='size')
		s2="	<xsl:sort order='"+order+"' select='@length' data-type='number'/>";
	else if(sort=='type')
		s2="	<xsl:sort order='"+order+"' select='concat(@type,@id)' data-type='text'/>";
	else
		s2="";
	var s1="<?xml version='1.0' encoding='UTF-8'?>\\r\\n"+
"<xsl:stylesheet version='1.0' xmlns:xsl='http://www.w3.org/1999/XSL/Transform' xmlns:msxsl='urn:schemas-microsoft-com:xslt' xmlns:user='http://editone.gaya.cn'>\\r\\n"+
"<xsl:template match='/'>\\r\\n"+
"<xsl:apply-templates select='Node' />\\r\\n"+
"</xsl:template>\\r\\n"+
"\\r\\n"+
"<xsl:template match='Node'>\\r\\n"+
"<xsl:for-each select='./Node[@type"+(dirOnly?"":"!")+"=\\"dir\\"]'>";
	var s3="	<div class='Node'>\\r\\n"+
"		<xsl:attribute name='id'>"+path+"<xsl:value-of select='@id'/></xsl:attribute>\\r\\n"+
"		<input type='checkbox' name='itemSel' class='selector'>\\r\\n"+
"		<xsl:attribute name='value'>"+path+"<xsl:value-of select='@id'/></xsl:attribute>\\r\\n"+
"		</input>\\r\\n"+
"		<img align='absmiddle'>\\r\\n"+
"			<xsl:attribute name='src'>\\r\\n"+
"			<xsl:choose>\\r\\n"+
"				<xsl:when test='string(@type)=\\"dir\\"'>"+this.img+"?do=image&amp;file=folder_close.gif</xsl:when>\\r\\n"+
"				<xsl:otherwise>"+this.img+"?do=image&amp;file=<xsl:value-of select='@type'/>.gif</xsl:otherwise>\\r\\n"+
"			</xsl:choose>\\r\\n"+
"			</xsl:attribute>\\r\\n"+
"		</img>\\r\\n"+
"		<span><a>\\r\\n"+
"			<xsl:attribute name='href'>javascript:void('<xsl:value-of select='@id'/>')</xsl:attribute>\\r\\n"+
"			<xsl:attribute name='onClick'>javascript:return _I.dispatch(this)</xsl:attribute>\\r\\n"+
"			<xsl:attribute name='onMouseOver'>javascript:return _I.showDetail(this)</xsl:attribute>\\r\\n"+
"			<xsl:attribute name='onMouseOut'>javascript:return _I.closeDetail(this)</xsl:attribute>\\r\\n"+
"			<xsl:choose>\\r\\n"+
"				<xsl:when test='@name'><xsl:value-of select='@name'/></xsl:when>\\r\\n"+
"				<xsl:otherwise><xsl:value-of select='@id'/></xsl:otherwise>\\r\\n"+
"			</xsl:choose>\\r\\n"+
"		</a></span>\\r\\n"+
"	</div>\\r\\n"+
"</xsl:for-each>\\r\\n"+
"</xsl:template>\\r\\n"+
"\\r\\n"+
"</xsl:stylesheet>";
	if(_isIE){
		var XSL=new ActiveXObject("Microsoft.XMLDom");
		XSL.loadXML(s1+s2+s3);
		return XSL;
	}
	else{
		var parser=new DOMParser();
		var XSL=parser.parseFromString(s1+s2+s3, "application/xml");
		var XSLTP=new XSLTProcessor();
		XSLTP.importStylesheet(XSL);
		return XSLTP;
	}
}
UltraTree.prototype.sortBy=function(type)
{
	this.order=(this.sort==type)?(this.order=="ascending"?"descending":"ascending"):"ascending";
	this.sort=type;
	var DIV=this.focus.parentNode.parentNode;
	DIV.removeChild(DIV.lastChild);
	this.render(DIV,type,this.order);
}
UltraTree.prototype.leftClick=function(evt)
{
	var EvtSrc=_isIE?window.event.srcElement.name:evt.target.name;
	if (EvtSrc!="nodeId"&&EvtSrc!="fileUpload") {
		if(this.renaming)
			this.modifyId();
		else if(this.adding!=null)
			this.confirmId();
		else if(this.uploading)
			this.uploadFile();
	}
	document.getElementById("//menu").style.display="none";
	return true;
}
UltraTree.prototype.rightClick=function(evt)
{
	if(evt==null)
		evt=window.event;//IE
	var EvtSrc=_isIE?window.event.srcElement.name:evt.target.name;
	if (EvtSrc!="nodeId"&&EvtSrc!="fileUpload") {
		if (this.renaming)
			this.modifyId();
		else if (this.adding!=null)
			this.confirmId();
		else if (this.uploading)
			this.uploadFile();
	}
	document.getElementById("//detail").style.display="none";
	var target=_isIE?evt.srcElement:evt.target, menu;
	target.blur();
	this.select(target);
	menu=target.tagName=="A"?CM[this.node(target,"type")]:CM["body"];
	if(this.objCopy!=null)
		menu[3][3]=true;
	str="<table border='0' width='100%' cellspacing='1'>";
	for(var n in menu)
		if (menu[n].length>3&&menu[n][3]==false)
			str+="<tr><td>"+menu[n][2]+"</td><td><font color='gray'>"+menu[n][0]+"</font></td></tr>";
		else
			str+="<tr><td>"+menu[n][2]+"</td><td><a href="+menu[n][1]+">"+menu[n][0]+"</a></td></tr>";
	str+="</table>";
	document.getElementById("//menu").innerHTML=str;
	document.getElementById("//menu").style.left=document.body.scrollLeft+46;
	document.getElementById("//menu").style.top=document.body.scrollTop+evt.clientY+10;
	document.getElementById("//menu").style.display="block";
	return false;
}
UltraTree.prototype.showDetail=function(obj)
{
	var attribs = this.data(obj.parentNode.parentNode.parentNode.parentNode.id,"/Node/Attrib",true), str="";
	for(var i=0;i<attribs.length;i++) {
		var attrib=attribs[i].getAttribute("id"), name=attribs[i].getAttribute("name"), type=attribs[i].getAttribute("type"), unit=attribs[i].getAttribute("unit");
		var value=this.node(obj,attrib);
		if (value==null)
			continue;
		if(type=="size")
			if(value>1024&&value<1048576)
				value=Math.floor(value/102.4)/10+" K&nbsp; &nbsp; &nbsp;("+value+" "+unit+")";
			else if (value>=1048576)
				value=Math.floor(value/104857.6)/10+" M&nbsp; &nbsp; &nbsp;("+value+" "+unit+")";
			else
				value=value+" "+unit;
		else if(type=="time") {
			D=new Date(value*1000);
			value=D.getFullYear()+"��"+(D.getMonth()+1)+"��"+D.getDate()+"�� "+D.getHours()+":"+D.getMinutes()+":"+D.getSeconds();
		}
		str+=name+": "+value+"<br>";
	}
	var DIV=document.getElementById("//detail");
	DIV.style.left=document.body.scrollLeft+25;
	DIV.style.top=document.body.scrollTop+this.findXY(obj).y+35;
	document.getElementById("//detail").innerHTML=str;
	DIV.style.display="block";
	return false;
}
UltraTree.prototype.closeDetail=function()
{
	document.getElementById("//detail").style.display="none";
	return false;
}
UltraTree.prototype.copy=function()
{
	this.objCopy=this.focus;
}
UltraTree.prototype.paste=function(inDir)
{
	var nodePath=this.node(this.objCopy), dir=this.focus.parentNode.parentNode.id, nodeType=this.node(this.objCopy,"type");
	var ajax=new AJAX(this.URI+"do=paste&node="+escape(nodePath)+"&dir="+escape(dir)+"&type="+nodeType);
	ajax.onload=function() {
		_I.XML["//"]=this.XML;
		this.async(_I);
	}
	this.onload=function() {
		var title="ճ��"+(nodeType=="dir"? "Ŀ¼":"�ļ�");
		if(this.attrib("/File/Status","//")!="true") {
			if (ajax.error)
				this.report(title+"����\\n\\n������Ϣ��\\n"+ajax.error.description, "ALERT");
			else {
				if (this.attrib("/File/Reason","//")==null)
					this.report(title+"����\\n\\n����δ��ʽ����Ϣ��\\n"+ajax.text, "ALERT");
				else
					this.report(title+"����\\n\\nԭ��\\n"+this.attrib("/File/Reason","//")+"\\n\\n���飺\\n"+this.attrib("/File/Advice","//"), "ALERT");
			}
			obj.parentNode.parentNode.parentNode.removeChild(obj.parentNode.parentNode);
			this.report(title+"����", "AUTO");
		}
		else {
			var resumeNode=function(target) {
				var name=_I.attrib("/File/Name","//");

				path=dir+(dir.length==0||dir.charAt(dir.length-1)=="/"? "":"/")+name;
				var DIV=document.createElement("DIV");

				DIV.id=path;
				DIV.className="Node";
				DIV.innerHTML="<input type=checkbox value='"+path+"' name='itemSel' class='selector'><img src='"+_I.img+"?do=image&amp;file="+(nodeType=="dir"? "folder_close":nodeType)+".gif' align=absMiddle><span><a href=\\"javascript:void('"+name+"')\\" onClick='javascript:return _I.dispatch(this)' onMouseOver='javascript:return _I.showDetail(this)' onMouseOut='javascript:return _I.closeDetail(this)'>"+name+"</a></span>";
				target.parentNode.insertBefore(DIV,target);
				_I.XML[path]=ajax.XML;
				_I.select(DIV.childNodes[2].firstChild);
				var node=_I.XML[dir].createElement("Node");
				node.setAttribute("type",nodeType);
				node.setAttribute("time",_I.attrib("/File/Time"));
				node.setAttribute("id",name);
				if(nodeType!="dir")
					node.setAttribute("length",_I.node(_I.objCopy,"length"));
			 	var parent=_I.data(dir,"/Node");
			 	parent.appendChild(node);
			 	_I.report(title+"�� "+nodePath+" ��Ϊ�� "+path+" ���ɹ���", "AUTO");
			}
			if(inDir) {
				if((this.status[dir]&1)==0)
					this.toggle(this.focus);
				else {

					if((this.status[dir]&2)==0)
						this.toggle(this.focus);
					resumeNode(this.focus.parentNode.parentNode.lastChild.firstChild);
				}
			}
			else
				resumeNode(this.focus);
			_I.objCopy=null;
		}
	}
	ajax.get();
	return true;
}
UltraTree.prototype.remove=function()
{
	var path=this.node(this.focus), nodeId=this.node(this.focus,"id"), nodeType=this.node(this.focus,"type");
	var warning=nodeType=="dir"? "��ȷ��Ҫɾ��Ŀ¼�� "+nodeId+" ����\\n\\n���棺\\n��ɾ������һ��Ŀ¼�����ɾ����Ŀ¼��Ҳ��ɾ����Ŀ¼�µ������ļ���Ŀ¼��":"��ȷ��Ҫɾ���ļ��� "+nodeId+" ����";
	if (!confirm(warning))
		return false;
	var ajax=new AJAX(this.URI+"do=remove&node="+escape(path)+"&type="+nodeType);
	ajax.onload=function() {
		_I.XML[path]=this.XML;
		this.async(_I);
	}
	this.onload=function() {
		if (this.attrib("/File/Status")!="true") {
			if (ajax.error)
				this.report("ɾ���ļ�����\\n\\n������Ϣ��\\n"+ajax.error.description, "ALERT");
			else {
				if (this.attrib("/File/Reason")==null)
					this.report("ɾ���ļ�����\\n\\n����δ��ʽ����Ϣ��\\n"+ajax.text, "ALERT");
				else
					this.report("ɾ���ļ�����\\n\\nԭ��\\n"+this.attrib("/File/Reason")+"\\n\\n���飺\\n"+this.attrib("/File/Advice"), "ALERT");
			}
			this.report("ɾ���ļ�����", "AUTO");
		}
		else {
		 	var dir=this.focus.parentNode.parentNode.parentNode.parentNode.id, parent=this.data(dir,"/Node");
		 	parent.removeChild(this.data(dir,"/Node/Node[@id='"+nodeId+"']"));
			this.focus.parentNode.parentNode.parentNode.removeChild(this.focus.parentNode.parentNode);
			this.focus=null;
			this.report("ɾ���ļ��� "+nodeId+" ���ɹ���", "AUTO");
		}
	}
	ajax.get();
}

UltraTree.prototype.add=function(type,inDir)
{
	var newNode=function(target) {
		var DIV=document.createElement("DIV");
		DIV.className="Node";
		DIV.innerHTML="<input type=checkbox value='' name='itemSel' class='selector'><img src='"+_I.img+"?do=image&amp;file="+(type=="dir"? "folder_close":"default")+".gif' align=absMiddle><span><input id='//nodeId' name='nodeId' type='text' value='"+(type=="dir"? "�½�Ŀ¼��":"�½��ļ���")+"' size='16' style='margin-left:13px'></span>";
		target.parentNode.insertBefore(DIV,target);
		with(document.getElementById("//nodeId")) {
			onkeyup=function(evt){if(window.event)evt=window.event;if(_I.adding!=null&&evt.keyCode==13)_I.confirmId();}
			focus();
			select();
		}
	}
	this.adding=type;
	if(inDir) {
		var dir=this.node(this.focus);
		if((this.status[dir]&1)==0) {
			this.addon=function(){newNode(this.focus.parentNode.parentNode.lastChild.firstChild);}
			this.toggle(this.focus);
		}
		else {
			if((this.status[dir]&2)==0)
				this.toggle(this.focus);
			newNode(this.focus.parentNode.parentNode.lastChild.firstChild);
		}
	}
	else
		newNode(this.focus);
}
UltraTree.prototype.confirmId=function()
{
	var obj=document.getElementById("//nodeId"), newId=obj.value, nodeType=this.adding;
	this.focus=obj;
	var dir=obj.parentNode.parentNode.parentNode.parentNode.id, span=obj.parentNode;
	var path=span.parentNode.id=dir+(dir.length==0||dir.charAt(dir.length-1)=='/'?"":"/")+newId;
	span.parentNode.childNodes[0].value=span.parentNode.id;
	var encoding=parent.mainFrame.document.M.encoding.value;
	var ajax=new AJAX(this.URI+"do=add&node="+escape(path)+"&type="+nodeType+"&encoding="+encoding);
	ajax.onload=function() {
		_I.XML[path]=this.XML;
		this.async(_I);
	}
	this.onload=function() {
		var title="�½�"+(nodeType=="dir"? "Ŀ¼":"�ļ�");
		if(this.attrib("/File/Status")!="true") {
			if (ajax.error)
				this.report(title+"����\\n\\n������Ϣ��\\n"+ajax.error.description, "ALERT");
			else {
				if (this.attrib("/File/Reason")==null)
					this.report(title+"����\\n\\n����δ��ʽ����Ϣ��\\n"+ajax.text, "ALERT");
				else
					this.report(title+"����\\n\\nԭ��\\n"+this.attrib("/File/Reason")+"\\n\\n���飺\\n"+this.attrib("/File/Advice"), "ALERT");
			}
			obj.parentNode.parentNode.parentNode.removeChild(obj.parentNode.parentNode);
			this.report(title+"����", "AUTO");
		}
		else {
			span.removeChild(obj);
			span.innerHTML="<a href=\\"javascript:void('"+newId+"')\\" onClick='javascript:return _I.dispatch(this)' onMouseOver='javascript:return _I.showDetail(this)' onMouseOut='javascript:return _I.closeDetail(this)'>"+newId+"</a>";
			this.focus=span.firstChild;
			nodeType=this.attrib("/File/Type");
			span.parentNode.childNodes[1].src=_I.img+"?do=image&file="+(nodeType=="dir"?"folder_close":nodeType)+".gif";
			var node=this.XML[dir].createElement("Node");
			node.setAttribute("type",nodeType);
			node.setAttribute("time",this.attrib("/File/Time"));
			node.setAttribute("id",newId);
			if(nodeType!="dir")
				node.setAttribute("length",this.attrib("/File/Length"));
		 	var parent=this.data(dir,"/Node");
		 	parent.appendChild(node);

			this.adding=null;
			this.report(title+"�� "+newId+" ���ɹ���", "AUTO");
		}
		this.focus=null;
	}
	ajax.post(nodeType=="dir"?"DIR":parent.mainFrame.editorText.value);
}
UltraTree.prototype.rename=function()
{
	var obj=this.focus, path=this.node(obj), oldId=this.node(obj,"id"), span=obj.parentNode;
	this.select(null);
	span.removeChild(obj);
	span.innerHTML="<input id='//nodeId' name='nodeId' type='text' value='"+oldId+"' size='16' style='margin-left:13px'>";
	this.renaming=true;
	with(document.getElementById("//nodeId")) {
		onkeyup=function(evt){if(window.event)evt=window.event;if(_I.renaming&&evt.keyCode==13)_I.modifyId();}
		focus();
		select();
	}
}
UltraTree.prototype.modifyId=function()
{
	var obj=document.getElementById("//nodeId"), newId=obj.value, nodeType=this.node(obj,"type");
	var path=this.node(obj), oldId=this.node(obj,"id");
	var resumeNode=function(name,type) {
		var span=obj.parentNode;
		span.removeChild(obj);
		span.innerHTML="<a href=\\"javascript:void('"+name+"')\\" onClick='javascript:return _I.dispatch(this)' onMouseOver='javascript:return _I.showDetail(this)' onMouseOut='javascript:return _I.closeDetail(this)'>"+name+"</a>";
		var n=path.lastIndexOf("/"), dir=path.substring(0,n+1);
		_I.focus=span.firstChild;
		_I.renaming=false;
		if (name==oldId)
			return;
		path=dir+name;
	 	span.parentNode.id=path;
	 	dir=n>=0?path.substring(0,n):"";
	 	span.parentNode.childNodes[0].value=path;
	 	if(type=="dir")
	 		type=(this.status[path]&2)!=0?"folder_open":"folder_close";
	 	span.parentNode.childNodes[1].src=_I.img+"?do=image&file="+type+".gif";
	 	with(_I.data(dir,"/Node/Node[@id='"+oldId+"']")) {
	 		setAttribute("type",type);
	 		setAttribute("id",name);
	 	}
	}
	if (newId==oldId) {
		resumeNode(newId);
		return true;
	}
	this.focus=obj;

	var ajax=new AJAX(this.URI+"do=rename&node="+escape(path)+"&name="+escape(newId)+"&type="+nodeType);
	ajax.onload=function() {
		_I.XML[path]=this.XML;
		this.async(_I);
	}
	this.onload=function() {
		var title="������"+(nodeType=="dir"? "Ŀ¼":"�ļ�");
		if(this.attrib("/File/Status")!="true") {
			if (ajax.error)
				this.report(title+"����\\n\\n������Ϣ��\\n"+ajax.error.description, "ALERT");
			else {
				if (this.attrib("/File/Reason")==null)
					this.report(title+"����\\n\\n����δ��ʽ����Ϣ��\\n"+ajax.text, "ALERT");
				else
					this.report(title+"����\\n\\nԭ��\\n"+this.attrib("/File/Reason")+"\\n\\n���飺\\n"+this.attrib("/File/Advice"), "ALERT");
			}
			resumeNode(oldId);
			this.report(title+"����", "AUTO");
		}
		else {
			resumeNode(newId,this.attrib("/File/Type"));
			this.report(title+"�� "+oldId+" ��Ϊ�� "+newId+" ���ɹ���", "AUTO");
		}
	}
	ajax.get();
}
UltraTree.prototype.editFile=function()
{
	var path=this.node(this.focus), nodeType=this.node(this.focus,"type");
	var encoding=parent.mainFrame.document.M.encoding.value;
	var ajax=new AJAX(this.URI+"do=edit&file="+escape(path)+"&type="+nodeType+"&encoding="+encoding);
	ajax.onload=function() {//������ɵĴ������
		_I.XML[path]=this.XML;
		this.async(_I);
	}
	this.onload=function() {
		if(this.attrib("/File/Status")!="true") {
			if (ajax.error)
				this.report("���س���\\n\\n������Ϣ��\\n"+ajax.error.description, "ALERT");
			else {
				if (this.attrib("/File/Reason")==null)
					this.report("���س���\\n\\n����δ��ʽ����Ϣ��\\n"+ajax.text, "ALERT");
				else
					this.report("���س���\\n\\nԭ��\\n"+this.attrib("/File/Reason")+"\\n\\n���飺\\n"+this.attrib("/File/Advice"), "ALERT");
			}
			this.report("�����ļ�����", "AUTO");
		}
		else {
			var content=this.attrib("/File/Content");
			this.report("�����ļ��� "+path+" ���ɹ���", "AUTO");
			if(parent.mainFrame.editMode=="preview")
				parent.mainFrame.switchMode("text");
			if(parent.mainFrame.editMode=="text")
				parent.mainFrame.editorText.value=content;
			else
				parent.mainFrame.editorVisual.SetHTML(content);
			parent.mainFrame.document.getElementById("AutoEncoding").innerHTML=this.attrib("/File/Encoding");
			parent.mainFrame.isNeedSave=false;
		}
	}
	ajax.get();
}
UltraTree.prototype.saveFile=function()
{
	if (this.focus==null)
		return false;
	var path=this.node(this.focus), nodeType=this.node(this.focus,"type");
	var isSuccess=true;
	this.report("���ڱ����ļ� �� "+path+" �� ...", "WAIT");
	var encoding=parent.mainFrame.document.M.encoding.value;
	var ajax=new AJAX(this.URI+"do=save&file="+escape(path)+"&type="+nodeType+"&encoding="+encoding);
	ajax.onload=function() {
		_I.XML[path]=this.XML;
		this.async(_I);
	}
	this.onload=function() {
		if (this.attrib("/File/Status")!="true") {
			if (ajax.error)
				this.report("����ʧ�ܡ�\\n\\n������Ϣ��\\n"+ajax.error.description, "ALERT");
			else {
				if (this.attrib("/File/Reason")==null)
					this.report("����ʧ�ܡ�\\n\\n����δ��ʽ����Ϣ��\\n"+ajax.text, "ALERT");
				else
					this.report("����ʧ�ܡ�\\n\\nԭ��\\n"+this.attrib("/File/Reason")+"\\n\\n���飺\\n"+this.attrib("/File/Advice"), "ALERT");
			}
			this.report("�����ļ�ʧ�ܡ�", "AUTO");
			isSuccess=false;
		}
		else {
			this.report("�ļ��� "+path+" ���ѱ��档", "AUTO");
			parent.mainFrame.isNeedSave=false;
		}
	}
	content=parent.mainFrame.editMode=="html"?parent.mainFrame.editorVisual.GetXHTML():parent.mainFrame.editorText.value;
	ajax.post(content,false);
	return isSuccess;
}
UltraTree.prototype.browseFile=function(type)
{
	if(parent.mainFrame.isNeedSave&&confirm("�����ѱ��޸ġ��Ƿ񱣴棿"))
		this.saveFile();
	if(type=="edit") {
		this.addon=parent.mainFrame.switchMode;
		this.editFile();
		return;
	}
	parent.mainFrame.editorText.value="";
	parent.mainFrame.switchMode("preview");
	if(type=="htm"||type=="pic")
		parent.mainFrame.document.getElementById("//previewer").src=this.node(this.focus);
	else if(type=="media") {
		var filename=this.node(this.focus), str;
		switch (filename.substr(filename.lastIndexOf(".")+1).toLowerCase()) {
			case "mp3": case "mpga": case "wav": case "mid": case "wma":
			str="<object id='media' classid='CLSID:22d6f312-b0f6-11d0-94ab-0080c74c7e95' height='66' width='310'><param name='FileName' value='"+filename+"'><param name='autostart' value='true'><embed src='"+filename+"' type='application/x-mplayer2' pluginspage='http://www.microsoft.com/isapi/redir.dll?prd=windows&amp;sbp=mediaplayer&amp;ar=media&amp;sba=plugin&amp;' height='66' width='310' autostart='1'></object>"; break;
			case "rm":
			str="<object id='media' classid='CLSID:CFCDAA03-8BE4-11cf-B84B-0020AFBBCCFA' height='275' width='316'><param name='controls' value='ImageWindow,StatusBar,ControlPanel'><param name='console' value='Clip1'><param name='autostart' value='true'><param name='LOOP' value='1'><param name='src' value='"+filename+"'><embed src='"+filename+"' type='audio/x-pn-realaudio-plugin' console='Clip1' controls='ImageWindow,ControlPanel,StatusBar' height='275' width='316' autostart='true'></object>"; break;
			case "swf":
			str="<object id='media' classid='CLSID:D27CDB6E-AE6D-11cf-96B8-444553540000' codebase='http://download.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=5,0,0,0' width='400' height='400'><param name=movie value='"+filename+"'><param name=quality value=high><embed src='"+filename+"' quality=high pluginspage='http://www.macromedia.com/shockwave/download/index.cgi?P1_Prod_Version=ShockwaveFlash' type='application/x-shockwave-flash'></embed></object>"; break;
			case "avi": case "mpg": case "wmv": default:
			str="<EMBED src='"+filename+"' HEIGHT='256' WIDTH='314' AutoStart=1></EMBED>";
		}
		with(parent.mainFrame.document.getElementById("//previewer").contentWindow.document) {
			open();
			write(str);
			close();
		}
	}
}
UltraTree.prototype.upload=function()
{
	with(document.getElementById("//loader").contentWindow.document) {
		open();
		write("<html><head><style>body{font-size:12px; color:#000000; background-color:#F1F1F1; margin-left:0px; margin-top:0px; margin-right:0px; margin-bottom:0px;} input {font-size:12px; height:18px}</style></head><body><form id='F' name='F' action='?do=upload' enctype='multipart/form-data' method='post' onSubmit='parent.upload()'><input type='hidden' name='dir' value='"+this.focus.parentNode.parentNode.id+"'><input id='//nodeId' name='nodeId' type='text' value='' size='12'><input type='file' size='1' name='fileUpload' value='' id='//fileUpload' style='width:0px' onChange='nm=this.value;document.getElementById(\\"//nodeId\\").value=nm.substring(nm.lastIndexOf(\\"\\\\\\\\\\")+1,nm.length);'></form></body></html>");
		close();
	}
	var DIV=document.createElement("DIV");
	DIV.id="//nodeUpload";
	DIV.className="Node";
	DIV.innerHTML="<input type=checkbox value='' name='itemSel' class='selector'><img src='"+this.img+"?do=image&amp;file=default.gif' align=absMiddle><span><font color='red'>�����ϴ���...</font></span>";
	this.focus.parentNode.insertBefore(DIV,this.focus);
	var obj=this.findXY(document.getElementById("//nodeUpload"));
	with(document.getElementById("//mobi")) {
		style.left=document.body.scrollLeft+obj.x+20;
		style.top=document.body.scrollTop+obj.y-1;
		style.display="block";
	}
	with(document.getElementById("//loader").contentWindow.document.getElementById("//fileUpload")) {
		focus();
		select();
	}
	this.uploading=true;
}
UltraTree.prototype.uploadFile=function()
{
	this.uploading=false;
	document.getElementById("//mobi").style.display="none";
	var DIV=document.getElementById("//nodeUpload"), loader=document.getElementById("//loader");
	if(loader.contentWindow.document.getElementById("//fileUpload").value=="") {
		DIV.parentNode.removeChild(DIV);
		return false;
	}
	var F=loader.contentWindow.document.getElementById("F");
	var path=F.dir.value+(F.dir.value.length==0||F.dir.value.charAt(F.dir.value.length-1)=="/"?"":"/")+F.nodeId.value;
	F.action=this.URI+"do=upload&name="+escape(F.nodeId.value)+"&path="+escape(path);
	F.submit();
	return true;
}
UltraTree.prototype.uploadReport=function(status,path,type,time,length)
{
	var DIV=document.getElementById("//nodeUpload");
	if(status==true) {
		var n=path.lastIndexOf("/"), name=path.substr(n+1), dir=path.substring(0,n>=0?n:0);
		DIV.id=path;
		DIV.childNodes[0].value=path;
		DIV.childNodes[1].src=this.img+"?do=image&file="+type+".gif";
		DIV.childNodes[2].innerHTML="<a href=\\"javascript:void('"+name+"')\\" onClick='javascript:return _I.dispatch(this)' onMouseOver='javascript:return _I.showDetail(this)' onMouseOut='javascript:return _I.closeDetail(this)'>"+name+"</a>";
		var node=this.XML[dir].createElement("Node");
		node.setAttribute("type",type);
		node.setAttribute("time",time);
		node.setAttribute("id",name);
		node.setAttribute("length",length);
	 	var parent=this.data(dir,"/Node");
	 	parent.appendChild(node);
	}
	else {
		DIV.parentNode.removeChild(DIV);
		alert("�ļ��ϴ�ʧ�ܡ�\\n\\nԭ��"+path+"\\n\\n���飺\\n"+"������:"+type+"; ���������ĵ���");
	}
}
UltraTree.prototype.download=function()
{
	document.getElementById("//loader").src=this.URI+"do=download&file="+escape(this.node(this.focus));
	return false;
}
UltraTree.prototype.runScript=function(obj)
{
	window.open(this.node(this.focus)+"?tmp="+Math.random(),'EditOne_Run','resizable=yes,scrollbars=yes,location=yes,toolbar=yes,menubar=yes,top=0,left=0,status=yes,titlebar=1,fullscreen=yes,directories=yes,channelmode=yes');
}
</script>
<script language="javascript">
var panelOpened=true;
var curColumns;
function switch_menu()
{
	if(panelOpened) {
		curColumns=parent.document.getElementsByTagName("FrameSet").item(0).cols;
		parent.document.getElementsByTagName("FrameSet").item(0).cols="0,*";
		panelOpened=false;
	}
	else {
		parent.document.getElementsByTagName("FrameSet").item(0).cols=curColumns;
		panelOpened=true;
	}
}
parent.document.getElementsByTagName("FrameSet").item(0).style.cursor="move";
parent.document.ondblclick=function(evt){if(parent.window.event)evt=parent.window.event;switch_menu();}

parent.document.onkeydown=function(evt){if(parent.window.event)evt=parent.window.event;return keyHandler(evt);}
document.onkeydown=function(evt){if(window.event)evt=window.event;return keyHandler(evt);}
function keyHandler(event)
{
	if(!event) return true;
	if(event.ctrlKey && event.keyCode==83) { // Ctrl+S
		parent.mainFrame.document.M.save.disabled=true;
		T.saveFile();
		parent.mainFrame.document.M.save.disabled=false;
		return false;
	}
	if(event.keyCode==114) { // F3
		parent.mainFrame.findNext();
		event.keyCode+=1000;
		return false;
	}
	if(event.ctrlKey && event.keyCode==70)	{ // Ctrl+F
		parent.mainFrame.document.M.word_find.focus();
		event.keyCode+=1000;
		return false;
	}
	if(event.ctrlKey && event.keyCode==82)	{ // Ctrl+R
		parent.mainFrame.document.M.word_replace.focus();
		event.keyCode+=1000;
		return false;
	}
	return true;
}

function setCookie(name,value)
{
	var Days=30;
	var exp=new Date();
	exp.setTime(exp.getTime()+Days*24*60*60*1000);
	document.getElementById("/").document.cookie=name+"="+ escape (value)+";expires="+exp.toGMTString();
}
function getCookie(name)
{
	var arr=document.getElementById("/").document.cookie.split("; ");
	for(i=0;i<arr.length;i++)
		if(arr[i].split("=")[0]==name)
			return unescape(arr[i].split("=")[1]);
	return null;
}
function delCookie(name)
{
	var exp=new Date();
	exp.setTime(exp.getTime()-1);
	var cval=getCookie(name);
	if(cval!=null) MainDIV.document.cookie= name+"="+cval+";expires="+exp.toGMTString();
}

var GayaTree=UltraTree;
var CM=[], K;
K="script";
CM[K]=[];
CM[K][CM[K].length]=new Array("�༭","javascript:void(T.editFile(T.focus))","");
CM[K][CM[K].length]=new Array("����","javascript:void(T.download())","");
CM[K][CM[K].length]=new Array("����","javascript:void(T.copy())","");
CM[K][CM[K].length]=new Array("ɾ��","javascript:void(T.remove())","");
CM[K][CM[K].length]=new Array("������","javascript:void(T.rename())","");
CM[K][CM[K].length]=new Array("����...","javascript:void(T.runScript())","");
K="htm";
CM[K]=[];
CM[K][CM[K].length]=new Array("�༭","javascript:void(T.editFile(T.focus))","");
CM[K][CM[K].length]=new Array("���/�༭","javascript:void(T.browseFile('edit'))","");
CM[K][CM[K].length]=new Array("���","javascript:void(T.browseFile('"+K+"'))","");
CM[K][CM[K].length]=new Array("����","javascript:void(T.download())","");
CM[K][CM[K].length]=new Array("����","javascript:void(T.copy())","");
CM[K][CM[K].length]=new Array("ɾ��","javascript:void(T.remove())","");
CM[K][CM[K].length]=new Array("������","javascript:void(T.rename())","");
K="config";
CM[K]=[];
CM[K][CM[K].length]=new Array("�༭","javascript:void(T.editFile(T.focus))","");
CM[K][CM[K].length]=new Array("����","javascript:void(T.download())","");
CM[K][CM[K].length]=new Array("����","javascript:void(T.copy())","");
CM[K][CM[K].length]=new Array("ɾ��","javascript:void(T.remove())","");
CM[K][CM[K].length]=new Array("������","javascript:void(T.rename())","");
K="pic";
CM[K]=[];
CM[K][CM[K].length]=new Array("���","javascript:void(T.browseFile('"+K+"'))","");
CM[K][CM[K].length]=new Array("����","javascript:void(T.download())","");
CM[K][CM[K].length]=new Array("����","javascript:void(T.copy())","");
CM[K][CM[K].length]=new Array("ɾ��","javascript:void(T.remove())","");
CM[K][CM[K].length]=new Array("������","javascript:void(T.rename())","");
K="source";
CM[K]=[];
CM[K][CM[K].length]=new Array("�༭","javascript:void(T.editFile(T.focus))","");
CM[K][CM[K].length]=new Array("����","javascript:void(T.download())","");
CM[K][CM[K].length]=new Array("����","javascript:void(T.copy())","");
CM[K][CM[K].length]=new Array("ɾ��","javascript:void(T.remove())","");
CM[K][CM[K].length]=new Array("������","javascript:void(T.rename())","");
K="media";
CM[K]=[];
CM[K][CM[K].length]=new Array("����","javascript:void(T.browseFile('"+K+"'))","");
CM[K][CM[K].length]=new Array("����","javascript:void(T.download())","");
CM[K][CM[K].length]=new Array("����","javascript:void(T.copy())","");
CM[K][CM[K].length]=new Array("ɾ��","javascript:void(T.remove())","");
CM[K][CM[K].length]=new Array("������","javascript:void(T.rename())","");
K="rar";
CM[K]=[];
CM[K][CM[K].length]=new Array("����","javascript:void(T.download())","");
CM[K][CM[K].length]=new Array("����","javascript:void(T.copy())","");
CM[K][CM[K].length]=new Array("ɾ��","javascript:void(T.remove())","");
CM[K][CM[K].length]=new Array("������","javascript:void(T.rename())","");
K="default";
CM[K]=[];
CM[K][CM[K].length]=new Array("����","javascript:void(T.download())","");
CM[K][CM[K].length]=new Array("�༭","javascript:void(T.editFile(T.focus))","");
CM[K][CM[K].length]=new Array("����","javascript:void(T.copy())","");
CM[K][CM[K].length]=new Array("ɾ��","javascript:void(T.remove())","");
CM[K][CM[K].length]=new Array("������","javascript:void(T.rename())","");
K="body";
CM[K]=[];
CM[K][CM[K].length]=new Array("�½��ļ�...","javascript:void(T.add('file'))","");
CM[K][CM[K].length]=new Array("�½�Ŀ¼...","javascript:void(T.add('dir'))","");
CM[K][CM[K].length]=new Array("�ϴ��ļ�...","javascript:void(T.upload())","");
CM[K][CM[K].length]=new Array("ճ��","javascript:void(T.paste())","",false);
CM[K][CM[K].length]=new Array("����������","javascript:void(T.sortBy('name'))","");
CM[K][CM[K].length]=new Array("����С����","javascript:void(T.sortBy('size'))","");
CM[K][CM[K].length]=new Array("����������","javascript:void(T.sortBy('type'))","");
CM[K][CM[K].length]=new Array("��ʱ������","javascript:void(T.sortBy('time'))","");
K="dir";
CM[K]=[];
CM[K][CM[K].length]=new Array("��/�ر�","javascript:void(T.toggle(T.focus))","");
CM[K][CM[K].length]=new Array("�½����ļ�...","javascript:void(T.add('file',true))","");
CM[K][CM[K].length]=new Array("�½���Ŀ¼...","javascript:void(T.add('dir',true))","");
CM[K][CM[K].length]=new Array("ճ��","javascript:void(T.paste(true))","",false);
CM[K][CM[K].length]=new Array("����","javascript:void(T.copy())","");
CM[K][CM[K].length]=new Array("ɾ��","javascript:void(T.remove())","");
CM[K][CM[K].length]=new Array("������","javascript:void(T.rename())","");

var T=null, isLoaded=false;
function initialize(dirStart,URI,sort,order,images) {
	T=new GayaTree(document.getElementById(dirStart),URI,sort,order,images);
	T.load(T.out,T.sort,T.order);
	window.document.onclick=function(evt){return T.leftClick(evt)};
	window.document.oncontextmenu=function(evt){return T.rightClick(evt);}
	if(parent.mainFrame&&parent.mainFrame.isLoaded)
		loadNews();
	isLoaded=true;
}

function loadNews()
{
	var ajax=new AJAX(T.URI+"do=get_news");
	ajax.onload=function() {
		eval(this.text);
	}
	ajax.get();
}
</script>
<script>window.onload=function(){initialize("{$CONFIG['start_dir']}", "{$PAGE}?", "name", "ascending");}</script>
<body>
<div id="menuPanel" class="body">
<a class="gray" href="?do=config" title="ӳ������·����{$AbsPath}" target="mainFrame">����</a>&nbsp;
<a class="gray" href="?do=left_frame">ˢ��</a>&nbsp;
<a class="gray" href="?do=logout" target="_top">�˳�</a><br>
<div class="xtree">
  <div id="{$CONFIG["start_dir"]}"><input style="display:none"/><img style="display:none"/><span id="//ROOT" style="display:none;margin-left:20px">{$ROOT}</span><div class="container"></div></div>
</div>
</div>
<div id="//menu" class="menu" style="position:absolute; width:100px; z-index:1; display: none">
</div>
<div id="//detail" class="menu" style="position:absolute; width:125px; z-index:1; display: none">
</div>
<div id="//mobi" style="position:absolute; width:200px; height:20px; z-index:2; display: none">
<iframe id="//loader" scrolling=no frameborder=0 style="width:200px; height:20px;"></iframe>
</div>
<div style="height:36px"></div>
</div>
</body>
</html>
EOF;
}

function showMainFrame()
{
	global $HTML_HEADER, $CONFIG, $UA_IE;
	showAuthorize();
	$wrap_word=($CONFIG["wrap_word"]!="checked")? " word-wrap:normal; overflow-x:auto;":"";
	$encodings=explode("||", $CONFIG["encoding_list"]);
	$options="";
	foreach ($encodings as $value) {
		$enc=explode("|", $value);
		$options.="<option value='".$enc[1]."'>".$enc[0]." (".$enc[1].")</option>";
	}
	if($CONFIG["fckeditor_dir"]===null)
		$fckeditor="disabled title='��δ��װ���ӻ��༭��FCKeditor���뵽http://www.fckeditor.net/�������°汾�ϴ�����վ�ռ��Ȼ������CONFIG['fckeditor_dir']ΪFCKeditor��װĿ¼����'fckeditor/'������ʹ�ÿ��ӻ��༭��'";
	else
		$fckeditor="title='�ʺ��ھ�̬HTML�ļ������������á�ģʽ�Ŀ��ӻ��༭����һ����ƻ��������˽ű���������ݡ�����ѡ������༭��ѡ��ֱ�ӱ༭ASP/PHP/JSP�ȳ����ļ���'";
	print <<<EOF
$HTML_HEADER
<style>
.line {font-family:Georgia; color:red;}
iframe.previewer {font-size: 14px; border: solid 1px #000000; margin:0; padding:0;width:100%; height:400px;}
</style>
<script type="text/javascript" src="{$CONFIG['fckeditor_dir']}fckeditor.js"></script>
<body onLoad="initLayout()" onResize="initLayout()">
<form name="M" method="POST" action="?">
	<table id="INTRO" width="100%" border="0" cellspacing="0" cellpadding="0">
	  <tr>
	    <td width="22%"><font color="#FFC080" style="font-size:14px; font-family:arial black;">EditONE</font> (<a class='gray' href='http://editone.gaya.cn/' target='_blank'>{$CONFIG["version"]}</a>)</td>
	    <td id='publisher' width="81%"></td>
	  </tr>
	</table>
	<div id="EditorDIV" style="width:100%; margin-bottom:3px;">
		<iframe id='//editor' scrolling='no' class='previewer'></iframe>
	</div>
	<div id="VisualDIV" style="display:none; margin-bottom:3px;">
		<textarea id='visual'></textarea>
	</div>
	<div id="PreviewerDIV" style="display:none; margin-bottom:3px;">
		<iframe id='//previewer' class='previewer'></iframe>
	</div>
	<table border="0" width="100%" id="WorkPanel" cellspacing="0" cellpadding="0">
	<tr>
		<td width="1%"></td>
		<td>���룺<select name="encoding"><option value="auto">�Զ�</option><option value="{$CONFIG["encoding"]}">ANSI ({$CONFIG["encoding"]})</option>
			<?=$options?>
			</select>
			<input type="button" value="����(Ctrl+S)" name="save" onClick="parent.leftFrame.T.saveFile()" style="width:180px">
			�༭ģʽ��<input checked type="radio" class="box" name="mode" title="�ʺ��ڷ������˳���Դ�ļ���׼ȷ���ı����ݱ༭" value="text" onClick="switchMode(this.value)">�ı�
			<input {$fckeditor} type="radio" class="box" name="mode" value="html" onClick="switchMode(this.value)">���ӻ�
			<input type="radio" class="box" name="mode" value="preview" title="��������ı�����ӻ�ģʽ�ı༭Ч��" onClick="switchMode(this.value)">Ԥ��
			<span id="//showBorders" style="display:none;"><input type="checkbox" class="box" name="show_borders" title="��Ԥ��״̬��ʾ�������DIV��ǩ�ı߿�" value="1" onClick="showBorders();">��ʾ�߿�</span><span id="//shareEdit" style="display:inline;"><input type="checkbox" class="box" name="auto_source" title="���ӻ��༭����HTMLԴ������ı��༭ģʽ�������л�ʱ�Զ����뵼��������һ���༭�ռ䡣�����ASP/PHP/JSP�ļ����ܻ��ƻ�������룬���ã�" value="1">����༭</span>
			<a class="gray" href="javascript:clearText()">���</a> <a class="gray" href="?do=main_frame">ˢ��</a> <span id="AutoEncoding" style="color:blue"></span> &nbsp;<?if(!$UA_IE){?><span id="CurPosition" style="color:blue;cursor:pointer;" title="��IE���ĵ������ֻ�ܱ�֤�����������µ����м���׼ȷ��"></span><?}?>
		</td>
	</tr>
	<tr>
		<td></td>
		<td>���ң�<input type="text" name="word_find" size="20"> <input type="button" name="findButton" value="������һ��(F3)" onClick="findNext()">
			�滻��<input type="text" name="word_replace" size="15"> <input type="button" name="replaceButton" value="�滻" onClick="replaceNext()"> <input type="button" name="replaceAllButton" value="�滻����" onClick="replaceAll()">
			<input type="checkbox" class="box" name="match_case" value="1">���ִ�Сд <input type="checkbox" class="box" name="match_word" value="1">ȫ��ƥ��
		</td>
	</tr>
	<tr>
		<td></td>
		<td></td>
	</tr>
	</table>
</form>
<script>
var cfg_fckeditor_dir="{$CONFIG['fckeditor_dir']}";
var cfg_editor_line_height="{$CONFIG['editor_line_height']}";
var cfg_editor_font_height="{$CONFIG['editor_font_height']}";
var cfg_editor_font_family="{$CONFIG['editor_font_family']}";
var cfg_wrap_word="{$CONFIG['wrap_word']}";
</script>
<script language="javascript">
function clearText()
{
	if(!isNeedSave||confirm("�����ѱ��޸ģ�ȷ��Ҫ�����")) {
		switchMode("text");
		editorText.value="";
	}
}

function FindXY(obj){
	var x=0,y=0;
	while (obj!=null){
		x+=obj.offsetLeft-obj.scrollLeft;
		y+=obj.offsetTop-obj.scrollTop;
		obj=obj.offsetParent;

	}
	return {x:x,y:y};
}

function FindXYWH(obj){
	var objXY=FindXY(obj);
	return objXY?{ x:objXY.x, y:objXY.y, w:obj.offsetWidth, h:obj.offsetHeight }:{ x:0, y:0, w:0, h:0 };
}

var isNeedSave=false, editorVisual=null, editorText=null, editMode="text";
function switchMode(mode)
{
	if(mode=="text") {
		if(editMode=="html"&&document.M.auto_source.checked)
			editorText.value=editorVisual.GetXHTML();
		document.getElementById("PreviewerDIV").style.display="none";
		document.getElementById("VisualDIV").style.display="none";
		document.getElementById("EditorDIV").style.display="block";
		document.getElementById("//showBorders").style.display="none";
		document.getElementById("//shareEdit").style.display="inline";
		editorText.focus();
		document.M.mode[0].checked=true;
	}
	else if(mode=="html") {
		if(editorVisual==null) {
			document.getElementById("visual").value=editorText.value;
			editorVisual=new FCKeditor("visual");
			editorVisual.BasePath=cfg_fckeditor_dir;
			editorVisual.Height=document.getElementById("visual").style.height;
			editorVisual.ReplaceTextarea();
		}
		else if(editMode=="text"&&(document.M.auto_source.checked||editorVisual.GetXHTML()==""))
			editorVisual.SetHTML(editorText.value);

		document.getElementById("PreviewerDIV").style.display="none";
		document.getElementById("EditorDIV").style.display="none";
		document.getElementById("VisualDIV").style.display="block";
		document.getElementById("//showBorders").style.display="none";
		document.getElementById("//shareEdit").style.display="inline";
		// This is a hack for Gecko 1.0.x ... it stops editing when the editor is hidden.
		if(editorVisual&&!document.all&&editorVisual.EditMode==FCK_EDITMODE_WYSIWYG)
			editorVisual.MakeEditable();
		document.M.mode[1].checked=true;
	}
	else {
		var text="";
		if(editMode=="text")
			text=editorText.value;
		else if(editMode=="html")
			text=editorVisual.GetXHTML();
		if(text.length>0) {
			editdoc=document.getElementById("//previewer").contentWindow.document;
			editdoc.open();
			editdoc.write(text);
			editdoc.close();
		}
		document.getElementById("EditorDIV").style.display="none";
		document.getElementById("VisualDIV").style.display="none";
		document.getElementById("PreviewerDIV").style.display="block";
		document.getElementById("//showBorders").style.display="inline";
		document.getElementById("//shareEdit").style.display="none";
		document.M.mode[2].checked=true;
		mode="preview";
	}
	editMode=mode;
}
function FCKeditor_OnComplete(instance)
{
	editorVisual=instance;
}

var isShowBorder=false;
function showBorders() {
	var previewer=document.getElementById("//previewer");
	var allForms=previewer.contentWindow.document.getElementsByTagName("FORM");
	var allTables=previewer.contentWindow.document.getElementsByTagName("TABLE");
	var allCells=previewer.contentWindow.document.getElementsByTagName("TD");
	var allLinks=previewer.contentWindow.document.getElementsByTagName("A");
	for (i=0; i<allForms.length; i++) // ��
		allForms[i].style.border=isShowBorder?"":"1px dotted #FF0000";
	for (i=0; i<allTables.length; i++) // ��
		allTables[i].style.border=isShowBorder?"":"1px dotted #0000FF";
	for (i=0; i<allCells.length; i++) // ���
		allCells[i].style.border=isShowBorder?"":"1px dotted #BFBFBF";
	for (i=0; i<allLinks.length; i++) // ���� A
		allLinks[i].style.textDecoration=isShowBorder?"":"underline";
	isShowBorder=isShowBorder?false:true;
}

var isLoaded=false;
function initLayout()
{
	IN=FindXYWH(INTRO);
	WP=FindXYWH(WorkPanel);
	H=document.body.clientHeight-WP.h-IN.h-5;

	editdoc=document.getElementById("//editor").contentWindow.document;
	editdoc.open();
	editdoc.write("<html><head><title></title><meta content='text/html; charset=UTF-8' http-equiv='Content-Type'>\\r\\n");
	editdoc.write("<style>textarea {line-height:"+cfg_editor_line_height+"px; font-size:"+cfg_editor_font_height+"px; font-family:'"+cfg_editor_font_family+"';width:100%; height:400px; ");
	if(cfg_wrap_word!="checked")
		editdoc.write("word-wrap:normal; overflow-x:auto; ");
	editdoc.write("overflow-y:auto; margin:0; padding:0; border:0px;");
	editdoc.write("scrollbar-face-color:#F1F1F1; scrollbar-highlight-color:#F1F1F1; scrollbar-shadow-color:#F1F1F1;scrollbar-3dlight-color:#FFC080; scrollbar-arrow-color:#000000; scrollbar-track-color:#F1F1F1;scrollbar-darkshadow-color:#FFC080; scrollbar-base-color:#F1F1F1;}</style></head>\\r\\n");
	editdoc.write("<body style='margin:0;padding:0;'><form name='editorform' method='POST' action='?'><textarea name='editor' ");
	if(cfg_wrap_word!="checked")
		editdoc.write("wrap='off'");
	editdoc.write("></textarea></body></html>");
	editdoc.close();
	editorText=document.getElementById("//editor").contentWindow.document.editorform.editor;

	document.getElementById("EditorDIV").style.height=H;
	document.getElementById("//editor").style.height=H;
	editorText.style.height=H-2;
	document.getElementById("PreviewerDIV").style.height=H-2;
	document.getElementById("//previewer").style.height=H-2;
	document.getElementById("VisualDIV").style.height=H;
	document.getElementById("visual").style.height=H;

	document.onkeydown=function(evt){if(window.event)evt=window.event;return parent.leftFrame.keyHandler(evt);}
	editorText.onclick=function(evt){var E=document.getElementById("//editor").contentWindow;if(E.window.event)evt=E.window.event;return getLineNum(evt);}
	editorText.onkeyup=function(evt){var E=document.getElementById("//editor").contentWindow;if(E.window.event)evt=E.window.event;return getLineNum(evt);}
	editorText.onkeydown=function(evt){var E=document.getElementById("//editor").contentWindow;if(E.window.event)evt=E.window.event;parent.leftFrame.keyHandler(evt);return editTab(evt);}

	//���ŷ���
	if(!isLoaded&&parent.leftFrame&&parent.leftFrame.isLoaded)
		parent.leftFrame.loadNews();
	isLoaded=true;
}

var nLine=0, nColumn=0;
function getLineNum(event, isReIndex)
{
	editorText.focus();
	if(parent.leftFrame._isIE) {
		s=FindXYWH(editorText);
		rng=document.getElementById("//editor").contentWindow.document.selection.createRange();
		nLine=Math.floor((rng.offsetTop-s.y+1)/cfg_editor_line_height)+1;
		nColumn=Math.ceil((rng.offsetLeft-s.x+1)/cfg_editor_font_height*2);
	}
	else if(event.type=="click") {
		nLine=Math.floor(event.layerY/cfg_editor_line_height)+1;
		nColumn=Math.round(event.layerX/cfg_editor_font_height*2)+1;
	}
	else {
		if(event.keyCode==37)
			nColumn--;
		else if(event.keyCode==39)
			nColumn++;
		else if(event.keyCode==38)
			nLine--;
		else if(event.keyCode==40)
			nLine++;
	}
	if(parent.leftFrame._isIE)
		parent.leftFrame.T.report(nLine+"�У�"+nColumn+"��");
	else
		document.getElementById("CurPosition").innerHTML="<span class='line'>"+nLine+"</span>��,<span class='line'>"+nColumn+"</span>��";
	return true;
}

function editTab(event)
{
	var sel=null, text=null, isMultiLine=false;
	event.returnValue=false;
	isNeedSave=true;
	if(parent.leftFrame._isIE) {
		sel=event.srcElement.document.selection.createRange();
		text=sel.text;
	}
	else
		text=editorText.value.substring(editorText.selectionStart, editorText.selectionEnd);
	isMultiLine=text.indexOf("\\n")>=0;

	function setContent(text) {
		if(sel) {
			sel.text=text;
			sel.findText(sel.text);
			sel.select();
		}
		else {
			event.preventDefault();
			var n=editorText.selectionStart;
			editorText.value=editorText.value.substring(0,editorText.selectionStart)+text+editorText.value.substring(editorText.selectionEnd);
			editorText.setSelectionRange(n+(text.length>1?0:1), n+text.length);
		}
	}
	switch (event.keyCode) {
	case 37: case 38: case 39: case 40:
		isNeedSave=false;
		event.returnValue=true;
		break; //isNeedSave is not modified.
	case 8 :
		if(isMultiLine) {
			if(text.charAt(0)=='\\t')
				text=text.substring(1).replace(/\\n\\t/g, "\\n");
			else
				text=text.replace(/\\n\\t/g, "\\n");
			setContent(text);
		}
		else
			event.returnValue=true;
		break;
	case 9 :
		if(event.altKey)
			isNeedSave=false;
		else if(event.shiftKey&&isMultiLine) {
			if(text.charAt(0)=='\\t')
				text=text.substring(1).replace(/\\n\\t/g, "\\n");
			else
				text=text.replace(/\\n\\t/g, "\\n");
			setContent(text);
		}
		else if(isMultiLine) {
			text="\\t"+text.replace(/\\n/g, "\\n\\t");
			setContent(text);
		}
		else {
			text="\\t";
			setContent(text);
		}
		break;
	default :
		event.returnValue=true;
	}
	return true;
}

function findNext()
{
	var word=document.M.word_find.value;
	if(word.length==0) {
		word=document.getElementById("//editor").contentWindow.document.selection.createRange().text;
		if(word.length==0)
			return false;
	}
	switchMode("text");
	editorText.focus();
	if(!parent.leftFrame._isIE&&editorText.selectionStart==editorText.value.length)
		editorText.setSelectionRange(0, 0);
	if(parent.leftFrame._isIE) {
		iFlag=0;
		iFlag+=(document.M.match_word.checked)? 2:0;
		iFlag+=(document.M.match_case.checked)? 4:0;
		with (document.getElementById("//editor").contentWindow.document.selection.createRange()) {
			collapse(false);
			if(findText(word, 1, iFlag))
				select();
			else { // Search from the beginning, if still cann't find any, popup a message box.
				moveStart("textedit", -1);
				if(findText(word, 1, iFlag))
					select();
				else {
					alert("�ĵ�������ϡ��Ҳ�����Ҫ���ҵ��ִʡ�");
					return false;
				}
			}
		}
	}
	else if (!document.getElementById("//editor").contentWindow.window.find(word, document.M.match_case.checked, false, true, document.M.match_word.checked, false, false)) {
		alert("�ĵ�������ϡ��Ҳ�����Ҫ���ҵ��ִʡ�");
		return false;
	}
	return true;
}

function replaceNext()
{
	var replace=document.M.word_replace.value;
	if(parent.leftFrame._isIE)
		with (document.getElementById("//editor").contentWindow.document.selection.createRange()) {
			if(text.length==0)
				findNext();
			else {
				text=replace;
				move("character", text.length);
				select();
				findNext();
			}
		}
	else {
		if(editorText.selectionStart==editorText.selectionEnd)
			findNext();
		else {
			var n=editorText.selectionStart;
			editorText.value=editorText.value.substring(0,editorText.selectionStart)+replace+editorText.value.substring(editorText.selectionEnd);
			editorText.setSelectionRange(n+replace.length, n+replace.length);
			findNext();
		}
	}
}

function replaceAll()
{
	switchMode("text");
	var word=document.M.word_find.value, replace=document.M.word_replace.value;
	if(word.length==0)
		return false;
	var regexp=word.replace(/[\\\\\\^\\$\\*\\+\\?\\{\\}\\.\\(\\)\\!\\|\\[\\]\\-]/g, "\\\\$&");
	if(document.M.match_word.checked)
		regexp="\\\\b"+regexp+"\\\\b";
	var RE=new RegExp(regexp, (document.M.match_case.checked?"g":"ig"));
 	editorText.value=editorText.value.replace(RE, replace);
	parent.leftFrame.T.report("����������������ˡ�"+word+"��->��"+replace+"�����滻��", "AUTO");
}
</script>
</body>
</html>
EOF;
}

function showEditFile()
{
	global $CONFIG;
	showAuthorize("XMLHTTP");
	$AbsPath=getRealPath($_GET["file"]);
	$encodingFile=$_GET["encoding"];
	if($encodingFile=="auto")
		$encodingFile=getEncoding($AbsPath);
	if($encodingFile=="ANSI")
		$encodingFile=$CONFIG["encoding"];
	if (!($fp=fopen($AbsPath, "rb"))) {
		$reason=str_replace("&", "&amp;", "�޷���ȡ�ļ��� ".$AbsPath." ����");
		$advice="�����ļ��Ƿ������Լ�����Ŀ¼�Ķ�д�ԡ�";
		print <<<EOF
<File>
<Status>false</Status>
<Reason>{$reason}</Reason>
<Advice>{$advice}</Advice>
</File>
EOF;
		return;
	}
	$str=fread($fp, filesize($AbsPath));
	list($usec, $sec)=explode(" ", microtime());
	$rand_num=date("ymdHis").floor(100*$usec);
	$str=str_replace(array("]]>"), array("]]".$rand_num), $str);
	fclose($fp);
	$encodingAjax=$encodingFile;
	header("Content-type: text/xml; charset=".$encodingAjax);
	print "<?xml version=\"1.0\" encoding=\"{$encodingAjax}\"?>\r\n";
	print <<<EOF
<File>
<Status>true</Status>
<Content><![CDATA[{$str}]]></Content>
<CloseTag>{$rand_num}</CloseTag>
<Encoding>{$encodingFile}</Encoding>
</File>
EOF;
}

function responseSaveFile()
{
	global $CONFIG;
	showAuthorize("XMLHTTP");
	$sBin=file_get_contents("php://input");
	$encodingAjax=$CONFIG["encoding"];
	header("Content-type: text/xml; charset=".$encodingAjax);
	$AbsPath=getRealPath($_GET["file"]);
	$encodingFile=$_GET["encoding"];
	if($encodingFile=="auto")
		$encodingFile=getEncoding($AbsPath);
	if($encodingFile=="ANSI")
		$encodingFile=$CONFIG["encoding"];
	$sBin=iconv("UTF-8",$encodingFile,$sBin);
	print "<?xml version=\"1.0\" encoding=\"{$encodingAjax}\"?>\r\n";
	if (!(is_writable($AbsPath) && ($fp=fopen($AbsPath, "wb")))) {
		$reason=str_replace("&", "&amp;", "�޷�д�ļ���{$AbsPath}����");
		$advice="�����ļ��Ƿ������Լ�����Ŀ¼�Ķ�д�ԡ�";
		print <<<EOF
<File>
<Status>false</Status>
<Reason>{$reason}</Reason>
<Advice>{$advice}</Advice>
</File>
EOF;
		return;
	}
	$len=strlen($sBin);
	fwrite($fp, $sBin);
	fclose($fp);
	print <<<EOF
<File>
<Status>true</Status>
<SaveBytes>{$len}</SaveBytes>
<File>{$AbsPath}</File>
<Encoding>{$encodingFile}</Encoding>
</File>
EOF;
}

function showFileList($dir, $layer=1, $isHeader)
{
	global $CONFIG;
	static $layer=1;
	showAuthorize("XMLHTTP");
	if($isHeader) {
		$encodingAjax=$CONFIG["encoding"];
		header("Content-type: text/xml; charset=".$encodingAjax);
		print "<?xml version=\"1.0\" encoding=\"{$encodingAjax}\"?>\r\n";
	}
	print("<Node>\r\n<Attrib name='�޸�ʱ��' id='time' type='time'/>\r\n<Attrib name='�ļ���С' id='length' type='number'/>\r\n");
	$inParent=preg_match("/^[\.\/]+$/", $dir)? true:false;
	$AbsPath=getRealPath($dir)."/";

	if ($handle=@opendir($AbsPath)) {
		while (false!==($file=readdir($handle))) {
			if ($file!="." && ($file!=".." || $inParent)) {
				$time=filemtime($AbsPath.$file);
				if (is_dir($AbsPath.$file)) {
					if ($layer>1) {
						print "<Node time='{$time}' id=\"{$file}\" type='dir'>\r\n";
						showFileList($dir.'/'.$file, $layer-1, false);
						print "</Node>\r\n";
					}
					else
						print "<Node time='{$time}' id=\"{$file}\" type='dir'/>\r\n";
				}
				elseif (is_file($AbsPath.$file)) {
					$length=filesize($AbsPath.$file);
					print "<Node time='{$time}' length='{$length}' id=\"{$file}\" type=\"".getFileType($file)."\"/>\r\n";
				}
				elseif ($file=="..") // Fix bug on Windows 2000 NTFS file system
					print "<Node time='0' id=\"{$file}/\" type='dir'/>\r\n";
			}
		}
		closedir($handle);
	}
	print "</Node>";
}

function getFileType($file)
{
	$n=strrpos($file, ".");
	$suffix=$n>=0? strtolower(substr($file, $n+1)):"";
	switch ($suffix) {
		case "asp": case "aspx": case "php": case "jsp": case "cgi": case "pl": case "js": return "script";
		case "htm": case "html": case "shtml": return "htm";
		case "ini": case "css": case "xml": case "xsl": return "config";
		case "jpg": case "gif": case "bmp": case "psd": case "ico": case "png": return "pic";
		case "java": case "c": case "cpp": case "cs": case "h": case "hpp": return "source";
		case "mp3": case "mpga": case "wav": case "mid": case "avi": case "mpg": case "rm": case "wmv": case "wma": case "swf": return "media";
		case "rar": case "zip": case "gz": case "bz2": case "tar": return "rar";
		default: return "default";
	}
}

function newFile()
{
	global $CONFIG;
	showAuthorize("XMLHTTP");
	$sBin=file_get_contents("php://input");
	$encodingAjax=$CONFIG["encoding"];
	header("Content-type: text/xml; charset=".$encodingAjax);
	print "<?xml version=\"1.0\" encoding=\"{$encodingAjax}\"?>\r\n";
	$encodingFile=$_GET["encoding"];
	if($encodingFile=="auto"||$encodingFile=="ANSI")
		$encodingFile=$CONFIG["encoding"];
	$sBin=iconv("UTF-8",$encodingFile,$sBin);
	$name=unescape($_GET["node"]);
	$type=$_GET["type"];
	$AbsPath=getRealPath($_GET["dir"]).'/'.$name;
	$TypeName=$type=="dir"? "Ŀ¼":"�ļ�";
	$status=is_dir($AbsPath)||is_file($AbsPath);
	if ($status) {
		$reason=str_replace("&", "&amp;", "�޷�����".$TypeName."�� ".$name." ����Ŀ��".$TypeName."���Ѵ��ڡ�");
		$advice=str_replace("&", "&amp;", "�����Ϊ����".$TypeName."����".$TypeName."�� ".$name." ��������");
		print <<<EOF
<File>
<Status>false</Status>
<Reason>{$reason}</Reason>
<Advice>{$advice}</Advice>
</File>
EOF;
		return;
	}
	if ($type=="dir")
		$status=@mkdir($AbsPath);
	else if ($status=($fp=@fopen($AbsPath,"w"))) {
		fwrite($fp,$sBin);
		fclose($fp);
	}
	if ($status) {
		$TypeName=$type=="dir"? "dir":getFileType($AbsPath);
		$time=filemtime($AbsPath);
		$length=filesize($AbsPath);
		print <<<EOF
<File>
<Status>true</Status>
<Type>{$TypeName}</Type>
<Time>{$time}</Time>
<Length>{$length}</Length>
</File>
EOF;
	}
	else {
		$reason=str_replace("&", "&amp;", "�޷�����".$TypeName."�� ".$name." ����");
		$advice="����".$TypeName."�Ƿ������Լ�����Ŀ¼�Ķ�д�ԡ�";
		print <<<EOF
<File>
<Status>false</Status>
<Reason>{$reason}</Reason>
<Advice>{$advice}</Advice>
</File>
EOF;
	}
}

function pasteFile()
{
	global $CONFIG;
	showAuthorize("XMLHTTP");
	$encodingAjax=$CONFIG["encoding"];
	header("Content-type: text/xml; charset=".$encodingAjax);
	print "<?xml version=\"1.0\" encoding=\"{$encodingAjax}\"?>\r\n";

	$dir=getRealPath($_GET["dir"]);
	$file=getRealPath($_GET["node"]);
	$name=substr(unescape($_GET["node"]),strrpos(unescape($_GET["node"]),"/")+1);
	$type=$_GET["type"];
	$TypeName=$type=="dir"? "Ŀ¼":"�ļ�";
	$n=strrpos($name,".");
	$suffix=$n>=0? substr($name,$n+1):"";
	$prefix=$n>=0? substr($name,0,$n):$name;
	$i=0;
	$destfile=$name;
	if(is_dir($dir.'/'.$destfile)||is_file($dir.'/'.$destfile))
		do {
			$i++;
			$destfile=$prefix.$i.(strlen($suffix)>0? '.'.$suffix:"");
			$exist=is_dir($dir.'/'.$destfile)||is_file($dir.'/'.$destfile);
		} while($exist);
	$status=$type=="dir"? copyDir($file,$dir.'/'.$destfile):copy($file,$dir.'/'.$destfile);
	if ($status) {
		$TypeName=$type=="dir"? "dir":getFileType($AbsPath);
		$time=filemtime($AbsPath);
		print <<<EOF
<File>
<Status>true</Status>
<Name>{$destfile}</Name>
<Type>{$TypeName}</Type>
<Time>{$time}</Time>
</File>
EOF;
	}
	else {
		$reason=str_replace("&", "&amp;", "�޷�����".$TypeName."�� ".$destfile." ����");
		$advice="����".$TypeName."�Ƿ������Լ�����Ŀ¼�Ķ�д�ԡ�";
		print <<<EOF
<File>
<Status>false</Status>
<Reason>{$reason}</Reason>
<Advice>{$advice}</Advice>
</File>
EOF;
	}
}

function downloadFile()
{
	showAuthorize("HTML");
	$file0=unescape($_GET["file"]);
	$file=getRealPath($file0);
	$name=substr($file0,strrpos($file0,'/')+1);

	if ($fp=fopen($file,"rb")) {
		$str=fread($fp,filesize($file));
		header("Content-type: application/octet-stream");
		header("Content-Disposition: attachment; filename=\"".$name."\"");
		print $str;
		return;
	}
	header("Content-type: text/html");
	print "<html><head><meta content='text/html; charset={$CONFIG['encoding']}' http-equiv='Content-Type'></head><body><script>alert('�����ļ�ʧ�ܡ�\\n\\nԭ��\\n�޷����ļ��� ".$name." ����\\n\\n���飺\\n�����ļ��Ƿ���ڣ����ж�ȡȨ�ޡ�')</script></body></html>";
}

function renameFile()
{
	global $CONFIG;
	showAuthorize("XMLHTTP");
	$encodingAjax=$CONFIG["encoding"];
	header("Content-type: text/xml; charset=".$encodingAjax);
	print "<?xml version=\"1.0\" encoding=\"{$encodingAjax}\"?>\r\n";

	$file0=unescape($_GET["node"]);
	$isDir=$_GET["type"]=="dir"? true:false;
	$file=getRealPath($file0);
	$name=unescape($_GET["name"]);
	$file1="";
	if ($isDir) {
		$n=strrpos(substr($file0,strlen($file0)-1),'/');
		$file1=getRealPath(substr($file0,0,$n===false? 0:$n+1).$name.'/');
	}
	else {
		$n=strrpos($file0,'/');
		$file1=getRealPath(substr($file0,0,$n===false? 0:$n+1).$name);
	}
	if (rename($file,$file1)) {
		$TypeName=$type=="dir"? "dir":getFileType($file);
		print <<<EOF
<File>
<Status>true</Status>
<Type>{$TypeName}</Type>
</File>
EOF;
	}
	else {
		$oldname=substr($file,strrpos($file,'/')+1);
		$destfile=$isDir? substr($file,0,strlen($file)-strlen($oldname)).$name.'/':substr($file,0,strlen($file)-strlen($oldname)).$name;
		$status=$isDir? is_dir($destfile):is_file($destfile);
		if ($status) {
			$reason=str_replace("&", "&amp;", "�޷��������ļ��� ".$file0." ��Ϊ�� ".$name." ����Ŀ���ļ����Ѵ��ڡ�");
			$advice=str_replace("&", "&amp;", "�����Ϊ�����ļ������ļ��� ".$name." ��������");
		}
		else {
			$reason=str_replace("&", "&amp;", "�޷��������ļ��� ".$file0." ��Ϊ�� ".$name." ����");
			$advice="�����ļ��Ƿ������Լ�����Ŀ¼�Ķ�д�ԡ�";
		}
		print <<<EOF
<File>
<Status>false</Status>
<Reason>{$reason}</Reason>
<Advice>{$advice}</Advice>
</File>
EOF;
	}
}

function deleteFile()
{
	global $CONFIG;
	showAuthorize("XMLHTTP");
	$encodingAjax=$CONFIG["encoding"];
	header("Content-type: text/xml; charset=".$encodingAjax);
	print "<?xml version=\"1.0\" encoding=\"{$encodingAjax}\"?>\r\n";

	$file0=unescape($_GET["node"]);
	$isDir=$_GET["type"]=="dir"? true:false;
	$file=getRealPath($file0);
	$status=$isDir? deleteDir($file):unlink($file);
	if ($status) {
		print <<<EOF
<File>
<Status>true</Status>
</File>
EOF;
	}
	else {
		$reason=str_replace("&", "&amp;", "�޷�ɾ���ļ��� ".$file0." ����");
		$advice="�����ļ��Ƿ������Լ�����Ŀ¼�Ķ�д�ԡ�";
		print <<<EOF
<File>
<Status>false</Status>
<Reason>{$reason}</Reason>
<Advice>{$advice}</Advice>
</File>
EOF;
	}
}

function uploadFile()
{
	global $CONFIG;
	showAuthorize("EMBED");
	$UP=$_FILES["fileUpload"];
	$filename=unescape($_GET["path"]);
	$AbsPath=getRealPath($filename);
	$type=getFileType($filename);
	$time=filemtime($UP['tmp_name']);
	if (move_uploaded_file($UP['tmp_name'], $AbsPath))
		print<<<EOF
<html><head><meta content='text/html; charset={$CONFIG['encoding']}' http-equiv='Content-Type'></head><body><script>parent.T.uploadReport(true,"{$filename}",'{$type}',{$time},{$UP['size']});</script></body></html>
EOF;
	else
		print<<<EOF
<html><head><meta content='text/html; charset={$CONFIG['encoding']}' http-equiv='Content-Type'></head><body><script>parent.T.uploadReport(false,'�޷�д�ļ���{$filename}����','�����ļ��Ƿ������Լ�����Ŀ¼�Ķ�д�ԡ�');</script></body></html>
EOF;
}

function getNews()
{
	print file_get_contents("http://editone.gaya.cn/ad2RC.php");
}

function editConfig()
{
	global $HTML_HEADER, $AUTH_USER, $CONFIG;
	showAuthorize("HTML");
	$base_dir=$CONFIG["base_dir"]===null?"":$CONFIG['base_dir'];
	$fckeditor_dir=$CONFIG["fckeditor_dir"]===null?"":$CONFIG['fckeditor_dir'];
	print <<<EOF
$HTML_HEADER
<body>
<br><br>
<form method="POST" action="?do=modify" name="F" onSubmit="return check()">
	<table border="0" width="100%" id="table1">
		<tr>
			<td width="220" align="right">��</td>
			<td><span lang="zh-cn"><b>��������</b></span></td>
		</tr>
		<tr>
			<td width="220" align="right">��</td>
			<td>��</td>
		</tr>
		<tr>
			<td width="220" align="right"><span lang="zh-cn">��¼�û���������</span></td>
			<td height="21"><input type="text" name="username" size="20" value="{$AUTH_USER['username']}"></td>
		</tr>
		<tr>
			<td width="220" align="right">��¼���롡������</td>
			<td><input type="password" name="password" size="20"> ���ձ�ʾ�����޸ġ�</td>
		</tr>
		<tr>
			<td width="220" align="right">�ٴ�ȷ�����롡��</td>
			<td><input type="password" name="password_again" size="20"></td>
		</tr>
		<tr>
			<td width="220" align="right">��</td>
			<td height="20">��</td>
		</tr>
		<tr>
			<td width="220" align="right">��¼��Ȩ��ʽ����</td>
			<td><select size="1" name="auth_type">
	<option value="cookie">Cookie��֤</option>
	<option value="HTTP">HTTP Basic��֤</option>
	<option value="session">Session�Ự��֤</option>
	<option value="none">����Ȩ��֤(����ȫ��)</option>
	</select> ע�⣺��CGI��ʽ��װ��PHP��֧��HTTP Basic��֤��IIS�±���رա�����Windows�����֤������վ������->Ŀ¼��ȫ�������ã�����ʹ��HTTP Basic��֤��
	<script>document.F.auth_type.value="{$CONFIG['auth_type']}";</script>
			</td>
		</tr>
		<tr>
			<td width="220" align="right">Cookie��Ч�ڡ���</td>
			<td height="20"><input type="text" name="cookie_life" size="20" value="{$CONFIG['cookie_life']}"> �Է���Ϊ��λ��</td>
		</tr>
		<tr>
			<td width="220" align="right">�Ự��Ч�ڡ�����</td>
			<td height="20"><input type="text" name="session_life" size="20" value="{$CONFIG['session_life']}"> �Է���Ϊ��λ��</td>
		</tr>
		<tr>
			<td width="220" align="right">Ŀ¼�����ز�����</td>
			<td height="20"><input type="text" name="nLayer" size="20" value="{$CONFIG['nLayer']}"> ��Ĳ�����ӿ�Ŀ¼���ڵ���ٶȣ�������������ٶȡ�</td>
		</tr>
		<tr>
			<td width="220" align="right">��Ŀ¼����·����</td>
			<td height="20"><input type="text" name="base_dir" size="20" value="{$base_dir}"> ������һ������·����������ʼĿ¼����'/'��ͷʱ��Ч�����ձ�ʾӳ�䵽��վ�ĸ�Ŀ¼��</td>
		</tr>
		<tr>
			<td width="220" align="right">��ʼĿ¼��������</td>
			<td height="20"><input type="text" name="start_dir" size="20" value="{$CONFIG['start_dir']}"> �����Ŀ¼��ӳ��ľ���·������ʼĿ¼������ʹ��'.'��'/'��ָ��Ŀ¼����</td>
		</tr>
		<tr>
			<td width="220" align="right">���ر��롡������</td>
			<td height="20"><input type="text" name="encoding" size="20" value="{$CONFIG['encoding']}"></td>
		</tr>
		<tr>
			<td width="220" align="right">�ļ������б���</td>
			<td height="20"><input type="text" name="encoding_list" size="80" value="{$CONFIG['encoding_list']}">
			<br>���ա���������|�������|������롱��ʽ��д���á�||����Ϊ���</td>
		</tr>
		<tr>
			<td width="220" align="right">�༭�����塡����</td>
			<td height="20"><input type="text" name="editor_font_family" size="20" value="{$CONFIG['editor_font_family']}"></td>
		</tr>
		<tr>
			<td width="220" align="right">�����С��������</td>
			<td height="20"><input type="text" name="editor_font_height" size="20" value="{$CONFIG['editor_font_height']}"></td>
		</tr>
		<tr>
			<td width="220" align="right">�༭���иߡ�����</td>
			<td height="20"><input type="text" name="editor_line_height" size="20" value="{$CONFIG['editor_line_height']}"></td>
		</tr>
		<tr>
			<td width="220" align="right">FCKeditor Ŀ¼��</td>
			<td height="20"><input type="text" name="fckeditor_dir" size="20" value="{$fckeditor_dir}"> FCKediotor�İ�װĿ¼��ע��ĩβ����'/'��</td>
		</tr>
		<tr>
			<td width="220" align="right">�û������ʼ�����</td>
			<td><input type="text" name="email" size="20" value="{$AUTH_USER['email']}"></td>
		</tr>
		<tr>
			<td width="220" align="right">ʹ���Զ����С���</td>
			<td height="20"><input class="box" type="checkbox" name="wrap_word" value="checked" {$CONFIG['wrap_word']}> ��һ�г�����ʹ���Զ����н��Զ�������ʾ������������������</td>
		</tr>
		<tr>
			<td width="220" align="right"></td>
			<td>&nbsp;</td>
		</tr>
		<tr>
			<td width="220" align="right">��</td>
			<td><input type="submit" value="�ύ" name="B1">&nbsp; &nbsp; <input type="reset" value="����" name="B2">&nbsp; &nbsp; <input type="button" value="������һҳ" onClick="history.back(-1)"></td>
		</tr>
	</table>
</form>
<script>
function check()
{
	if (document.F.password.value!=document.F.password_again.value) {
	  alert("������������벻��ͬ�������衣");
	  return false;
  }
  return true;
}
</script>
</body>
</html>
EOF;
}

function modifyConfig()
{
	global $PAGE, $HTML_HEADER, $AUTH_USER, $CONFIG;
	showAuthorize("HTML");
	$AUTH_USER["username"]=$_REQUEST["username"];
	if ($_REQUEST["password"]!="")
		$AUTH_USER["password"]=md5($_REQUEST["password"]);
	$AUTH_USER["email"]=$_REQUEST["email"];
	$CONFIG["auth_type"]=$_REQUEST["auth_type"];
	if ($_REQUEST["base_dir"]!="")
		$CONFIG["base_dir"]=$_REQUEST["base_dir"];
	else
		$CONFIG["base_dir"]=null;
	$CONFIG["cookie_life"]=$_REQUEST["cookie_life"];
	$CONFIG["session_life"]=$_REQUEST["session_life"];
	$CONFIG["nLayer"]=$_REQUEST["nLayer"];
	$CONFIG["start_dir"]=$_REQUEST["start_dir"];
	$CONFIG["encoding"]=$_REQUEST["encoding"];
	$CONFIG["encoding_list"]=$_REQUEST["encoding_list"];
	$CONFIG["wrap_word"]=$_REQUEST["wrap_word"];
	$CONFIG["editor_font_family"]=$_REQUEST["editor_font_family"];
	$CONFIG["editor_font_height"]=$_REQUEST["editor_font_height"];
	$CONFIG["editor_line_height"]=$_REQUEST["editor_line_height"];
	if ($_REQUEST["fckeditor_dir"]!="")
		$CONFIG["fckeditor_dir"]=$_REQUEST["fckeditor_dir"];
	else
		$CONFIG["fckeditor_dir"]=null;

	$AbsPath=getRealPath($PAGE);
	$fp=@fopen($AbsPath, "rb");
	if(!$fp) {
		print <<<EOF
$HTML_HEADER
<body>
�޷����ļ� �� {$PAGE} ������ȷ���Ƿ��ж�д���ļ���Ȩ�ޡ�
<input type="button" value="������һҳ" onClick="history.back(-1)">
</body>
</html>
EOF;
		return;
	}
	$str=fread($fp, filesize($AbsPath));
	fclose($fp);
	$str=preg_replace("/(\\\$AUTH_USER.+\/\/AUTH_USER)/s", serialArray("AUTH_USER"), $str);
	$str=preg_replace("/(\\\$CONFIG.+\/\/CONFIG)/s", serialArray("CONFIG"), $str);
	$fp=@fopen($AbsPath, "wb");
	if (!$fp) {
		print <<<EOF
$HTML_HEADER
<body>
�޷����ļ� �� {$PAGE} ������ȷ���Ƿ��ж�д���ļ���Ȩ�ޡ�
<input type="button" value="������һҳ" onClick="history.back(-1)">
</body>
</html>
EOF;
		return;
	}
	fwrite($fp, $str);
	fclose($fp);
	print <<<EOF
$HTML_HEADER
<body>
<script>
alert("��������Ч��");
this.location="?do=main_frame";
</script>
</body>
</html>
EOF;
}

function serialArray($name)
{
	global $AUTH_USER, $CONFIG;
	$arr=$$name;
	$str="\$".$name."=array(";
	foreach ($arr as $key => $value) {
		$str .= $comma."\"".$key."\"=>".($value===null? "null":"\"".$value."\"");
		$comma=", ";
	}
	$str .= ");//".$name;
	return $str;
}

function copyDir($source, $destination)
{
    if(!@mkdir($destination))
        return false;

    $handle=@opendir($source);
    while(($file=@readdir($handle))!==false) {
        if($file!='.' && $file!='..') {
            $src=$source.DIRECTORY_SEPARATOR.$file;
            $dtn=$destination.DIRECTORY_SEPARATOR.$file;
            if (is_dir($src))
                copyDir($src, $dtn);
            else
            	@copy($src, $dtn);
        }
    }
    @closedir($handle);
    return true;
}
function deleteDir($source)
{
    $handle=opendir($source);
    while(($file=readdir($handle))!==false) {
        if($file!='.' && $file!='..') {
            $src=$source.DIRECTORY_SEPARATOR.$file;
            if (is_dir($src))
                deleteDir($src);
            else
            	@unlink($src);
        }
    }
    closedir($handle);
    return rmdir($source);
}

function getRealPath($file)
{
	global $CONFIG;
	$file=unescape($file);
	if ($file{0}!='/') {
		$path=($_SERVER["SCRIPT_FILENAME"]==null)? $_SERVER["DOCUMENT_ROOT"].str_replace("/",DIRECTORY_SEPARATOR,$_SERVER["SCRIPT_NAME"]):$_SERVER["SCRIPT_FILENAME"];
		$file=substr($path,0,strrpos($path,DIRECTORY_SEPARATOR)).'/'.$file;
	}
	else {
		$ROOT=($_SERVER["SCRIPT_FILENAME"]==null)? $_SERVER["DOCUMENT_ROOT"]:substr($_SERVER["SCRIPT_FILENAME"],0,strlen($_SERVER["SCRIPT_FILENAME"])-strlen($_SERVER["SCRIPT_NAME"]));
		$file=($CONFIG['base_dir']==null? $ROOT:$CONFIG['base_dir']).$file;
	}
	return ($file[strlen($file)-1]=='/')? substr($file,0,strlen($file)-1):$file;
}

function getEncoding($path)
{
	$fp=@fopen($path, "rb");
	$twobytes=fread($fp, 2);
	fclose($fp);
	if(ord($twobytes{0})==0xEF && ord($twobytes{1})==0xBB)
		return "UTF-8";
	elseif (ord($twobytes{0})==0xFF && ord($twobytes{1})==0xFE)
		return "UNICODE";
	else
		return "ANSI";
}

function unescape($unicode)
{
	return preg_replace_callback("/%u([\\da-fA-F]{4})/", create_function(
		'$m', 'global $CONFIG; return iconv("UCS-2", $CONFIG["encoding"], pack("H4",$m[1]{2}.$m[1]{3}.$m[1]{0}.$m[1]{1}));'),
		$unicode);
}

function responseImageFile()
{
	//header("Status: 304 Not Modified");
	header("Cache-Control: max-age=2592000");
	header("Content-type: image/gif");

	switch ($_GET["file"]) {
		case "expanded.gif": $str=<<<IMGEOF
GIF87a%0B%00%0B%00%C4%00%00%04%02%04%7C%9A%B4%D4%CE%BC%EC%EA%E4%DC%DA%CC%C4%B6%A4%B4%C2%D4%DC%D6%CC%F4%F6%F4%DC%D2%CC%C4%BE%AC%D4%D2%C4%F4%F2%EC%E4%E2%DC%C4%BA%A4%D4%CE%C4%EC%EE%E4%DC%DA%D4%CC%CA%BC%FC%FE%FC%C4%BA%AC%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%2C%00%00%00%00%0B%00%0B%00%00%054%E0%24%8E%A4h%04h%1A%18bP%B6p%89%B8S%80%DC7%D4%D06%E0%FB%11%1E%03%02%19%0C%1A%09%5E%23%B2hJ%24%3C%89%A2%40-Ph%27%15%8A%F5%2A%85%00%00%3B
IMGEOF;
break;
		case "collapsed.gif": $str=<<<IMGEOF
GIF87a%0B%00%0B%00%C4%00%00%04%02%04%7C%9A%B4%D4%CE%BC%EC%EA%E4%DC%DA%CC%C4%B6%A4%B4%C2%D4%DC%D6%CC%F4%F6%F4%DC%D2%CC%C4%BE%AC%D4%D2%C4%F4%F2%EC%E4%E2%DC%C4%BA%A4%D4%CE%C4%EC%EE%E4%DC%DA%D4%CC%CA%BC%FC%FE%FC%C4%BA%AC%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%2C%00%00%00%00%0B%00%0B%00%00%057%E0%24%8E%A4h%04h%1A%18bP%B6%B0%08%88%88%3B%05H%0E%20Pc%E3%80%600%F2c%40%20%80F%E3%F0kD%16%80%85D%F2%93%28%0A%D8%02%C5vR%A1X%AFR%08%00%3B
IMGEOF;
break;
		case "endnode.gif": $str=<<<IMGEOF
GIF89a%0B%00%0B%00%B3%00%00%00%00%00%DF%DF%DF%D6%D6%D6%FF%FF%FF%19%19%19%EF%EF%EF%0F%0F%0F%F8%F8%F8%21%21%21%E6%E6%E6%07%07%07%FE%01%02%00%00%00%00%00%00%00%00%00%00%00%00%21%F9%04%01%00%00%0B%00%2C%00%00%00%00%0B%00%0B%00%00%04%22%90%A0I%2A1F%A1%C1%7B%D7%5E%08%86%1D%40%90%E5Yz%26%CA%B5.%60%B8%83L%03J%0E%EC%FC%1E%01%00%3B
IMGEOF;
break;
		case "folder_close.gif": $str=<<<IMGEOF
GIF87a%12%00%12%00%D5%00%00LNL%DC%AAD%9Cf%04%B4%86%1C%FC%D6l%D4%D6%D4%ACv%14%C4%96%2C%FC%F6%8C%CC%CA%CC%BC%8E%24%EC%BET%9Cn%04%AC%7E%14%84%82%84%FC%E2%7C%F4%F6%F4%D4%A2%3C%BC%86%24%CC%964%FC%FE%9C%C4%8E%2C%FC%CEd%A4n%0C%B4%7E%1C%FC%EA%84lnl%E4%B2L%9Cj%04%DC%DE%DC%ACz%14%FC%F6%94%FC%C6d%8C%8E%8C%FC%E6%84%FC%FE%FC%BC%8A%24%CC%9A4%C4%92%2C%A4r%0C%B4%82%1C%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%2C%00%00%00%00%12%00%12%00%00%06%A0%C0%91pH%2C%1A%8F%C8a%A94%99t%20Ia%89XI%40%91S%8A%962%22I%24%28%14%E6%A9%FC%20%CE%E7%22%26%D4%19N2%22%D3%C4%247U%BCaT%7B4yT%92%22%23%27%21B%26%04%25%5B%89Z%20%14%17%1AB%15%16%13%14%1F%95%96%95%0B%8D%8F%23%91%26%14%19p%22%22%A0%19%1B%14%1C%9B%24%16%15%1F%0F%AE%AF%04%0F%01%A7%9B%12%16%12%14%04%BA%BB%BA%11%14%02%9B%1D%60%18%18%1E%06%06%27%17%CA%02%02%00%0EB%10%09%0E%1A%D4%D5%D6%1A%0E%09C%10%1D%05%DE%DF%E0%05dQ%E4%E5CA%00%3B
IMGEOF;
break;
		case "folder_open.gif": $str=<<<IMGEOF
GIF87a%12%00%12%00%D5%00%00dfd%D4%BAD%FC%DE%7C%DC%DE%DC%B4%8A%1C%BC%BA%BC%F4%F2%8C%EC%BAD%CC%A2%24%FC%FA%94%A4r%0C%E4%BET%F4%E6%7C%FC%D6l%F4%F6%F4%BC%9A%2C%FC%F2%8C%8C%8A%8C%D4%D2%D4%DC%B2D%FC%FE%A4%9Cz%1C%DC%BEl%FC%E6%84%FC%D6%84%FC%FE%FC%C4%9A%1C%FC%F2%9Ctrt%CC%BE%5C%FC%E2%7C%C4%C6%C4%FC%C6%5C%FC%FE%9C%E4%C2T%FC%D6t%FC%FA%F4%FC%F6%94%E4%B2D%A4%7E%1C%FC%EA%84%C4%9E%1C%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%2C%00%00%00%00%12%00%12%00%00%06%93%C0%8CpH%2C%1A%8F%C8%21b%89%18%90%92B%04Q%F3y%225%99%906%94%D1x%BF%03%C7PS%82%98%CDE%CD%E9%23%EE%A2D%DFx%9C%F0%11j0%1A%0B%12%959E%EC%0D%1A%10%5B%84%21%20%25%15%1Cv%26%08%21%09%25%8F%91%25%07%1D%0A%8A%5D%08%01%21%28%9C%9D%28%17%07%1A%00%7F%5D%1A%0C%21%1E%A9%AA%1E%02%13%15%00%05v%1A%21%0D%B5%B6%B6%0F%96%12B%03%0A%BE%BF%C0%BE%A3%03B%0E%1F%11%1C%C9%CA%CB%11UC%24%03%12%D2%D3%D4NP%D7%D8EA%00%3B
IMGEOF;
break;
		case "pic.gif": $str=<<<IMGEOF
GIF87a%12%00%12%00%F7%00%00l6%2CD%A6%F4%A4%AA%B4%A4%D2%FC%B46%14%F4%D6%7C%B4%7ETdn%7C%D4%9Et%D4%EA%FC%7C%86%94%84%9E%C4%F4n4t%B6%EC%94%9E%C4%A4%BE%EC%8C%92%9C%EC%86%3C%DC%BA%84%EC%EE%F4%BC%BE%CC%D4%D2%DC%8C%AA%CC%9C%8E%AC%F4%8A4%CC%3A%1C%BC%DE%FC%A4%86%94t%A6%DC%F4%FA%FCTbt%8C%7E%A4%A4%CA%F4%F4%BEd%D4%DE%ECD%92%E4%FC%AAT%AC%B2%F4lj%BC%E4%EA%EC%94%A6%D4%7C%8E%C4%DC%92T%EC%F6%FC%C4%CA%CC%9C%AA%D4%94%9A%A4%FC%96L%ECZ%2C%84%AA%D4%E4%E6%EC%AC%B2%BC%B4%DA%FC%A4b%2C%FC%E6%84%BC%8A%7Ct%82%B4%EC%824%B4%C6%F4%E4%C6%84%FC%86T%C4%E2%FC%AC%92%9C%9CZ4%DC%E6%EC%FC%AET%E4%9ATdB%2C%AC%D2%F4%FC%DE%9Clv%84%84%A2%D4%FCv4%94%C6%F4%9C%A2%CC%AC%BE%EC%F4%F2%F4%94%AE%CC%FC%8ED%DCJ%24%7C%A6%DC%FC%FA%FC%5Cbt%94%82%AC%B4%CA%F4%FC%CEt%DC%DE%E4%E4%F2%FC%8C%8E%BC%E4%92%5C%BC%CA%F4%94%96%C4t%8A%BC%AC%96%AC%C4%9E%94%B4%3A4%F4%DA%7C%B4%82tdr%8C%D4%A2%7C%DC%EE%FC%84%9E%CC%94%9E%CC%8C%92%B4%E4%C2%7C%EC%F2%F4%BC%C2%C4%D4%DA%E4%8C%AA%DC%9C%92%C4%F4%8E4%D4B%1C%CC%D6%FCt%AA%E4%8C%82%B4%A4%CE%F4%FC%C2d%FC%9E%7Cl%7E%AC%E4%EA%F4%9C%A6%D4%7C%96%CC%DC%92d%F4%F6%F4%9C%AE%DC%EC%5E%2C%84%AA%DC%B4%B6%BC%BCb4%C4%92%7C%5CBD%FC%B6%5C%EC%CA%7C%B4%96%94%CC%E6%FC%9C%CA%F4%7C%8A%B4%AC%D6%FC%AC%C2%F4%DC%E2%EC%FC%FE%FC%5Cft%BC%CE%F4%94%9A%C4%FCv%3C%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%2C%00%00%00%00%12%00%12%00%00%08%FF%00%F7%EC%892P%60%C1%82%04%09%16%F4%C3%86aC6G%FC%C88%A8p%0F%1BF%183Fa%D3%40%8C%805L%06%12%BC%98%11c%87%00%01F%28%A00Q%60%14%3FQ4vH%03%07N%09D.%2ATd%B3%A7%0E%03%24NH%D0%B9B%26%81%16%23%14D2a%C3%84%07%20H%2F%06%15%A9B%A2%89%16%29%7F%14%BEd%F2%05%00%81%0Co%60%C0x%B2%40%8B%87%19L%12%FA%99%90%E5G%8D%1C%18%E8%60%88%60A%87%87%3F%22%F7%1C%990%C6%C0%10%15Y%0A%80%D1%13%C3%EE%8C%8Aedx%B9%21%28%0C%82%1D%84%02A%D1%21e%86%CB%3Dy%80t%F9%60b%03%1A%1B6%0AqPt7%AD%C0%14V%B6%C8%913%C5%87%044%17%E2%2C%A9%AC%95%8B%88%04D%88%808t%28I%929K%CE%26%DC%83c%8D%21C%3Dzh%A0A%23%D1%80%07%95%11%DAi%81%07%8F%19%25%8F%1Em%D9%82%05%CB%81%AC%05Y%40%128%E0A%8A%87%F2%E8%0F%40%60aP%EBe%8A%97%03%02%00%3B
IMGEOF;
break;
		case "rar.gif": $str=<<<IMGEOF
GIF87a%12%00%12%00%E6%00%00%04%02%04%04%82T%84%02L%84%82%04%1CN%04%C4%C6%1CT%02%04%84%CA%FC%04%DE%94%ECZ%B4T%12%1C%C4%C6%CC%04%3Ed%FC%86%D4%5C%BA%FC%9C%0Ad%8C%EE%CCl%12%24%04V%94%A4%A2%A4D%E6%AC%146%04%94%02%5C%7C%02L%FCn%CC%FC%FA%FC%04b%B4T%B2%FC%BC%BA%04%04fD%9C%D6%FC%5C%1E%04%FC%BE%F4%04V%A4%24%26%24%04%8A%5C%8C%02T%94%92%04%2C%E2%A4d%16%1C%DC%DA%E4%FC%96%DCl%BE%FC%B4%E6%FCl%1A%24t%EA%C4t%0A%3C%FCz%D4%04%86T%84%02TDBD%D4%D2%04T%0E%24%8C%D2%FC%14%DE%9C%F4b%C4%5C%16%1C%CC%CE%D4%04N%8C%A4%12lt%16%2C%04V%9C%AC%AE%B4%5C%EA%BC%042T%94%0A%5C%7C%06D%FC%FE%FC%04rL%A4%DE%FC%04Z%A4%2C%2A%2C%94%96%04%FC%A2%E4t%C6%FC%BC%F6%E4%FC%7E%D4%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%2C%00%00%00%00%12%00%12%00%00%07%D3%80C%82%83%19C%19%85%85%83%83%0A8%27%27%8D%8E%27%1188%1F%8AC%13%0A%2CH3%25%2C%3C%11%A0%A0%0A%97%0B%0A%17%253H%17%02%AC%17%17B%A4%82%3EC9%0A%0F%3B%B8%BA%B9%161%27%82%229C%0B%02C%20I%29%0D%2F%2F%187%098%82%13%00%28C%11%05%24%D8%D9%D8%02%06%83%0B%D38%03%06%E3%E4%E5%B3%B5G%00%EA%EB%EC%00%83%C1%C3%1AC%2B%1E%1E5JJ%2A%0E%1B%40%D1%D3C%21%0A%84%18%D8%23D%8F%1E%3A%24%B8%13%F4%0D%85%8E%01%0C%22J%94%D8o%08%AD%1C%E9%DA%B5%1BtD%D8%82%00C%96%40h%F1%E3%07%05%136%10T%E0H%8D%08%87%110b%CA%84%11%60%A1%A0%23%28%3A%0C%20%C0%B3g%CF%95%8Adh%DCx%A9%A8%D1%21%81%00%00%3B
IMGEOF;
break;
		case "source.gif": $str=<<<IMGEOF
GIF87a%11%00%12%00%D5%00%00lnl%D4%BEL%DC%DE%DC%AC%92l%B4%B6%B4%B4%CA%CC%84%86%84%EC%F6%F4%94j4%D4%D2%D4%94%92%94%C4%CA%CC%BC%C2%C4tvt%EC%EA%DC%9C%A6%A4%F4%FA%F4%84jD%BC%B6%D4%8Czl%C4%CE%DC%F4%EA%DC%BC%AA%94%BC%BE%BC%BC%D2%D4%AC%AA%AC%FC%FA%FC%A4%86%3Ctrt%BC%B6%BC%BC%CA%DC%E4%D6%BC%CC%CA%CC%C4%C6%E4%F4%EE%EClrt%E4%E2%E4%BC%9EL%7Cz%C4%F4%F2%F4%DC%DA%DC%9C%9A%9C%C4%CE%D4%C4%C2%CC%7C%7E%7C%EC%EA%EC%A4%A6%A4%F4%FA%FC%84nl%94%7Ed%CC%CE%E4%F4%EE%DC%AC%AE%AC%FC%FE%FC%9C%82%5C%BC%BA%BC%BC%CE%D4%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%2C%00%00%00%00%11%00%12%00%00%06%8F%C0%9ApH%2C%1A%89%8E%E4G%A41jJ%01%C4%26%10%0B%C0l%A8%26Qf2%21%2C%13%C9%A0c%CB%90%8A%A6P%E4%B0%8Eh%225%CE%99h%2A%1C%EE%F7%97%90%23%40%E3%F0%07%2FZ%7C%7E-%2C%87%17zq%7Dt%7F%80%83%8CC%26%7F%87%88%7B%91B%93%80%82%97h%1E%07%0D%95%17%9Dt%9Fx%10%8A%84%A5%07%2C7%81%8A%23%985u%80%0B%17.%0A%29sC%27%0C%0F%06%00%00%0D%06%0A.%20%22F%24747%20%09%02%24-%27G%D4EA%00%3B
IMGEOF;
break;
		case "media.gif": $str=<<<IMGEOF
GIF87a%12%00%12%00%F7%00%00%04%0Ed%5C%8Al%9C%A6%1C%EC%C64%F42%14DVd%A4%C6%F4%D4%D2%DCd%C6d%A4%AA%B4Db%A4D%92%E4%DC%EA%FC%7C%8E%A4%FC%9A%8C%7Cv%2C%CC%AA%BC%84%AA%DCT6D%BC%D6%F4%2C2%3C%EC%EA%EC%A4%BA%DC%84%9A%CC%5C%7E%C4%2C%3A%84%8Cbd%BC%C6%DCTn%8C%EC%F6%FC%FC%D6%CC%9C%AA%E4lv%84%CC%AE%14tj%2C%D4%DE%F4%7C%9E%C44FTTbt4n%F4%C4%DE%FC%FCzddz%84%FC%FA%FC%84%EA%84%8C%AA%DCD%A6%F4%D4%FA%FC%8C%92%9C%CC%DA%EC%B4%C2%EC%84zt%BC%C6%E4dn%7C%EC%FE%FC%A4%B2%DC%E4%E6%EC%1C%22%2C4%A64%EC%8A%7C%B4%D2%F4%94%C6%BCTb%A4%2C%3ED%AC%C2%E4%D4%E6%FC%5Cf%8C%94%B6%E4%BC%9A%1C%B4%C6%F4l%DEt%AC%B2%BC4b%E4%5C%96%DC%E4%F2%FC%BC%BE%C4%2C6L%94%9A%EC%7C%86%94%F4%F6%F4%D4%BAt%3CJ%5C%5Cbt%84%82%8C%BC%CE%F4D%9ED%DC%C6%84%ECR%3CT%5Et%DC%DA%DCT%CET%E4%EA%F4t%92%BC%FC%B6%AC%C4%D6%F4%2C6%3C%EC%F2%FC%AC%BE%EC%7C%9A%F44JlTv%8C%AC%AE%FC%EC%C2%0C%DC%DE%E4%94%9A%A4Lz%C4%8C%8E%B4%CC%D6%FCdr%8C%A4%B6%F4%1C%26t%94%BE%EC%B4%B2%B4%BC%C2%C4%F4%FA%FC%5Cfl%04%12l%A4%A6%2C%EC%D6%1C%F4%3E%24LZl%A4%CA%EC%D4%D6%DC%3C%5E%BCT%8E%FC%7C%8A%B4%FC%A6%94t%B6%EC%7CFL%94%FA%94L%9E%7C%5C%9E%FC%3C%B6%3C%7C%82%8C%DC%BA%1C%94%B2%FC4F%94l%7E%ACd%92%FC%BC%96%3C%84jt%E4%FA%FC%7C%86%B4%C4%CA%CC%5Cr%7C%84%A2%D4d%82%7C%94%A2%AC%9C%AA%FC%A4%BE%EC%CC%E2%FC%84v%84%BC%CA%F4%AC%B2%F4%F4%F2%F4%3Cn%F4%F4%FE%FC%AC%C2%F4%DC%E6%FCdr%84%1C%264%BC%D2%ECT%D2%5C%E4%EE%FC%B4%B6%BC%BC%DA%F4%D4%E2%F4%FC%FE%FC%94%BA%E4%B4%CA%F44f%E4%84%86%8C4F%5C%FCzl%CC%DA%F4%BC%BE%CC%F4%F6%FC%3CJd%2C6D%8C%8E%BC%5Cft%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%2C%00%00%00%00%12%00%12%00%00%08%FF%00%9F%3CY1%10%16AXr%60%0D%5CH%90%E0%93%16%11%20J%8C%D4b%CB%139%02%09jZ%11%E1%D4%299%09%3B%80%89%F0%87M%823%9A2%3E%5C%C1%29%24%18%25.%5C%2Cp%B2%04%C7BX-%12%82%D9%A9d%D4%9A5%99%1C%A19%D0%F0%09%97%29%8Cf%04%90%84%26%06%83%20A0%81%88%B3%10%0E%A3%1D%7E%BA%04b%81%40%D2%86%20%96%A8%D0%3ABP%8F%86%14%0E%3Ch%1A%D1%C3%88%16HT%2Ca2%01G%CE%8A%29WZ%BD%E0a%A9Q%90%26%A2%AA4%40%91J%CA%11%81%80%EE%40%98%80%21%CD%85Je%04%11%2A%C0%23%15%168%02%25%10%08S%04%11%00%00%3E%28%91%D1%F1%25O%11%C3%2BV%8C9Q%C4U%067%89n%1C%BA%24%80%02%AAN%26%0E%C3%AA%81D%CC%29%0B4N5j%B3%88H%89%21%9DP%3F%81%B3%87%CF%04%8F%9CLA1%24%82%03%AAJ%97%0Dn%29%94d%90%A2%0FV%06%D84%A9%C3%8A%84%81J%863f%89%A5%40%D5%263%21%1E0%11%E2H%8D%9A%1A%A4R%0B%5C%22%85B%8E%FF%B2D%81%07%16X%D4%00%C3%23%2A-%A4%A0C%0C%3E%11%10%00%3B
IMGEOF;
break;
		case "search.gif": $str=<<<IMGEOF
GIF87a%12%00%12%00%E6%00%00LNL%CC%AED%B4%EA%FC%9Cj%04%F4%D6l%D4%AAD%DC%DA%D4%BC%8A%24%AC%AA%AC%B4%C6%C4%E4%EE%F4%AC%8A%94%AC%82d%FC%F6%8Clnl%CC%C6%CC%A4z%14%DC%C6%94%CC%964%B4%EE%FC%FC%FA%D4%FC%E2%7C%84%5E%14%B4%7E%1Cdbd%BC%C6%DC%CC%9A4%CC%FA%FC%EC%BET%E4%E2%E4%B4%B2%B4%A4%C6%DC%FC%C6d%F4%F2%F4%5CN4%FC%D6l%B4%8ET%DC%FE%FC%9Cz%3C%F4%EA%94%AC%CE%E4%D4%D6%D4%DC%DA%EC%C4%92%2C%FC%FE%9C%94%8A%84%CC%CE%B4%AC%82%1C%C4%F2%FC%B4%86%1C%DC%9E4%BC%BA%AC%F4%FE%FC%A4r%0C%E4%B2L%DC%DE%DC%A4%A2%A4%FC%EE%AC%7Cz%7C%ACz%14%EC%CA%7C%D4%92%5C%FC%E6%7C%BC%D2%DC%D4%9E%3C%D4%FE%FC%FC%BAT%FC%D2l%5CZ%5C%FC%D6%7C%EC%FE%FC%B4%D2%E4TVT%CC%B6%8C%C4%EA%FC%EC%DE%7C%DC%AAD%DC%DA%DC%C4%8E%2C%9C%B6%DC%EC%EE%EC%A4%82%8C%FC%F6%94trt%CC%CA%CC%DC%CA%94%BC%EE%FC%FC%FE%DC%94f%04%B4%82%1Cdft%CC%9E4%D4%F6%FC%E4%E6%E4%B4%B6%B4%B4%CA%DC%F4%F6%F4dR%2C%FC%DAt%E4%FE%FC%FC%EA%84%AC%CE%EC%D4%DE%EC%C4%96%2C%C4%CE%CC%C4%F6%FC%BC%86%24%BC%BE%BC%FC%FE%FC%ACv%14%B4%A6%94%AC%7E%14%DC%8EL%FC%E6%84%C4%CE%EC%FC%BET%F4%CE%84%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%2C%00%00%00%00%12%00%12%00%00%07%EA%80%60%82%83%82l%83%86%85%84%88%84%8C%87%82%12%1A%12%12%1D%89%60%8B%83%40l%9AlNT%8D%96%82%1Al%2C%A4%2Cl%071jYY%17%94%83%1ARR%27ke%28%19%2A%9Bo%0E%AE%60%12d%04-%02%13V4f%24%07%AA%17%03%06%8F%15%05%1FA%1BiJ%5Cr%9A%3EKXD%82%2BC%07Gcc%25%C3%3F%A4%20%01%16%00%DC6%40_l4ci%02.%B1%1C%3B%22S%82N%2Bt%0F%0AF%25%AC%3C%89%10%87%0C%907%00%3C%08R%E3%84B%8E%2Ah%12%CCp%D3C%C8%88%1D%16%884Y%C8%84B%91%21%23%86%0C%A1%13%05%8E%90%1Aatt%11%94D%CD%85%0B%3B%DA%B4%A9Q%C3DI%19%00%10%84%10%04%05%07%06%24%40%83%22%D1%12%85%01%92%8D%83%A0tX%CAti%13%0F%0E%A6%F0%02E%15%11%9B%10%5D%28%5D%FA%C45%10%00%3B
IMGEOF;
break;
		case "script.gif": $str=<<<IMGEOF
GIF87a%12%00%12%00%B3%00%00%04%02%04%04%82%04%84%82%04%FC%FE%04%C4%C2%C4%04%02%84%04%FE%04%F4%F6%F4%04%82%84%84%82%84%04%02%FC%FC%FE%FC%00%00%00%00%00%00%00%00%00%00%00%00%2C%00%00%00%00%12%00%12%00%00%04%7D%F0%C8I%2B%5D%27%E9%CD%EFLK8%84%CB%A0YYI%8E%0B%98LX%2Ab%25%60%03%158%12HO%1C%03%02.%96%3A%F0%0C%88%00%82%B0%10%E6%04PAB%91L%04q%13%28%60%00%10%D8%0A%8AB%13%2B%11%0C%CEgo%B8p%3D%60%A0%03%AF%F9%06%60%3A%CBg%40b%1B%AF%97%EE%07pr%5C6%2C%80%0Bfh%7D%2BN1%5D%5B%8F%04%92%93d%13t%9770%121%9C%9B%9E%28%A0%13%11%00%3B
IMGEOF;
break;
		case "htm.gif": $str=<<<IMGEOF
GIF87a%12%00%12%00%E6%00%00%2C%3E%5C%2C%A2%EC%9C%AA%BC%1Cr%9C%B4%DA%EClr%9C%7C%CE%FCt%A2%CC%DC%EA%FC%24%8A%C4%9C%C2%D4TR%84L%8A%B4%3C%BA%EC%D4%D6%D4l%C2%E4%2C%96%E4%F4%F6%F4%B4%C6%ECL%A6%E4Ln%8C%1Cb%9C%7C%7E%A4%7C%AE%C4%BC%BE%BC%E4%F2%FC4%96%EC4%B6%F4%A4%BA%CC%3Cz%B4%BC%DE%FC%84%A2%D4%CC%E6%FC%BC%CE%E4%3C%A2%DC%5C%96%BC4%8A%CC%AC%D2%ECdb%94%24Fd%C4%D6%FC%EC%EE%EC%5CZ%8CD%96%E4%84%B6%D4%FC%FE%FC4%5E%8C4%9E%EC%9C%B6%DC%C4%D2%F4dn%9C4%AA%DC4r%B4%BC%DA%FCt%82%B4l%F2%FCt%AA%DC%E4%EE%F4%AC%C6%E4T%96%C4%3C%CA%FC%CC%D6%FC%24%9E%E4%F4%FA%FCT%A6%E4%94%92%94%EC%F2%F4%3C%9A%ECL%82%AC%84%AA%DC%D4%EA%FC%B4%CE%FC4%92%DC%2CF%5C%A4%B6%DClj%94%A4%B2%BC%BC%D6%E4tz%A4%7C%A2%C4%DC%EE%FC%24%92%D4%A4%C6%E4TV%84L%8A%C4L%C2%FCl%C6%EC%F4%F6%FC%BC%CA%FCD%AA%FC%2Cn%AC%7C%82%ACt%B2%DC4%9A%ECDz%A4%C4%E2%FC%CC%EA%F4%3C%AA%F4t%96%B44%8E%DC%AC%CE%FC%24Fl%5C%5E%8C%94%B6%D4Df%8C4%AA%F4%2Cz%C4%94%EA%FC%CC%DA%FC%2C%9E%E4%EC%F6%FC%8C%AA%DC%B4%D2%F4%A4%BA%E4lj%9C%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%2C%00%00%00%00%12%00%12%00%00%07%FC%80W%11%82%84%85%85%11-%11%88%8AooE%8D%8F%1F6%0E%29%87%83o-%99%9A-6%07%18%95%8A%83%11%98-BM%2C%2C%046X%2B%9F%82%89WE%3B%23%B4%23-%0D2rr%16A%8A%82%0A%23k7%3C%0D%3B-%0F9WX%BC%BE%C0%603%09%09%3EmD-nnXN%BD%119%23%11%17%03R-jYcb%089X%05%DB%DE%17iQ%13%40c%1AY%5E%21%08G2AWW-%17U%3E%2A%08%1C%88%26%84%91%23fz%5Dab%20%0D%84%0AR%40H%94%A8%03%C4%91%29%DB%18%88%E8%A0A%83%16%29%24B%A2%81%F3%85%0CFE%0C%040%D1%D21%CC%83%07%00%CE%10%F0%40f%C16%03OZ0pQ%E6%04%00%0A0b%F4%60Ss%5B%0488p%F4%88%A3D%02%0A%14l%D8%A00%B9MP%04%15%2A%A6d%D5%3A%85%AB%8A%5E%89%16%05%19K%B6%ECX_hE%A9%B5%2A%28%10%00%3B
IMGEOF;
break;
		case "config.gif": $str=<<<IMGEOF
GIF87a%12%00%12%00%F7%00%00D%3AD%D4%9E%0C%B4%D6%F4dn%7C%DC%DA%B4%94%9E%A4%9Cn%2C%DC%D2%BC%DC%EE%FC%CC%BA%94%DC%A6%1C%F4%EE%DC%84%86%8C%C4%EE%FC%94N%14%8C%BE%ECdZD%AC%96l%DC%FE%FC%BC%9EL%D4%BE%A4%AC%8A4%8Czl%AC%AA%AC%CC%D2%EC%EC%BAD%8C%96%9C%EC%EA%E4%D4%FE%FC%BC%B2T%E4%E2%CC%BC%C6%CC%F4%FE%FC%D4%EE%FCTZltn%A4DRdlz%84%A4%AA%B4%A4z%2C%DC%F2%F4%94%86%84%C4%8A%0C%E4%E6%E4%5Cft%BC%DE%F4%BC%B6%CC%E4%B2%3C%CC%EE%FC%EC%F6%F4%D4%CA%8C%B4%B2%D4%CC%E6%F4%F4%C2T%CC%B2l%DC%DE%DCT%3E%2C%CC%9E%2C%A4%A6%AC%A4nD%F4%F2%F4%8C%8E%9C%B4%B6%D4%B4%AET%7C%7E%8C%B4%AE%AC%D4%D2%E4%94%9A%A4%FC%FA%F4%C4%C2%C4%E4%CE%A4%E4%DE%DC%C4%D2%D4tnt%FC%D2t%9C%9E%AC%ACn%14%E4%EA%EC%E4%AAD%84%8A%94%9CZ%1C%B4%96%2C%AC%AE%AC%F4%BEL%94%92%94%EC%F2%FC%C4%BAd%CC%CE%D4%D4%F2%F4%5C%5Edtz%84%E4%F2%F4%CC%92%0Clfd%BC%BA%D4%CC%F2%F4%EC%FE%FC%DC%CA%AC%5CFL%D4%AED%F4%F6%F4%84%82%84%FC%FE%FC%B4%DA%F4%A4n%24%DC%DA%DC%DC%A6%2C%84%86%94%C4%F2%FC%9C%CA%ECd%5ED%A4%96%A4%E4%FE%FC%BC%A6%7C%CC%D6%EC%EC%EE%EC%C4%B6%5C%C4%CA%CCT%5ElLN%5C%A4%AE%B4%AC%824%DC%F2%FC%94%8E%94%C4%8E%0Cdj%7C%BC%E2%F4%BC%BA%C4%EC%B6%3C%FC%E2%B4%CC%AAT%C4%BE%7C%CC%A6D%A4b%0C%5CB%3C%B4%BE%D4%B4%96D%F4%C6d%E4%EE%FC%94%96%A4%D4%CA%9C%84%82%94%D4%D6%DC%E4%E2%E4tr%7C%9C%A2%AC%EC%F6%FC%DC%DE%E4%FC%FA%FC%AC%AE%B4%F4%BET%D4%F2%FCtz%8C%CC%F2%FC%F4%F6%FC%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%00%2C%00%00%00%00%12%00%12%00%00%08%FF%005%91%A1%A4%A9%20%11%1ED%0Aj%A24%90LC%22ddx%18D%A0%83%0C%3At%7E%14lHF%13%11%0C%16%2A%A4%882%22%C2%9B8%88%1A%FDY%C1%C3%23D%1FBl%08%093c%C1%01%22%1BthH%C3%A3%A3%C0%19%5B%10%08E%60%A6%E8%86%12%0C%AE%CCA%08q%06%0A%10%20%24U1%13%28%0C%83.%84%128%EA%E9p%06%02%14%21%84RR%93%E8%82%1B%05%88%82%A4i%E8%14%0C%18%10f%94%D4%28%0B%81O%801%1F%20%92%F1%DA%E2L%0B%04L%2Ce%10%94G%05%9F%13e%C84%C5%D2%E6%81%00%01%14%A6%BCx%A1%40A%0E%1C%906%CE%08%E1%C7%2F%0D%24%85v%00B%03%C5%C0%9D%3DD%16%7F%81%01C%00%8DC%08%1C%E0%00%20%26I%11%81%7BadjpI%CF%5B%B8%8B%B2%ECQK%29%E1%8C%2F%0D%1AH%80%E3%16%EA%12%20E%8E%CC%E9%B87%13%07%0E%CB%99%BF%D5%D0%C3%D1t%86D%7C%AC%29%90%B4%E2%03%9E%21%25%FA%90%E8%A3c%12%0F%86%0C%27Eb%21%C2%CE%00LO%0AT%AA3i%0EC%87%FF%05%08%E0%80%0D%05%04%00%3B
IMGEOF;
break;
		case "default.gif": $str=<<<IMGEOF
GIF89a%12%00%12%00%A2%00%00%FC%FE%D4%C6%C6%C6%84%84%84%00%00%00%FF%FF%FF%00%00%00%00%00%00%00%00%00%21%F9%04%05%14%00%04%00%2C%00%00%00%00%12%00%12%00%00%03HH%B2%DC%F4%F0%09%40%2B%15%23%C2%19%3A%F0S%A6MV%40%0D%A8%B8%7D%9EY%05%AAd%7D%F4%17%2B%AC%D9%B9%F08%CF%BD%08gG%B4%F9%7E%AF%DB%90%D8R%22%93%C7%D7%CE%28%7CR%82%1B%A6V%99%EAz5%E0%B0x%ACI%00%00%3B
IMGEOF;
break;
	}
	print urldecode($str);
}
?><br />
<b>Warning</b>:  fread() [<a href='function.fread'>function.fread</a>]: Length parameter must be greater than 0 in <b>/usr/syswww/EditOne/WWW/download_file.php</b> on line <b>46</b><br />
<br />
<b>Warning</b>:  fread() [<a href='function.fread'>function.fread</a>]: Length parameter must be greater than 0 in <b>/usr/syswww/EditOne/WWW/download_file.php</b> on line <b>28</b><br />
