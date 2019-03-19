// JavaScript Document
function checkIdcard(idcard) {
	var Errors = new Array( "*验证通过!", "*身份证号码位数不对!", "*身份证号码出生日期超出范围或含有非法字符!", "*身份证号码校验错误!");
	var idcard, Y, JYM;
	var S, M;
	var idcard_array = new Array();
	idcard_array = idcard.split("");  
   //身份号码位数及格式检验
	switch (idcard.length) {
		case 15:
			if ((parseInt(idcard.substr(6, 2)) + 1900) % 4 == 0 || ((parseInt(idcard.substr(6, 2)) + 1900) % 100 == 0 && (parseInt(idcard.substr(6, 2)) + 1900) % 4 == 0)) {
				ereg = /^[1-9][0-9]{5}[0-9]{2}((01|03|05|07|08|10|12)(0[1-9]|[1-2][0-9]|3[0-1])|(04|06|09|11)(0[1-9]|[1-2][0-9]|30)|02(0[1-9]|[1-2][0-9]))[0-9]{3}$/;
				} else {
				ereg = /^[1-9][0-9]{5}[0-9]{2}((01|03|05|07|08|10|12)(0[1-9]|[1-2][0-9]|3[0-1])|(04|06|09|11)(0[1-9]|[1-2][0-9]|30)|02(0[1-9]|1[0-9]|2[0-8]))[0-9]{3}$/;
				}
			if (ereg.test(idcard)) { //success
				document.getElementById("birthday").value = "19" + idcard.substr(6, 2) + "-" + idcard.substr(8, 2) + "-" + idcard.substr(10, 2)
				//计算年龄
				var nowdate = new Date();
				var brithday = document.getElementById("birthday").value;
				document.getElementById("age").value = nowdate.getFullYear() - brithday.substr(0,4);
				
					if (parseInt(idcard.substr(14, 1)) % 2 == 0) {
						//document.getElementById("txtsex").value = "女";
						document.getElementById("sex").value = "女";
						} else {
						//document.getElementById("txtsex").value = "男";
						document.getElementById("sex").value = "男";
					}
						return "";
					} else
					return Errors[2];
					//document.getElementById("txtsex").value = "";
					document.getElementById("sex").value = "";
					break;
				
		case 18: //18位身份号码检测 //出生日期的合法性检查
		//闰年月日:((01|03|05|07|08|10|12)(0[1-9]|[1-2][0-9]|3[0-1])|(04|06|09|11)(0[1-9]|[1-2][0-9]|30)|02(0[1-9]|[1-2][0-9]))
		//平年月日:((01|03|05|07|08|10|12)(0[1-9]|[1-2][0-9]|3[0-1])|(04|06|09|11)(0[1-9]|[1-2][0-9]|30)|02(0[1-9]|1[0-9]|2[0-8]))
		if (parseInt(idcard.substr(6, 4)) % 4 == 0 || (parseInt(idcard.substr(6,4)) % 100 == 0 && parseInt(idcard.substr(6, 4)) % 4 == 0)) {
			ereg = /^[1-9][0-9]{5}19[0-9]{2}((01|03|05|07|08|10|12)(0[1-9]|[1-2][0-9]|3[0-1])|(04|06|09|11)(0[1-9]|[1-2][0-9]|30)|02(0[1-9]|[1-2][0-9]))[0-9]{3}[0-9Xx]$/;
			} else {
			ereg = /^[1-9][0-9]{5}19[0-9]{2}((01|03|05|07|08|10|12)(0[1-9]|[1-2][0-9]|3[0-1])|(04|06|09|11)(0[1-9]|[1-2][0-9]|30)|02(0[1-9]|1[0-9]|2[0-8]))[0-9]{3}[0-9Xx]$/;
			}
			
		if (ereg.test(idcard)) { //测试出生日期的合法性 //计算校验位
			S = (parseInt(idcard_array[0]) + parseInt(idcard_array[10])) * 7 + (parseInt(idcard_array[1]) + parseInt(idcard_array[11])) * 9 + (parseInt(idcard_array[2]) + parseInt(idcard_array[12])) * 10 + (parseInt(idcard_array[3]) + parseInt(idcard_array[13])) * 5 + (parseInt(idcard_array[4]) + parseInt(idcard_array[14])) * 8 + (parseInt(idcard_array[5]) + parseInt(idcard_array[15])) * 4 + (parseInt(idcard_array[6]) + parseInt(idcard_array[16])) * 2 + parseInt(idcard_array[7]) * 1 + parseInt(idcard_array[8]) * 6 + parseInt(idcard_array[9]) * 3;
			Y = S % 11;
			M = "F";
			JYM = "10X98765432";
			M = JYM.substr(Y, 1); //判断校验位
			if (M == idcard_array[17]) { //success
				document.getElementById("birthday").value = idcard.substr(6, 4) + "-" + idcard.substr(10, 2) + "-" + idcard.substr(12, 2)
				//计算年龄
				var nowdate = new Date();
				var brithday = document.getElementById("birthday").value;
				document.getElementById("age").value = nowdate.getFullYear() - brithday.substr(0,4);
				
					if (parseInt(idcard.substr(16, 1)) % 2 == 0) {
						//document.getElementById("txtsex").value = "女";
						document.getElementById("sex").value = "女";
					} else {
						//document.getElementById("txtsex").value = "男";
						document.getElementById("sex").value = "男";
					}	
						return "";
					}
				else return Errors[3];
			} else return Errors[2];
			break;
			
			default:
			return Errors[1];
			break;
		}
}