
DatePickerControl.defaultFormat="Y-m-d";DatePickerControl.submitFormat="";DatePickerControl.offsetY=1;DatePickerControl.offsetX=0;DatePickerControl.todayText="today";DatePickerControl.buttonTitle="calendar";DatePickerControl.buttonPosition="in";DatePickerControl.buttonOffsetX=0;DatePickerControl.buttonOffsetY=0;DatePickerControl.closeOnTodayBtn=true;DatePickerControl.defaultTodaySel=true;DatePickerControl.autoShow=false;DatePickerControl.firstWeekDay=0;DatePickerControl.weekend=[0,6];DatePickerControl.weekNumber=false;DatePickerControl.Months=["January","February","March","April","May","June","July","August","September","October","November","December"];DatePickerControl.Days=["Sun","Mon","Tue","Wed","Thu","Fri","Sat"];DatePickerControl.useTrickyBG=false;if(navigator.userAgent.indexOf("MSIE")>1){DatePickerControl.useTrickyBG=true;DatePickerControl.offsetY=0;DatePickerControl.offsetX=-1;DatePickerControl.buttonOffsetX=-4;DatePickerControl.buttonOffsetY=-2;if(document.getElementsByTagName("html")[0].getAttribute("xmlns")!=null){DatePickerControl.offsetY=16;DatePickerControl.offsetX=10;DatePickerControl.buttonOffsetX=8;DatePickerControl.buttonOffsetY=14;}}
DatePickerControl.editIdPrefix="cal_";DatePickerControl.displayed=false;DatePickerControl.HIDE_TIMEOUT=300;DatePickerControl.hideTimeout=null;DatePickerControl.buttonIdPrefix="CALBUTTON";DatePickerControl.dayIdPrefix="CALDAY";DatePickerControl.currentDay=1;DatePickerControl.originalValue="";DatePickerControl.calFrameId="calendarframe";DatePickerControl.submitByKey=false;DatePickerControl.dayOfWeek=0;DatePickerControl.firstFocused=false;DatePickerControl.hideCauseBlur=true;DatePickerControl.onSubmitAsigned=false;DatePickerControl.minDate=null;DatePickerControl.maxDate=null;DatePickerControl.DOMonth=[31,28,31,30,31,30,31,31,30,31,30,31];DatePickerControl.lDOMonth=[31,29,31,30,31,30,31,31,30,31,30,31];function DatePickerControl()
{}
DatePickerControl.init=function()
{if(!document.getElementById("CalendarPickerControl"))
{this.setGlobalParams();this.calBG=null;if(this.useTrickyBG){this.calBG=document.createElement("iframe");this.calBG.id="CalendarPickerControlBG";this.calBG.style.zIndex="49999";this.calBG.style.position="absolute";this.calBG.style.display="none";this.calBG.style.border="0px solid transparent";document.body.appendChild(this.calBG);}
this.calContainer=document.createElement("div");this.calContainer.id="CalendarPickerControl";this.calContainer.style.zIndex="50000";this.calContainer.style.position="absolute";this.calContainer.style.display="none";document.body.appendChild(this.calContainer);if(this.calContainer.addEventListener){this.calContainer.addEventListener("click",cal_onContainerClick,false);window.addEventListener("resize",cal_onWindowResize,false);}
else if(this.calContainer.attachEvent){this.calContainer.attachEvent("onclick",cal_onContainerClick);window.attachEvent("onresize",cal_onWindowResize);}}}
DatePickerControl.setGlobalParams=function()
{var obj=document.getElementById("cal_DEFAULT_FORMAT");if(obj)this.defaultFormat=obj.value;obj=document.getElementById("cal_SUBMIT_FORMAT");if(obj)this.submitFormat=obj.value;obj=document.getElementById("cal_FIRST_WEEK_DAY");if(obj)this.firstWeekDay=(obj.value<0||obj.value>6)?0:parseInt(obj.value);obj=document.getElementById("cal_WEEKEND_DAYS");if(obj)eval("this.weekend = "+obj.value);obj=document.getElementById("cal_AUTO_SHOW");if(obj)this.autoShow=obj.value=="true";obj=document.getElementById("cal_DEFAULT_TODAY");if(obj)this.defaultTodaySel=obj.value=="true";obj=document.getElementById("cal_CALENDAR_OFFSET_X");if(obj)this.offsetX=parseInt(obj.value);obj=document.getElementById("cal_CALENDAR_OFFSET_Y");if(obj)this.offsetY=parseInt(obj.value);obj=document.getElementById("cal_TODAY_TEXT");if(obj)this.todayText=obj.value;obj=document.getElementById("cal_BUTTON_TITLE");if(obj)this.buttonTitle=obj.value;obj=document.getElementById("cal_BUTTON_POSITION");if(obj)this.buttonPosition=obj.value;obj=document.getElementById("cal_BUTTON_OFFSET_X");if(obj)this.buttonOffsetX=parseInt(obj.value);obj=document.getElementById("cal_BUTTON_OFFSET_Y");if(obj)this.buttonOffsetY=parseInt(obj.value);obj=document.getElementById("cal_WEEK_NUMBER");if(obj)this.weekNumber=obj.value=="true";obj=document.getElementById("cal_MONTH_NAMES");if(obj)eval("this.Months = "+obj.value);obj=document.getElementById("cal_DAY_NAMES");if(obj)eval("this.Days = "+obj.value);}
function cal_autoInit()
{DatePickerControl.init();}
if(window.addEventListener)
{window.addEventListener("load",cal_autoInit,false);}
else if(window.attachEvent)
{window.attachEvent("onload",cal_autoInit);}
DatePickerControl.createButton=function(input,useId)
{return true;}
DatePickerControl.show=function()
{if(!this.displayed){var input=this.inputControl;if(input==null)return;if(input.disabled)return;var top=getObject.getSize("offsetTop",input);var left=getObject.getSize("offsetLeft",input);var calframe=document.getElementById(this.calFrameId);this.calContainer.style.top=top+input.offsetHeight+this.offsetY+"px";this.calContainer.style.left=left+this.offsetX+"px";this.calContainer.style.display="none";this.calContainer.style.visibility="visible";this.calContainer.style.display="block";this.calContainer.style.height=calframe.offsetHeight;if(this.calBG){this.calBG.style.top=this.calContainer.style.top;this.calBG.style.left=this.calContainer.style.left;this.calBG.style.display="none";this.calBG.style.visibility="visible";this.calBG.style.display="block";this.calBG.style.width=this.calContainer.offsetWidth;if(calframe){this.calBG.style.height=calframe.offsetHeight;}}
this.displayed=true;input.focus();}}
DatePickerControl.hide=function()
{if(this.displayed){this.calContainer.style.visibility="hidden";this.calContainer.style.left=-1000;this.calContainer.style.top=-1000;if(this.calBG){this.calBG.style.visibility="hidden";this.calBG.style.left=-1000;this.calBG.style.top=-1000;}
this.inputControl.value=this.originalValue;this.displayed=false;}}
DatePickerControl.getMonthName=function(monthNumber)
{return this.Months[monthNumber];}
DatePickerControl.getDaysOfMonth=function(monthNo,p_year)
{if(this.isLeapYear(p_year)){return this.lDOMonth[monthNo];}
else{return this.DOMonth[monthNo];}}
DatePickerControl.calcMonthYear=function(p_Month,p_Year,incr)
{var ret_arr=new Array();if(incr==-1){if(p_Month==0){ret_arr[0]=11;ret_arr[1]=parseInt(p_Year)-1;}
else{ret_arr[0]=parseInt(p_Month)-1;ret_arr[1]=parseInt(p_Year);}}
else if(incr==1){if(p_Month==11){ret_arr[0]=0;ret_arr[1]=parseInt(p_Year)+1;}
else{ret_arr[0]=parseInt(p_Month)+1;ret_arr[1]=parseInt(p_Year);}}
return ret_arr;}
DatePickerControl.getAllCode=function()
{var vCode="";vCode+="<table class='calframe' id='"+this.calFrameId+"'>";vCode+=this.getHeaderCode();vCode+=this.getDaysHeaderCode();vCode+=this.getDaysCode();vCode+="</table>";return vCode;}
DatePickerControl.getHeaderCode=function()
{var prevMMYYYY=this.calcMonthYear(this.month,this.year,-1);var prevMM=prevMMYYYY[0];var prevYYYY=prevMMYYYY[1];var nextMMYYYY=this.calcMonthYear(this.month,this.year,1);var nextMM=nextMMYYYY[0];var nextYYYY=nextMMYYYY[1];var gNow=new Date();var vCode="";var numberCols=this.weekNumber?8:7;vCode+="<tr><td colspan='"+numberCols+"' class='monthname'>";vCode+="<span title='"+this.Months[this.month]+" "+(parseInt(this.year)-1)+"' class='yearbutton' ";vCode+="onclick='DatePickerControl.build("+this.month+", "+(parseInt(this.year)-1)+");return false;'>&laquo;</span>";vCode+="&nbsp;"+this.year+"&nbsp;";vCode+="<span title='"+this.Months[this.month]+" "+(parseInt(this.year)+1)+"' class='yearbutton' ";vCode+="onclick='DatePickerControl.build("+this.month+", "+(parseInt(this.year)+1)+");return false;'>&raquo;</span>";vCode+="</td></tr>";vCode+="<tr><td style='border-width:0px' colspan='"+numberCols+"'>";vCode+="<table class='navigation' width='100%'><tr>";vCode+="<td class='navbutton' title='"+this.Months[prevMM]+" "+prevYYYY+"' ";vCode+="onclick='DatePickerControl.build("+prevMM+", "+prevYYYY+");return false;'>&lt;&lt;</td>";vCode+="<td class='navbutton' title='"+gNow.getDate()+" "+this.Months[gNow.getMonth()]+" "+gNow.getFullYear()+"' ";vCode+="onclick='DatePickerControl.build("+gNow.getMonth()+", "+gNow.getFullYear()+");DatePickerControl.selectToday();return false;'>";vCode+=this.monthName+"</td>";vCode+="<td class='navbutton' title='"+this.Months[nextMM]+" "+nextYYYY+"' ";vCode+="onclick='DatePickerControl.build("+nextMM+", "+nextYYYY+");return false;'>&gt;&gt;</td>";vCode+="</tr></table>";vCode+="</td></tr>";return vCode;}
DatePickerControl.getDaysHeaderCode=function()
{var vCode="";vCode=vCode+"<tr>";if(this.weekNumber){vCode+="<td class='weeknumber'>&nbsp;</td>"}
for(i=this.firstWeekDay;i<this.firstWeekDay+7;i++){vCode+="<td class='dayname' width='14%'>"+this.Days[i%7]+"</td>";}
vCode=vCode+"</tr>";return vCode;}
DatePickerControl.getDaysCode=function()
{var vDate=new Date();vDate.setDate(1);vDate.setMonth(this.month);vDate.setFullYear(this.year);var vFirstDay=vDate.getDay();var vDay=1;var vLastDay=this.getDaysOfMonth(this.month,this.year);var vOnLastDay=0;var vCode="";this.dayOfWeek=vFirstDay;var prevm=this.month==0?11:this.month-1;var prevy=this.prevm==11?this.year-1:this.year;prevmontdays=this.getDaysOfMonth(prevm,prevy);vFirstDay=(vFirstDay==0&&this.firstWeekDay)?7:vFirstDay;if(this.weekNumber){var week=this.getWeekNumber(this.year,this.month,1);}
vCode+="<tr>";if(this.weekNumber){vCode+="<td class='weeknumber'>"+week+"</td>";}
for(i=this.firstWeekDay;i<vFirstDay;i++){vCode=vCode+"<td class='dayothermonth'>"+(prevmontdays-vFirstDay+i+1)+"</td>";}
for(j=vFirstDay-this.firstWeekDay;j<7;j++){if(this.isInRange(vDay)){classname=this.getDayClass(vDay,j);vCode+="<td class='"+classname+"' class_orig='"+classname+"' "+"onClick='DatePickerControl.writeDate("+vDay+")' id='"+this.dayIdPrefix+vDay+"'>"+vDay+"</td>";}
else{vCode+="<td class='dayothermonth'>"+vDay+"</td>";}
vDay++;}
vCode=vCode+"</tr>";for(k=2;k<7;k++){vCode=vCode+"<tr>";if(this.weekNumber){week++;if(week>=53)week=1;vCode+="<td class='weeknumber'>"+week+"</td>";}
for(j=0;j<7;j++){if(this.isInRange(vDay)){classname=this.getDayClass(vDay,j);vCode+="<td class='"+classname+"' class_orig='"+classname+"' "+"onClick='DatePickerControl.writeDate("+vDay+")' id='"+this.dayIdPrefix+vDay+"'>"+vDay+"</td>";}
else{vCode+="<td class='dayothermonth'>"+vDay+"</td>";}
vDay++;if(vDay>vLastDay){vOnLastDay=1;break;}}
if(j==6)
vCode+="</tr>";if(vOnLastDay==1)
break;}
for(m=1;m<(7-j);m++){vCode+="<td class='dayothermonth'>"+m+"</td>";}
return vCode;}
DatePickerControl.getDayClass=function(vday,dayofweek)
{var gNow=new Date();var vNowDay=gNow.getDate();var vNowMonth=gNow.getMonth();var vNowYear=gNow.getFullYear();if(vday==vNowDay&&this.month==vNowMonth&&this.year==vNowYear){return"today";}
else{var realdayofweek=(7+dayofweek+this.firstWeekDay)%7;for(i=0;i<this.weekend.length;i++){if(realdayofweek==this.weekend[i]){return"weekend";}}
return"day";}}
DatePickerControl.formatData=function(p_day)
{var vData;var vMonth=1+this.month;vMonth=(vMonth.toString().length<2)?"0"+vMonth:vMonth;var vMon=this.getMonthName(this.month).substr(0,3).toUpperCase();var vFMon=this.getMonthName(this.month).toUpperCase();var vY4=new String(this.year);var vY2=new String(this.year).substr(2,2);var vDD=(p_day.toString().length<2)?"0"+p_day:p_day;var time=new Date();var curr_hour=time.getHours();if(curr_hour<12)
{a_p="AM";}
else
{a_p="PM";}
if(curr_hour==0)
{curr_hour=12;}
if(curr_hour>12)
{curr_hour=curr_hour-12;}
var curr_min=time.getMinutes();curr_min=curr_min+"";if(curr_min.length==1)
{curr_min="0"+curr_min;}
var curr_sec=time.getSeconds();curr_sec=curr_sec+"";if(curr_sec.length==1)
{curr_sec="0"+curr_sec;}
switch(this.format){case"m/d/Y":vData=vMonth+"/"+vDD+"/"+vY4;break;case"m/d/y":vData=vMonth+"/"+vDD+"/"+vY2;break;case"m-d-Y":vData=vMonth+"-"+vDD+"-"+vY4;break;case"m-d-y":vData=vMonth+"-"+vDD+"-"+vY2;break;case"Y-m-d":vData=vY4+"-"+vMonth+"-"+vDD;break;case"Y/m/d":vData=vY4+"/"+vMonth+"/"+vDD;break;case"d/M/Y":vData=vDD+"/"+vMon+"/"+vY4;break;case"d/M/y":vData=vDD+"/"+vMon+"/"+vY2;break;case"d-M-Y":vData=vDD+"-"+vMon+"-"+vY4;break;case"d-M-y":vData=vDD+"-"+vMon+"-"+vY2;break;case"d/F/Y":vData=vDD+"/"+vFMon+"/"+vY4;break;case"d/F/y":vData=vDD+"/"+vFMon+"/"+vY2;break;case"d-F-Y":vData=vDD+"-"+vFMon+"-"+vY4;break;case"d-F-y":vData=vDD+"-"+vFMon+"-"+vY2;break;case"d/m/Y":vData=vDD+"/"+vMonth+"/"+vY4;break;case"d/m/y":vData=vDD+"/"+vMonth+"/"+vY2;break;case"d-m-Y":vData=vDD+"-"+vMonth+"-"+vY4;break;case"d-m-y":vData=vDD+"-"+vMonth+"-"+vY2;break;case"d.m.Y":vData=vDD+"."+vMonth+"."+vY4;break;case"d.m.y":vData=vDD+"."+vMonth+"."+vY2;break;default:vData=vMonth+"/"+vDD+"/"+vY4;}
return vData;}
DatePickerControl.getDateFromControl=function(ctrl)
{if(ctrl==null)ctrl=this.inputControl;var value=ctrl.value;var format=document.getElementById("zig_calendar_"+ctrl.id).getAttribute("datepicker_format");return this.getDateFromString(value,format.toString());}
DatePickerControl.getDateFromString=function(strdate,format)
{var aDate=new Date();var day,month,year;if(strdate==""||format=="")return aDate;strdate=strdate.replace("/","@").replace("/","@");strdate=strdate.replace("-","@").replace("-","@");strdate=strdate.replace(".","@").replace(".","@");if(strdate.indexOf("/")>=0||strdate.indexOf("-")>=0||strdate.indexOf(".")>=0)return aDate;var data=strdate.split("@");if(data.length!=3)return aDate;for(i=0;i<3;i++){data[i]=parseFloat(data[i]);if(isNaN(data[i]))return aDate;}
aDate.setDate(1);if(format.substring(0,1).toUpperCase()=="D"){aDate.setFullYear(this.yearTwo2Four(data[2]));aDate.setMonth(data[1]-1);aDate.setDate(data[0]);}
else if(format.substring(0,1).toUpperCase()=="Y"){aDate.setFullYear(this.yearTwo2Four(data[0]));aDate.setMonth(data[1]-1);aDate.setDate(data[2]);}
else if(format.substring(0,1).toUpperCase()=="M"){aDate.setFullYear(this.yearTwo2Four(data[2]));aDate.setMonth(data[0]-1);aDate.setDate(data[1]);}
return aDate;}
DatePickerControl.yearTwo2Four=function(year)
{if(year<99){if(year>=30){year+=1900;}
else{year+=2000;}}
return year;}
DatePickerControl.writeDate=function(day)
{var d=this.formatData(day);this.inputControl.value=d;this.originalValue=d;this.hide();if(DatePickerControl.onSelect)DatePickerControl.onSelect(this.inputControl.id);this.firstFocused=true;this.inputControl.focus();}
DatePickerControl.writeCurrentDate=function()
{var d=this.formatData(this.currentDay);this.inputControl.value=d;}
DatePickerControl.build=function(m,y)
{var bkm=this.month;var bky=this.year;var calframe=document.getElementById(this.calFrameId);if(m==null){var now=new Date();this.month=now.getMonth();this.year=now.getFullYear();}
else{this.month=m;this.year=y;}
if(!this.isInRange(null)){this.month=bkm;this.year=bky;}
if(!this.isInRange(this.currentDay)){if(this.minDate&&this.currentDay<this.minDate.getDate())this.currentDay=this.minDate.getDate();if(this.maxDate&&this.currentDay>this.maxDate.getDate())this.currentDay=this.maxDate.getDate();}
this.monthName=this.Months[this.month];var code=this.getAllCode();writeLayer(this.calContainer.id,null,code);if(this.calContainer&&calframe)this.calContainer.style.height=calframe.offsetHeight;this.firstFocused=true;this.inputControl.focus();this.selectDay(this.currentDay);}
DatePickerControl.buildPrev=function()
{if(!this.displayed)return;var prevMMYYYY=this.calcMonthYear(this.month,this.year,-1);var prevMM=prevMMYYYY[0];var prevYYYY=prevMMYYYY[1];this.build(prevMM,prevYYYY);}
DatePickerControl.buildNext=function()
{if(!this.displayed)return;var nextMMYYYY=this.calcMonthYear(this.month,this.year,1);var nextMM=nextMMYYYY[0];var nextYYYY=nextMMYYYY[1];this.build(nextMM,nextYYYY);}
DatePickerControl.selectToday=function()
{var now=new Date();var today=now.getDate();if(!this.isInRange(today))return;if(this.closeOnTodayBtn){this.currentDay=today;this.writeDate(this.currentDay);}
else{this.selectDay(today);}}
DatePickerControl.selectDay=function(day)
{if(!this.displayed)return;if(!this.isInRange(day)){return;}
var n=this.currentDay;var max=this.getDaysOfMonth(this.month,this.year);if(day>max)return;var newDayObject=document.getElementById(this.dayIdPrefix+day);var currentDayObject=document.getElementById(this.dayIdPrefix+this.currentDay);if(currentDayObject){currentDayObject.className=currentDayObject.getAttribute("class_orig");}
if(newDayObject){newDayObject.className="current";this.currentDay=day;this.writeCurrentDate();}}
DatePickerControl.selectPrevDay=function(decr)
{if(!this.displayed)return;var n=this.currentDay;var max=this.getDaysOfMonth(this.month,this.year);var prev=n-decr;if(prev<=0){if(decr==7){n=(n+this.dayOfWeek)+28-this.dayOfWeek;n--;prev=n>max?n-7:n;}
else{prev=max;}}
this.selectDay(prev);}
DatePickerControl.selectNextDay=function(incr)
{if(!this.displayed)return;var n=this.currentDay;var max=this.getDaysOfMonth(this.month,this.year);var next=n+incr;if(next>max){if(incr==7){n=((n+this.dayOfWeek)%7)-this.dayOfWeek;next=n<0?n+7:n;next++;}
else{next=1;}}
this.selectDay(next);}
DatePickerControl.showForEdit=function(edit)
{if(this.displayed)return;if(edit==null)return;if(edit.disabled)return;this.inputControl=edit;this.originalValue=edit.value;this.setupRange();var format=this.inputControl.getAttribute("datepicker_format");if(format==null)format=this.defaultFormat;this.format=format;if(this.validate(edit.value,format)){var date=this.getDateFromControl();this.currentDate=date;this.build(date.getMonth(),date.getFullYear());this.currentDay=date.getDate();}
else{edit.value="";this.originalValue="";this.currentDate=null;if(this.defaultTodaySel){this.currentDay=new Date().getDate();}
else{this.currentDay=1;}
this.build(null,null);}
var currentDayObject=document.getElementById(this.dayIdPrefix+this.currentDay);if(currentDayObject)currentDayObject.className="current";this.writeCurrentDate();this.show();}
DatePickerControl.isInRange=function(day)
{if(!this.minDate&&!this.maxDate)return true;if(day){var aDate=new Date();aDate.setFullYear(this.year);aDate.setMonth(this.month);aDate.setDate(day);if(this.minDate){if(this.compareDates(aDate,this.minDate)<0)return false;}
if(this.maxDate){if(this.compareDates(aDate,this.maxDate)>0)return false;}}
else{var currentym=parseInt(this.year.toString()+(this.month<10?"0"+this.month.toString():this.month.toString()));var m;if(this.minDate){m=this.minDate.getMonth();var minym=parseInt(this.minDate.getFullYear().toString()+(m<10?"0"+m.toString():m.toString()));if(currentym<minym)return false;}
if(this.maxDate){m=this.maxDate.getMonth();var maxym=parseInt(this.maxDate.getFullYear().toString()+(m<10?"0"+m.toString():m.toString()));if(currentym>maxym)return false;}}
return true;}
DatePickerControl.setupRange=function()
{var edit=this.inputControl;var format=edit.getAttribute("datepicker_format");var min=edit.getAttribute("datepicker_min");this.minDate=min?this.getDateFromString(min,format):null;var max=edit.getAttribute("datepicker_max");this.maxDate=max?this.getDateFromString(max,format):null;if(this.maxDate&&this.minDate){if(this.maxDate.getTime()<this.minDate.getTime()){var tmp=this.maxDate;this.maxDate=this.minDate;this.minDate=tmp;}}}
DatePickerControl.compareDates=function(d1,d2)
{var m=d1.getMonth();var d=d1.getDate();var s1=d1.getFullYear().toString()+(m<10?"0"+m.toString():m.toString())+(d<10?"0"+d.toString():d.toString());m=d2.getMonth();d=d2.getDate();var s2=d2.getFullYear().toString()+(m<10?"0"+m.toString():m.toString())+(d<10?"0"+d.toString():d.toString());var n1=parseInt(s1);var n2=parseInt(s2);return n1-n2;}
DatePickerControl.validate=function(strdate,format)
{var dateRegExp;var separator;var d,m,y;var od=this.currentDay,om=this.month,oy=this.year;if(strdate=="")return false;if(format.substring(0,1).toUpperCase()=="D"){dateRegExp=/^\d{1,2}(\-|\/|\.)\d{1,2}\1\d{2,4}$/}
else if(format.substring(0,1).toUpperCase()=="Y"){dateRegExp=/^\d{2,4}(\-|\/|\.)\d{1,2}\1\d{1,2}$/}
else if(format.substring(0,1).toUpperCase()=="M"){dateRegExp=/^\d{1,2}(\-|\/|\.)\d{1,2}\1\d{2,4}$/}
if(!dateRegExp.test(strdate)){return false;}
separator=(strdate.indexOf("/")>1)?"/":((strdate.indexOf("-")>1)?"-":".");var datearray=strdate.split(separator);if(format.substring(0,1).toUpperCase()=="D"){d=parseFloat(datearray[0]);m=parseFloat(datearray[1]);y=parseFloat(datearray[2]);}
else if(format.substring(0,1).toUpperCase()=="Y"){d=parseFloat(datearray[2]);m=parseFloat(datearray[1]);y=parseFloat(datearray[0]);}
else if(format.substring(0,1).toUpperCase()=="M"){d=parseFloat(datearray[1]);m=parseFloat(datearray[0]);y=parseFloat(datearray[2]);}
if(m<1||m>12)return false;if(d>this.getDaysOfMonth(m-1,y))return false;this.month=m;this.year=y;var res=this.isInRange(d);this.month=om;this.year=oy;return res;}
DatePickerControl.isLeapYear=function(year)
{if((year%4)==0){if((year%100)==0&&(year%400)!=0){return false;}
return true;}
return false;}
function cal_onButtonClick(event)
{DatePickerControl.onButtonClick(event);}
DatePickerControl.onButtonClick=function(event)
{if(!this.displayed)
{if(event==null)event=window.event;var button=(event.srcElement)?event.srcElement:event.originalTarget;var input=document.getElementById(button.getAttribute("datepicker_inputid"));this.showForEdit(input);}
else
{this.hide();}}
function cal_onContainerClick(event){DatePickerControl.onContainerClick(event);}
DatePickerControl.onContainerClick=function(event)
{if(event==null)event=window.event;if(this.hideTimeout){clearTimeout(this.hideTimeout);this.hideTimeout=null;}
this.inputControl.focus();return false;}
function cal_onEditControlKeyUp(event){DatePickerControl.onEditControlKeyUp(event);}
DatePickerControl.onEditControlKeyUp=function(event)
{if(event==null)event=window.event;var edit=event.srcElement?event.srcElement:event.originalTarget;var kc=event.charCode?event.charCode:event.which?event.which:event.keyCode;switch(kc){case 37:this.selectPrevDay(1);break;case 38:this.selectPrevDay(7);break;case 39:this.selectNextDay(1);break;case 40:if(!this.displayed){this.showForEdit(edit);}
else{this.selectNextDay(7);break;}
break;case 27:this.hide();break;case 33:if((event.modifiers&Event.SHIFT_MASK)||(event.shiftKey)){this.build(this.month,parseInt(this.year)-1);}
else{this.buildPrev();}
break;case 34:if((event.modifiers&Event.SHIFT_MASK)||(event.shiftKey)){this.build(this.month,parseInt(this.year)+1);}
else{this.buildNext();}
break;case 13:if(this.displayed&&this.currentDay>0&&this.submitByKey){this.writeDate(this.currentDay);}
break;}
return false;}
function cal_onEditControlKeyDown(event){DatePickerControl.onEditControlKeyDown(event);}
DatePickerControl.onEditControlKeyDown=function(event)
{if(event==null)event=window.event;var edit=event.srcElement?event.srcElement:event.originalTarget;var kc=event.charCode?event.charCode:event.which?event.which:event.keyCode;if(kc>=65&&kc<=90){if(event.stopPropagation)event.stopPropagation();if(event.preventDefault)event.preventDefault();event.returnValue=false;event.cancelBubble=true;return false;}
switch(kc){case 13:this.submitByKey=true;break;case 9:case 32:if(this.displayed&&this.currentDay>0){this.writeDate(this.currentDay);}
break;}}
function cal_onEditControlKeyPress(event){DatePickerControl.onEditControlKeyPress(event);}
DatePickerControl.onEditControlKeyPress=function(event)
{if(event==null)event=window.event;var edit=event.srcElement?event.srcElement:event.originalTarget;var kc=event.charCode?event.charCode:event.which?event.which:event.keyCode;if(!((kc<32)||(kc>44&&kc<58))){if(event.stopPropagation)event.stopPropagation();if(event.preventDefault)event.preventDefault();event.returnValue=false;event.cancelBubble=true;return false;}}
function cal_onEditControlBlur(event){DatePickerControl.onEditControlBlur(event);}
DatePickerControl.onEditControlBlur=function(event)
{if(event==null)event=window.event;if(!this.hideTimeout){this.hideTimeout=setTimeout("DatePickerControl.hide()",this.HIDE_TIMEOUT);}
this.firstFocused=false;this.hideCauseBlur=true;}
function cal_onEditControlChange(event){DatePickerControl.onEditControlChange(event);}
DatePickerControl.onEditControlChange=function(event)
{}
function cal_onEditControlFocus(event){DatePickerControl.onEditControlFocus(event);}
DatePickerControl.onEditControlFocus=function(event)
{if(event==null)event=window.event;var edit=(event.srcElement)?event.srcElement:event.originalTarget;this.setupRange();if((!this.displayed||this.hideCauseBlur)&&this.autoShow&&!this.firstFocused){clearTimeout(this.hideTimeout);this.hideTimeout=null;this.firstFocused=true;if(this.hideCauseBlur){this.hideCauseBlur=false;this.hide();}
this.showForEdit(edit);}
else if(this.inputControl&&this.inputControl.id!=edit.id){this.hide();}
else if(this.hideTimeout){clearTimeout(this.hideTimeout);this.hideTimeout=null;}}
function cal_onFormSubmit(event){DatePickerControl.onFormSubmit(event);}
DatePickerControl.onFormSubmit=function(event)
{if(this.submitByKey){this.submitByKey=false;if(this.displayed&&this.currentDay>0){this.writeDate(this.currentDay);if(event==null)event=window.event;var theForm=(event.srcElement)?event.srcElement:event.originalTarget;if(event.stopPropagation)event.stopPropagation();if(event.preventDefault)event.preventDefault();event.returnValue=false;event.cancelBubble=true;return false;}}
this.reformatOnSubmit();}
DatePickerControl.reformatOnSubmit=function()
{if(this.submitFormat=="")return true;var inputControls=document.getElementsByTagName("input");var inputsLength=inputControls.length;var i;for(i=0;i<inputsLength;i++){if(inputControls[i].type.toLowerCase()=="text"){var editctrl=inputControls[i];if(editctrl.value=="")continue;var isdpc=editctrl.getAttribute("isdatepicker");if(isdpc&&isdpc=="true"){var thedate=this.getDateFromControl(editctrl);var res=this.submitFormat.replace("DD",thedate.getDate());var mo=thedate.getMonth()+1;res=res.replace("MM",mo.toString());if(this.submitFormat.indexOf("YYYY")>=0){res=res.replace("YYYY",thedate.getFullYear());}
else{res=res.replace("YY",thedate.getFullYear());}
editctrl.value=res;}}}
return true;}
function cal_formSubmit()
{var res=DatePickerControl.reformatOnSubmit();if(this.submitOrig){res=this.submitOrig();}
return res;}
function cal_onWindowResize(event){DatePickerControl.onWindowResize(event);}
DatePickerControl.onWindowResize=function(event)
{this.relocate();}
DatePickerControl.relocateButtons=function()
{return;var divElements=document.getElementsByTagName("div");for(key in divElements){if(divElements[key].id&&divElements[key].id.indexOf(this.buttonIdPrefix)==0){var calButton=divElements[key];if(calButton.style.display=='none')continue;var input=document.getElementById(calButton.getAttribute("datepicker_inputid"));if(input.style.display=='none'||input.offsetTop==0)continue;var nTop=getObject.getSize("offsetTop",input);var nLeft=getObject.getSize("offsetLeft",input);calButton.style.top=(nTop+Math.floor((input.offsetHeight-calButton.offsetHeight)/2)+this.buttonOffsetY)+"px";var btnOffX=Math.floor((input.offsetHeight-calButton.offsetHeight)/2);if(this.buttonPosition=="in"){calButton.style.left=(nLeft+input.offsetWidth-calButton.offsetWidth-btnOffX+this.buttonOffsetX)+"px";}
else{calButton.style.left=(nLeft+input.offsetWidth+btnOffX+this.buttonOffsetX)+"px";}}}}
DatePickerControl.relocate=function()
{if(this.displayed){var input=this.inputControl;if(input==null)return;var top=getObject.getSize("offsetTop",input);var left=getObject.getSize("offsetLeft",input);this.calContainer.style.top=top+input.offsetHeight+this.offsetY+"px";this.calContainer.style.left=left+this.offsetX+"px";if(this.calBG){this.calBG.style.top=this.calContainer.style.top;this.calBG.style.left=this.calContainer.style.left;}}}
DatePickerControl.getWeekNumber=function(year,month,day)
{var when=new Date(year,month,day);var newYear=new Date(year,0,1);var offset=7+1-newYear.getDay();if(offset==8)offset=1;var daynum=((Date.UTC(y2k(year),when.getMonth(),when.getDate(),0,0,0)-Date.UTC(y2k(year),0,1,0,0,0))/1000/60/60/24)+1;var weeknum=Math.floor((daynum-offset+7)/7);if(weeknum==0){year--;var prevNewYear=new Date(year,0,1);var prevOffset=7+1-prevNewYear.getDay();if(prevOffset==2||prevOffset==8)weeknum=53;else weeknum=52;}
return weeknum;}
function y2k(number){return(number<1000)?number+1900:number;}
function getObject(sId)
{if(bw.dom){this.hElement=document.getElementById(sId);this.hStyle=this.hElement.style;}
else if(bw.ns4){this.hElement=document.layers[sId];this.hStyle=this.hElement;}
else if(bw.ie){this.hElement=document.all[sId];this.hStyle=this.hElement.style;}}
getObject.getSize=function(sParam,hLayer)
{nPos=0;while((hLayer.tagName)&&!(/(body|html)/i.test(hLayer.tagName))){nPos+=eval('hLayer.'+sParam);if(sParam=='offsetTop'){if(hLayer.clientTop){nPos+=hLayer.clientTop;}}
if(sParam=='offsetLeft'){if(hLayer.clientLeft){nPos+=hLayer.clientLeft;}}
hLayer=hLayer.offsetParent;}
return nPos;}
function writeLayer(ID,parentID,sText)
{if(document.layers){var oLayer;if(parentID){oLayer=eval('document.'+parentID+'.document.'+ID+'.document');}
else{oLayer=document.layers[ID].document;}
oLayer.open();oLayer.write(sText);oLayer.close();}
else if(document.all){document.all[ID].innerHTML=sText;}
else{document.getElementById(ID).innerHTML=sText;}}