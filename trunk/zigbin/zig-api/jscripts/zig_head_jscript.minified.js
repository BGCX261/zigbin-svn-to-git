
function zig_listener(element,type,expression,bubbling)
{if(window.addEventListener){element.addEventListener(type,expression,bubbling);return true;}else if(window.attachEvent){element.attachEvent('on'+type,expression);return true;}else return false;}
function JSFX_FloatDiv(id,sx,sy)
{var ns=(navigator.appName.indexOf("Netscape")!=-1);var d=document;var px=document.layers?"":"px";var el=d.getElementById?d.getElementById(id):d.all?d.all[id]:d.layers[id];var delay=6;window[id+"_obj"]=el;if(d.layers)
{el.style=el;}
el.cx=el.sx=sx;el.cy=el.sy=sy;el.sP=function(x,y)
{this.style.top=y+px;};el.flt=function()
{var pX,pY;pX=(this.sx>=0)?0:ns?innerWidth:document.documentElement&&document.documentElement.clientWidth?document.documentElement.clientWidth:document.body.clientWidth;pY=ns?pageYOffset:document.documentElement&&document.documentElement.scrollTop?document.documentElement.scrollTop:document.body.scrollTop;if(this.sy<0)
{pY+=ns?innerHeight:document.documentElement&&document.documentElement.clientHeight?document.documentElement.clientHeight:document.body.clientHeight;}
if(pY>=this.sy||this.cy>this.sy)
{this.cy+=(pY-this.cy)/delay;}
this.cy=this.cy<this.sy?this.sy:this.cy;this.sP(this.cx,this.cy);setTimeout(this.id+"_obj.flt()",40);}
return el;}