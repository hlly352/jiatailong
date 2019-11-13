// JavaScript Document
//数字判断
var ri_a = /^\d+$///非负整数(正整数+0)
var ri_b = /^[0-9]*[1-9][0-9]*$/ //正整数
var ri_c = /^((-\d+)|(0+))$/ //非正整数(负整数+0) 
var ri_d = /^-[0-9]*[1-9][0-9]*$/ //负整数
var ri_e = /^-?\d+$/ //整数
var rf_a = /^\d+(\.\d+)?$/ //非负浮点数(正浮点数+0)
var rf_b = /^(([0-9]+\.[0-9]*[1-9][0-9]*)|([0-9]*[1-9][0-9]*\.[0-9]+)|([0-9]*[1-9][0-9]*))$/ //正浮点数
var rf_c = /^((-\d+(\.\d+)?)|(0+(\.0+)?))$/ //非正浮点数(负浮点数+0)
var rf_d = /^(-(([0-9]+\.[0-9]*[1-9][0-9]*)|([0-9]*[1-9][0-9]*\.[0-9]+)|([0-9]*[1-9][0-9]*)))$/ //负浮点数
var rf_e = /^(-?\d+)(\.\d+)?$/ //浮点数
//邮箱地址判断
var email_reg = /^[A-Za-zd]+([-_.][A-Za-zd]+)*@([A-Za-zd]+[-.])+[A-Za-zd]{2,5}$/;
var zimu_reg= /^[A-Za-z]+$/;
$(function(){
	//菜单下拉
	$('#menu ul li').mouseover(function(){
		$(this).children('ul').show();
	});
	$('#menu ul li').mouseleave(function(){
		$(this).children('ul').hide();
	});
	//全选，反选，清除 
	$("#CheckedAll").click(function(){
		$('[name^=id]:checkbox').attr('checked',true);
		$('[id=submit]').attr('disabled',false);
	});
	$("#CheckedNo").click(function(){
		$('[name^=id]:checkbox').attr('checked',false);
		$('[id=submit]').attr('disabled',true);
	});
	$("#CheckedRev").click(function(){
		$('[name^=id]:checkbox').each(function(){
			this.checked=!this.checked;
		});
		flag=false;
		if(!$('[name^=id]:checkbox').filter(':checked').length){
			flag=true;
		}
		$('[id=submit]').attr('disabled',flag);
	});
	//checkbox id 选择
	$('[name^=id]:checkbox').live('click',function(){
		
		flag=false;
		if(!$('[name^=id]:checkbox').filter(':checked').length){
			flag=true;
		}
		$('[id=submit]').attr('disabled',flag);
	});
	//隔行换色
	$("#table_list tr:even,#table_sheet tr:even").addClass("even");
	//input txt 获取焦点
	$(".input_txt:input").focus(function(){
	$(this).addClass("focus");
	}).blur(function(){
		$(this).removeClass("focus");
	})
	//form_list 鼠标滑动高亮
	$("#table_list tr").mouseover(function(){					
		$(this).addClass('highlight').siblings().removeClass('highlight');
	})
	$("#add_file").click(function(){
		$(this).after("<br /><input type=\"file\" name=\"file[]\" class=\"input_files\"");
	})	
})
//复制地址
function copyToClipboard(txt) {    
     if(window.clipboardData) {    
        window.clipboardData.clearData();    
        window.clipboardData.setData("Text", txt);    
        alert("Your request has been processed successfully.");    
      } else if(navigator.userAgent.indexOf("Opera") != -1) {    
       window.location = txt;    
      } else if (window.netscape) {    
      try {    
        netscape.security.PrivilegeManager.enablePrivilege("UniversalXPConnect");    
       } catch (e) {    
        alert("被浏览器拒绝！\n请在浏览器地址栏输入'about:config'并回车\n然后将'signed.applets.codebase_principal_support'设置为'true'");    
       }    
      var clip = Components.classes['@mozilla.org/widget/clipboard;1'].createInstance(Components.interfaces.nsIClipboard);    
      if (!clip)    
       return;    
      var trans = Components.classes['@mozilla.org/widget/transferable;1'].createInstance(Components.interfaces.nsITransferable);    
      if (!trans)    
       return;    
       trans.addDataFlavor('text/unicode');    
      var str = new Object();    
      var len = new Object();    
      var str = Components.classes["@mozilla.org/supports-string;1"].createInstance(Components.interfaces.nsISupportsString);    
      var copytext = txt;    
       str.data = copytext;    
       trans.setTransferData("text/unicode",str,copytext.length*2);    
      var clipid = Components.interfaces.nsIClipboard;    
      if (!clip)    
       return false;    
       clip.setData(trans,null,clipid.kGlobalClipboard);    
       alert("Your request has been processed successfully.");    
      }    
}
//比较日期/时间
function GetDateDiff(startTime, endTime, diffType) {
	 //将xxxx-xx-xx的时间格式，转换为 xxxx/xx/xx的格式 
	 startTime = startTime.replace(/\-/g, "/");
	 endTime = endTime.replace(/\-/g, "/");
	 
	 //将计算间隔类性字符转换为小写
	 diffType = diffType.toLowerCase();
	 var sTime = new Date(startTime);      //开始时间
	 var eTime = new Date(endTime);  //结束时间
	 //作为除数的数字
	 var divNum = 1;
	 switch (diffType) {
		 case "second":
		 divNum = 1000;
		 break;
		 case "minute":
		 divNum = 1000 * 60;
		 break;
		 case "hour":
		 divNum = 1000 * 3600;
		 break;
		 case "day":
		 divNum = 1000 * 3600 * 24;
		 break;
		 case "year":
		 divNum = 1000 * 3600 * 24 *365;
		 break;
		 default:
		 break;
	}
	return parseInt((eTime.getTime() - sTime.getTime()) / parseInt(divNum));
}
 //判断是否在数组中
  function is_arr(str,arr){
    var len = arr.length-1;
    while(len>=0){
      if(str === arr[len]){
      return true;
    }
      len--;

    }
    return false;
  }
  function get_employee(){
     var deptid = $('#dept').val();
    //获取当前部门的人员
    $.post('../ajax_function/get_dept_employee.php',{deptid:deptid},function(data){
      var select_num = $('.select_employee').size();
      var array_select = new Array();
      for(var j=0;j<select_num;j++){
        var employeeid = $('.select_employee').eq(j).attr('employeeid');
        array_select.push(employeeid);
      }
      $('#employee').empty();
      for(var i=0;i<data.length;i++){
        if(!is_arr(data[i].employeeid,array_select)){
          var span = '<span class="employee" id="employee_'+data[i].employeeid+'" style="padding:5px;cursor:pointer;color:blue">'+data[i].employee_name+'<span>';
          $('#employee').append(span);
       }
      }
    },'json')
  }