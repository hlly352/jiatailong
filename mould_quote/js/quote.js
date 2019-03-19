// JavaScript Document
$(function(){
	$("input[id^=A]").blur(function(){
		var field_value = $(this).val();
		var field_default_value = this.defaultValue;
		var array_id = $(this).attr('id').split('-');
		var item_type_sn = array_id[0];
		var item_sn = array_id[1];
		var field_name = array_id[2];
		var quote_listid = array_id[3];
		if((field_name == 'number' && !ri_a.test(field_value)) || ((field_name != 'number' && field_name != 'specification') && !rf_a.test(field_value))){
			alert('输入数字');
			$(this).val(field_default_value);
			return false;
		}
		$.post("mould_quote_update.php",{
			   quote_listid:quote_listid,
			   field_name:field_name,
			   field_value:field_value,
			   item_type_sn:item_type_sn
		},function(data,textStatus){
			if(data != ''){
				var array_data = data.split('#');
				$("#"+item_type_sn+"-"+item_sn+"-length-"+quote_listid).val(array_data[0]);
				$("#"+item_type_sn+"-"+item_sn+"-width-"+quote_listid).val(array_data[1]);
				$("#"+item_type_sn+"-"+item_sn+"-height-"+quote_listid).val(array_data[2]);
				$("#"+item_type_sn+"-"+item_sn+"-weight-"+quote_listid).html(array_data[3]);
				$("#"+item_type_sn+"-"+item_sn+"-unit_price-"+quote_listid).val(array_data[4]);
				$("#"+item_type_sn+"-"+item_sn+"-total_price-"+quote_listid).html(array_data[5]);
				$("#"+item_type_sn+"-sum_price").html(array_data[6]);
				$("input[name='B-B-weight']").val(array_data[7]);
				$("input[name='D-hour']").val(array_data[8]);
				$("input[name='E-A-hour'],input[name='E-B-hour'],input[name='E-C-hour'],input[name='E-D-hour'],input[name='E-H-hour']").val(array_data[9]);
				$("input[name='E-E-hour'],input[name='E-G-hour']").val(Math.round(array_data[9]*0.8));
				$("input[name='E-F-hour']").val(Math.round(array_data[9]*1.5));
				$("input[name='B-B-weight']").blur();
				$("input[name='D-hour']").blur();
				$("input[name^='E']").blur();
			}
		})
	})
	$("input[id^=B]").blur(function(){
		var field_value = $(this).val();
		var field_default_value = this.defaultValue;
		var array_id = $(this).attr('id').split('-');
		var item_type_sn = array_id[0];
		var item_sn = array_id[1];
		var field_name = array_id[2];
		var quote_listid = array_id[3];
		if(!rf_a.test(field_value)){
			alert('输入数字');
			$(this).val(field_default_value);
			return false;
		}
		$.post("mould_quote_update.php",{
			   quote_listid:quote_listid,
			   field_name:field_name,
			   field_value:field_value,
			   item_type_sn:item_type_sn
		},function(data,textStatus){
			if(data != ''){
				var array_data = data.split('#');
				$("#"+item_type_sn+"-"+item_sn+"-weight-"+quote_listid).val(array_data[0]);
				$("#"+item_type_sn+"-"+item_sn+"-unit_price-"+quote_listid).val(array_data[1]);
				$("#"+item_type_sn+"-"+item_sn+"-total_price-"+quote_listid).html(array_data[2]);
				$("#"+item_type_sn+"-sum_price").html(array_data[3]);
				$("input[name='E-A-hour'],input[name='E-B-hour'],input[name='E-C-hour'],input[name='E-D-hour'],input[name='E-H-hour']").val(array_data[4]);
				$("input[name='E-E-hour'],input[name='E-G-hour']").val(Math.round(array_data[4]*0.8));
				$("input[name='E-F-hour']").val(Math.round(array_data[4]*1.5));
				$("input[name^='E']").blur();
			}
		})
	})
	$("input[id^=C]").blur(function(){
		var field_value = $(this).val();
		var field_default_value = this.defaultValue;
		var array_id = $(this).attr('id').split('-');
		var item_type_sn = array_id[0];
		var item_sn = array_id[1];
		var field_name = array_id[2];
		var quote_listid = array_id[3];
		if((field_name == 'number' && !ri_a.test(field_value)) || (field_name == 'unit_price' && !rf_a.test(field_value))){
			alert('输入数字');
			$(this).val(field_default_value);
			return false;
		}
		$.post("mould_quote_update.php",{
			   quote_listid:quote_listid,
			   field_name:field_name,
			   field_value:field_value,
			   item_type_sn:item_type_sn
		},function(data,textStatus){
			if(data != ''){
				var array_data = data.split('#');
				$("#"+item_type_sn+"-"+item_sn+"-unit_price-"+quote_listid).val(array_data[0]);
				$("#"+item_type_sn+"-"+item_sn+"-total_price-"+quote_listid).html(array_data[1]);
				$("#"+item_type_sn+"-sum_price").html(array_data[2]);
				$("span[name=F-C-total_price]").html(array_data[3]);
				$("span[name=F-D-total_price]").html(array_data[4]);
				$("span[name=F-E-total_price]").html(array_data[5]);
				$("#F-sum_price").html(array_data[6]);
				$("#total_sum_price").html(array_data[7]);
				$("#total_sum_price_usd").html(array_data[8]);
				$("#total_sum_price_vat").html(array_data[9]);
			}
		})
	})
	$("input[id^=D],input[id^=E]").blur(function(){
		var field_value = $(this).val();
		var field_default_value = this.defaultValue;
		var array_id = $(this).attr('id').split('-');
		var item_type_sn = array_id[0];
		var item_sn = array_id[1];
		var field_name = array_id[2];
		var quote_listid = array_id[3];
		if((field_name == 'hour' && !ri_a.test(field_value)) || (field_name == 'unit_price' && !rf_a.test(field_value))){
			alert('输入数字');
			$(this).val(field_default_value);
			return false;
		}
		$.post("mould_quote_update.php",{
			   quote_listid:quote_listid,
			   field_name:field_name,
			   field_value:field_value,
			   item_type_sn:item_type_sn
		},function(data,textStatus){
			if(data != ''){
				var array_data = data.split('#');
				$("#"+item_type_sn+"-"+item_sn+"-unit_price-"+quote_listid).val(array_data[0]);
				$("#"+item_type_sn+"-"+item_sn+"-total_price-"+quote_listid).html(array_data[1]);
				$("#"+item_type_sn+"-sum_price").html(array_data[2]);
				$("span[name=F-C-total_price]").html(array_data[3]);
				$("span[name=F-D-total_price]").html(array_data[4]);
				$("span[name=F-E-total_price]").html(array_data[5]);
				$("#F-sum_price").html(array_data[6]);
				$("#total_sum_price").html(array_data[7]);
				$("#total_sum_price_usd").html(array_data[8]);
				$("#total_sum_price_vat").html(array_data[9]);
			}
		})
	})

	$("input[id^=F]").blur(function(){
		var field_value = $(this).val().replace(',',''); 
		var field_default_value = this.defaultValue;
		var array_id = $(this).attr('id').split('-');
		var item_type_sn = array_id[0];
		var item_sn = array_id[1];
		var field_name = array_id[2];
		var quote_listid = array_id[3];
		if(field_name == 'total_price' && !rf_a.test(field_value)){
			alert('输入数字');
			$(this).val(field_default_value);
			return false;
		}
		var reg = /^((\d+\.?\d*)|(\d*\.\d+))\%$/;
		var item_key = item_type_sn+'-'+item_sn+'-'+field_name;
		var array_f_item_sn = ['F-C-descripition','F-D-descripition','F-E-descripition'];
		if($.inArray(item_key,array_f_item_sn) != -1 && !reg.test(field_value)){
			alert('请输入百分数');
			$(this).val(field_default_value);
			return false;
		}
		$.post("mould_quote_update.php",{
			   quote_listid:quote_listid,
			   field_name:field_name,
			   field_value:field_value,
			   item_type_sn:item_type_sn
		},function(data,textStatus){
			if(data != ''){
				var array_data = data.split('#');
				$("#"+item_type_sn+"-"+item_sn+"-total_price-"+quote_listid).val(array_data[0]);
				$("span[name=F-C-total_price]").html(array_data[1]);
				$("span[name=F-D-total_price]").html(array_data[2]);
				$("span[name=F-E-total_price]").html(array_data[3]);
				$("#F-sum_price").html(array_data[4]);
				$("#total_sum_price").html(array_data[5]);
				$("#total_sum_price_usd").html(array_data[6]);
				$("#total_sum_price_vat").html(array_data[7]);
			}
		})
	})
})