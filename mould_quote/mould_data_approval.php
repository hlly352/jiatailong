<?php
require_once '../global_mysql_connect.php';
require_once '../function/function.php';
require_once '../class/page.php';
require_once 'shell.php';
$sdate = $_GET['sdate']?$_GET['sdate']:date('Y-m-01');
$edate = $_GET['edate']?$_GET['edate']:date('Y-m-d',strtotime($sdate."+1 month -1 day"));
if($_GET['submit']){
	$mould_name = trim($_GET['mould_name']);
	$client_name = trim($_GET['client_name']);
	$project_name = trim($_GET['project_name']);
	$sqlwhere = "  AND `client_name` LIKE '%$client_name%' AND `mould_name` LIKE '%$mould_name%' AND `project_name` LIKE '%$project_name%'";
}
$sql = "SELECT * FROM `db_mould_data` 
WHERE time in (
SELECT max(a.time)
FROM db_mould_data a
GROUP BY mold_id)".$sqlwhere."AND `is_approval` = '1'";

$result = $db->query($sql);
$pages = new page($result->num_rows,15);
$sqllist = $sql . " ORDER BY `mould_dataid` DESC" . $pages->limitsql;
$result = $db->query($sqllist);
$result_id = $db->query($sqllist);
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<link href="../css/system_base.css" type="text/css" rel="stylesheet" />
<link href="css/main.css" type="text/css" rel="stylesheet" />
<link rel="shortcut icon" href="../images/logo/xel.ico" />
<script language="javascript" type="text/javascript" src="../js/jquery-1.6.4.min.js"></script>
<script language="javascript" type="text/javascript" src="../js/My97DatePicker/WdatePicker.js" ></script>
<script language="javascript" type="text/javascript" src="../js/main.js"></script>
<script type="text/javascript">
function getdate(timestamp) {
        var date = new Date(timestamp * 1000);//时间戳为10位需*1000，时间戳为13位的话不需乘1000
        var Y = date.getFullYear() + '-';
        var M = (date.getMonth()+1 < 10 ? '0'+(date.getMonth()+1) : date.getMonth()+1) + '-';
        var D = date.getDate() + ' ';
        var h = date.getHours() + ':';
        var m = date.getMinutes() + ':';
        var s = date.getSeconds();
        return Y+M+D;
    }
	$(function(){
		var mold_num = $('.mold_num').size();
		for(var i=0;i<mold_num;i++){
			var  num = $('.mold_num').eq(i).text();
			if(num>1){
				$('.but').eq(i).css('background','green');
				$('.but').eq(i).css('cursor','pointer');

			}
		}
		$('.but').bind('click',function(){
			 mold_nu = $('.but').index(this);

			var mold_id = $('.mold_id_val').eq(mold_nu).val();
			$.ajax({
				'url':'../ajax_function/mould_id_data.php',
				'type':'post',
				'dataType':'json',
				'async':true,
				'data':{mold_id:mold_id},
				'success':function(data){
					for(var i=1;i<data.length;i++){
							if($(".mold_num").eq(mold_nu).text() >1){
								var getdat = getdate(data[i].time);
				var tr = '   <tr class="block'+data[i].mold_id+'">        <td><input type="checkbox" name="id[]" value="'+data[i].mould_dataid+'" /></td>        <td> '+getdat+' </td>        <td>'+data[i].client_name+'</td>        <td>'+data[i].project_name+'</td>        <td>'+data[i].mould_name+'</td>        <td>'+data[i].part_number+'</td>        <!--<td><a href="mould_photo.php?id=<?php echo $mould_dataid; ?>"><?php echo $image_file; ?></a></td>-->        <td><?php echo $image_file ?></td>         <td>'+data[i].p_length+'*'+data[i].p_width+'*'+data[i].p_height+'</td>        <td>'+data[i].m_material+'</td>        <td><?php echo $cavity_nu; ?></td>        <td>'+data[i].m_length+'*'+data[i].m_width+'*'+data[i].m_height+'</td>        <td>'+data[i].m_weight+'</td>                <td><?php echo $arrs_materials[1][1].'/'.$arrs_materials[2][1] ?></td>        <td>        	<?php 
        		if($arrs_standards[4][1] !=0&&$arrs_standards[4][1] != null){
        			echo $arrs_standards[4][2].'/'.$arrs_standards[4][1];
        		} else {
        			echo '无';
        		}
        	?>        </td>        <td>'+data[i].tonnage+'</td>        <td>&yen;'+data[i].mold_price_rmb+'</td>        <td>&yen;'+data[i].mold_with_vat+'</td>        <td></td>      <!-- <td><a href="mould_quote_list.php?id=<?php echo $mould_dataid; ?>"><img src="../images/system_ico/quote_11_12.png" width="11" height="12" /></a></td> -->        <td><!--<?php if($count == 0){ ?><a href="mould_dataae.php?id=<?php echo $mould_dataid; ?>&action=edit"><img src="../images/system_ico/edit_10_10.png" width="10" height="10" /></a><?php } ?>--></td>      </tr> ';			
        	 $('.but').eq(mold_nu).parent().parent().after(tr);
        	 $('.but').eq(mold_nu).unbind('click').val('收起').css('background','#ddd').addClass('del');
  
        	 			}
			 }			
		},
				'error':function(){
					alert('获取数据失败');
			 	}
			});
			 
					
        	 	}) 
		//收起子列表
		$(".del").live('click',function(){
			if($(this).val() == '收起'){

				var id_nu = $('.but').index(this);
				var mold_id = $('.mold_ids').eq(id_nu).val();
				$(".block"+mold_id).remove();
				$(this).val('查看').css('background','green').removeClass('del').bind('click',function(){
			 mold_nu = $('.but').index(this);
			var mold_id = $('.mold_id_val').eq(mold_nu).val();
			$.ajax({
				'url':'../ajax_function/mould_id_data.php',
				'type':'post',
				'dataType':'json',
				'async':true,
				'data':{mold_id:mold_id},
				'success':function(data){
					for(var i=1;i<data.length;i++){
						console.log(data[i].time);
						//console.log(mold_nu);
							if($(".mold_num").eq(mold_nu).text() >1){
								var getdat = getdate(data[i].time);
				var tr = '   <tr class="block'+data[i].mold_id+'">         <td><input type="checkbox" name="id[]" value="'+data[i].mould_dataid+'" /></td>        <td> '+getdat+' </td>        <td>'+data[i].client_name+'</td>        <td>'+data[i].project_name+'</td>        <td>'+data[i].mould_name+'</td>        <td>'+data[i].part_number+'</td>        <!--<td><a href="mould_photo.php?id=<?php echo $mould_dataid; ?>"><?php echo $image_file; ?></a></td>-->        <td><?php echo $image_file ?></td>         <td>'+data[i].p_length+'*'+data[i].p_width+'*'+data[i].p_height+'</td>        <td>'+data[i].m_material+'</td>        <td><?php echo $cavity_nu; ?></td>        <td>'+data[i].m_length+'*'+data[i].m_width+'*'+data[i].m_height+'</td>        <td>'+data[i].m_weight+'</td>                <td><?php echo $arrs_materials[1][1].'/'.$arrs_materials[2][1] ?></td>        <td>        	<?php 
        		if($arrs_standards[4][1] !=0&&$arrs_standards[4][1] != null){
        			echo $arrs_standards[4][2].'/'.$arrs_standards[4][1];
        		} else {
        			echo '无';
        		}
        	?>        </td>        <td>'+data[i].tonnage+'</td>        <td>&yen;'+data[i].mold_price_rmb+'</td>        <td>&yen;'+data[i].mold_with_vat+'</td>        <td></td>      <!-- <td><a href="mould_quote_list.php?id=<?php echo $mould_dataid; ?>"><img src="../images/system_ico/quote_11_12.png" width="11" height="12" /></a></td> -->        <td><!--<?php if($count == 0){ ?><a href="mould_dataae.php?id=<?php echo $mould_dataid; ?>&action=edit"><img src="../images/system_ico/edit_10_10.png" width="10" height="10" /></a><?php } ?>--></td>      </tr> ';			
        	 $('.but').eq(mold_nu).parent().parent().after(tr);
        	 $('.but').eq(mold_nu).unbind('click').val('收起').css('background','#ddd').addClass('del');
  
        	 			}
			 }			
		},
				'error':function(){
					alert('获取数据失败');
			 	}
			});
			 
					
        	 	}) ;
			}
		})
        	 })

      </script>
<title>模具报价-希尔林</title>
</head>

<body>
<?php include "header.php"; ?>
<div id="table_search">
  <h4 style="padding-left:10px">
  	 
  </h4>
  <form action="" name="search" method="get">
    <table>
      <tr>
      	
          
       </tr>
       <tr>
       <td>客户名称</td>
       <td><input type="text" name="client_name" class="input_txt" /></td>
       <td></td>
       <td>项目名称</td>
       <td><input type="text" name="project_name" class="input_txt"></td>
       <td></td>
        <td>模具名称</td>
        <td><input type="text" name="mould_name" class="input_txt" /></td>
        <td>报价日期</td>
        <td><input type="text" name="sdate" value="<?php echo $sdate; ?>" onfocus="WdatePicker({dateFmt:'yyyy-MM-dd',isShowClear:false,readOnly:true})" class="input_txt" />
          --
          <input type="text" name="edate" value="<?php echo $edate; ?>" onfocus="WdatePicker({dateFmt:'yyyy-MM-dd',isShowClear:false,readOnly:true})" class="input_txt" /></td>
        <td><input type="submit" name="submit" value="查找" class="button" />
      </tr>
    </table>
  </form>
</div>
<div id="table_list">
  <?php
  if($result->num_rows){
	  while($row_id = $result_id->fetch_assoc()){

		  $array_mould_dataid .= $row_id['mould_dataid'].',';
	  }
	  $array_mould_dataid = rtrim($array_mould_dataid,',');
	  $sql_group = "SELECT `mould_dataid`,COUNT(*) AS `count` FROM `db_mould_quote` WHERE `mould_dataid` IN ($array_mould_dataid) AND `quote_status` = 1 GROUP BY `mould_dataid`";
	  $result_group = $db->query($sql_group);
	  if($result_group->num_rows){
		  while($row_group = $result_group->fetch_assoc()){
			  $array_group[$row_group['mould_dataid']] = $row_group['count'];
		  }
	  }else{
		  $array_group = array();
	  }
	
  ?>
  <form action="mould_datado.php" name="list" method="post">
    <table>
      <tr>
        <th width="">ID</th>
        <th width="">报价时间</th>
        <th width="">客户名称</th>
        <th width="">项目名称</th>
        <th width="">模具名称</th>
        <th width="">零件编号</th>
        <th width="">零件图片</th>
        <th width="">产品尺寸</th>
        <th width="">塑胶材料</th>
        <th width="">模穴数</th>
        <th width="">模具尺寸</th>
        <th width="">模具重量</th>
        <th width="">型腔/型芯材质</th>
        <th width="">热流道类型</th>
        <th width="">吨位</th>
        <th width="">未税价格</th>
        <th width="">含税价格</th>
        <th width="">历史价格</th>
        <th width="">修改</th>
      <?php
      while($row = $result->fetch_assoc()){
		  $mould_dataid = $row['mould_dataid'];
		  $image_filedir = $row['image_filedir'];
		  $image_filepath = $row['upload_final_path'];
		  if(stristr($image_filepath,'$') == true){
		  	$image_filepath = substr($image_filepath,0,stripos($image_filepath,"$"));
			}
		  //echo $image_filename.'<br>';
		  //$image_filepath = "../upload/mould_image/".$image_filedir.'/'.$image_filename;
		  if(is_file($image_filepath)){
			  $image_file = "<img width=\"85\" height=\"45\" src=\"".$image_filepath."\" />";
		  }else{
			  $image_file = "<img src=\"../images/no_image_85_45.png\" width=\"85\" height=\"45\" />";
		  }
		  $count = array_key_exists($mould_dataid,$array_group)?$array_group[$mould_dataid]:0;
		
		  //获取零件编号
		  if(substr_count($row['part_number'],'$$') !=0){
		  	$part_number = explode('$$',$row['part_number']);
		  	$p_length = explode('$$',$row['p_length']);
		  	$p_width = explode('$$',$row['p_width']);
		  	$p_height = explode('$$',$row['p_height']);
		  	$m_material = explode('$$',$row['m_material']);
		  	$arr1= arr_merge($p_length,$p_width);
			$arr_res = arr_merge($arr1,$p_height);
			//多个模穴数时拼接尺寸
			$re = ' ';
			for($i=0;$i<count($arr_res);$i++){
				$re .= $arr_res[$i][0].'*'.$arr_res[$i][1].'*'.$arr_res[$i][2].'<br>';
				
			}
			
			} else {
			    $part_number = $row['part_number'];	
			    $m_material = $row['m_material'];
			    $p_length = $row['p_length'];
			    $p_width = $row['p_width'];
			    $p_height = $row['p_height'];
			    $re = $p_length.'*'.$p_width.'*'.$p_height;
			}
			
		  //获取模穴数
		  $cavity_num = turn_arr($row['cavity_type']);
	
		  if(count($cavity_num)  == 1){
		  	$cavity_nu = '1*'.$cavity_num[0];
		  } else {
		  	$cavity_nu = $cavity_num[0];
		  	for($i = 1;$i<count($cavity_num);$i++){
		  		
		  			
		  			$cavity_nu .= '+'.$cavity_num[$i];
		  		
		  	}
		  }
		  //获取加工材料费的数据
		  $old_material = [$row['mould_material'],$row['material_specification'],$row['materials_number'],$row['material_length'],$row['material_width'],$row['material_height'],$row['material_weight'],$row['material_unit_price'],$row['material_price']];
		 $arrs_materials = getdata($old_material);
	
		  //获取模具配件的数据
		   $old_standard = [$row['mold_standard'],$row['standard_specification'],$row['standard_supplier'],$row['standard_number'],$row['standard_unit_price'],$row['standard_price']];
		 $arrs_standards = getdata($old_standard);

		//查询报价的历史个数
		 $sqls = "SELECT count(mould_dataid) FROM `db_mould_data` WHERE `mold_id` =".$row['mold_id'];
		 $res = $db->query($sqls);
	
		 $r = $res->fetch_row();
		 $row['num'] = $r[0];
	  ?>
     <tr class="show">
       <td><input type="checkbox" name="id[]" value="<?php echo $mould_dataid; ?>"<?php if($count > 0) echo " disabled=\"disabled\""; ?> /></td>
        <td><?php echo date('Y-m-d',$row['time']) ?></td>
        <td><?php echo $row['client_name']; ?><input type="hidden" class="mold_ids" value="<?php echo $row['mold_id'] ?>"></td>
        <td><?php echo $row['project_name']; ?></td>
        <td><?php echo $row['mould_name']; ?></td>
        <td><?php getin($part_number); ?></td>
        <!--<td><a href="mould_photo.php?id=<?php echo $mould_dataid; ?>"><?php echo $image_file; ?></a></td>-->
        <td><?php echo $image_file ?></td>
         <td><?php echo $re ?></td>

        <td><?php getin($m_material); ?></td>
        <td><?php echo $cavity_nu; ?></td>
        <td><?php echo $row['m_length'].'*'.$row['m_width'].'*'.$row['m_height']; ?></td>
        <td><?php echo $row['m_weight']; ?></td>
        
        <td><?php echo $arrs_materials[1][1].'/'.$arrs_materials[2][1] ?></td>
        <td>
        	<?php 
        		if($arrs_standards[4][1] !=0&&$arrs_standards[4][1] != null){
        			echo $arrs_standards[4][2].'/'.$arrs_standards[4][1];
        		} else {
        			echo '无';
        		}
        	?>
        </td>
        <td><?php echo $row['tonnage']; ?></td>
        <td>&yen;<?php echo $row['mold_price_rmb']; ?></td>
        <td>&yen;<?php echo $row['mold_with_vat'] ?></td>
        <td><input type="button" class="but" value="查看"></button>&nbsp;<span class="mold_num"><?php echo $row['num'];        		
        	 ?><input type="hidden" class="mold_id_val" value="<?php echo $row['mold_id'] ?>"></span></td>
      <!-- <td><a href="mould_quote_list.php?id=<?php echo $mould_dataid; ?>"><img src="../images/system_ico/quote_11_12.png" width="11" height="12" /></a></td> -->
        <td><?php if($count == 0){ ?><a href="mould_dataae.php?id=<?php echo $mould_dataid; ?>&action=approval_edit"><input type="button" value="修改"></a><?php } ?></td>
      </tr> 
      <?php } ?>
    </table>
    <div id="checkall">
      <input name="all" type="button" class="select_button" id="CheckedAll" value="全选" />
      <input type="button" name="other" class="select_button" id="CheckedRev" value="反选" />
      <input type="button" name="reset" class="select_button" id="CheckedNo" value="清除" />
      <input type="submit" name="submit" id="submit" value="删除" class="select_button" onclick="JavaScript:return confirm('系统提示:确定删除吗?')" disabled="disabled" />
      <input type="hidden" name="action" value="del" />
    </div>
  </form>
  <div id="page">
    <?php $pages->getPage();?>
  </div>
  <?php
  }else{
	  echo "<p class=\"tag\">系统提示：暂无记录！</p>";
  }
  ?>
</div>
<?php include "../footer.php"; ?>
</body>
</html>