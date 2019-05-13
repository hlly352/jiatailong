<?php
require_once '../global_mysql_connect.php';
require_once '../function/function.php';
require_once '../config/config.php';
require_once 'shell.php';
$action = fun_check_action($_GET['action']);

$employeeid = $_SESSION['employee_info']['employeeid'];
//通过员工id查询姓名和部门
$sql = "SELECT a.employee_name,b.`dept_name` FROM `db_employee` as a INNER JOIN `db_department` as b ON a.deptid = b.deptid WHERE a.employeeid = ".$employeeid ;
$res = $db->query($sql);
$dept = [];
while($rows = $res->fetch_assoc()){
  $dept = $rows;
}
//查找市场部和总经办的人员
$min_boss_sql = "SELECT `employee_name`,`employeeid`,`deptid` FROM `db_employee` WHERE `deptid` =1 AND `employee_status` = '1' OR `deptid` = 2 AND `employee_status` = '1'";

$min_boss = $db->query($min_boss_sql);
$employees = [];
if($min_boss->num_rows){
  while($row = $min_boss->fetch_assoc()){
    $employees[] = $row;
  }
}

//查找总经办的人员
$boss_sql = "SELECT `employee_name`,`employeeid`,`deptid` FROM `db_employee` WHERE `deptid` =1 AND `employee_status` = '1'";

$boss = $db->query($boss_sql);
$employeess = [];
if($boss->num_rows){
  while($row = $boss->fetch_assoc()){
    $employeess[] = $row;
  }
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<link href="../css/system_base.css" type="text/css" rel="stylesheet" />

<link href="css/main.css" type="text/css" rel="stylesheet" />
<link rel="shortcut icon" href="../images/logo/xel.ico" />
<script language="javascript" type="text/javascript" src="../js/jquery-1.6.4.min.js"></script>
<script language="javascript" type="text/javascript" src="../js/main.js"></script>
<script langeage="javascript" type="text/javascript" src="../js/add_customer.js"></script>
<script language="javascript" type="text/javascript">
  //获取当前时间
  function getNowDate() {
    var date = new Date();
    var seperator1 = "-";
    var seperator2 = ":";
    var month = date.getMonth() + 1<10? "0"+(date.getMonth() + 1):date.getMonth() + 1;
    var strDate = date.getDate()<10? "0" + date.getDate():date.getDate();
    var currentdate = date.getFullYear() + seperator1  + month  + seperator1  + strDate;
    return currentdate;
  }
   nowDate = getNowDate();

  $(function(){
    $('tr').removeClass('even');
    
  var add_contacts= ' <tr>           <th>姓名：</th>           <td width="">               <input teyp="text" name="contacts_name[]" />              <span  class="del del_contacts">删除</span>      </td>        </tr>     <tr>         <th>所属公司：</th>          <td>             <input type="text" name="contacts_company[]" />          </td>        </tr>     <tr>         <th>职务：</th>          <td>             <input type="text" name="contacts_work[]" />           </td>      </tr>           <tr>      <th>电话/手机：</th>     <td>           <input type="text" name="contacts_phone[]" />      </td>     </tr>     <tr>         <th>邮箱：</th>           <td>              <input type="text" name="contacts_email[]" />           </td>       </tr>     <tr>          <th>备注：</th>        <td>            <input type="text" name="contacts_note[]" />        </td>     </tr>';
  var add_company = '<tr>         <th width="">代码：</th>         <td style="width:70px">           <input type="text" name="customer_code[]" style="width:70px" readonly/>         </td>         <td style="width:40px">等级：</td>         <td>            <select name="customer_grade[]" style="height:30px;width:70px" class="customer_grades">             <?php foreach($array_customer_grade as $k=>$v){ 
                echo '<option value="'.$v.'">'.$v.'</option>';
                }
                ?>            </select> <span  class="del del_company">删除</span>                </td>   </tr>   <tr>          <th width="">名称：</th>         <td width="" colspan="3">           <input type="text" name="customer_name[]" />          </td>   </tr>   <tr>          <th>主营业务：</th>          <td colspan="3">            <input type="text" name="customer_business[]" />          </td>   </tr>   <tr>          <th>网址：</th>          <td colspan="3">            <input type="text" name="customer_url[]" />         </td>   </tr>   <tr>          <th>地址：</th>          <td colspan="3">            <input type="text" name="customer_address[]" />         </td>   </tr>   <tr class="last_tr">          <th>邮编：</th>          <td class="post" colspan="3">           <input type="text" name ="customer_post[]" />         </td>       </tr>       ';
  
  //动态添加联系人
  $('#add_contacts').live('click',function(){
    $(this).parent().parent().before(add_contacts);
    
  })
  //动态添加分公司
  $('#add_company').live('click',function(){
    $(this).parent().parent().before(add_company);
      var customer_grades = $('.customer_grades:last').val();
      var status_grades = $('.status_grades').val(customer_grades);
  })
  //动态删除联系人
  $('.del_contacts').live('click',function(){
    $(this).parent().parent().nextAll().slice(0,5).remove();
    $(this).parent().parent().remove();
    $(this).remove();
  })
  //动态删除分公司信息
  $('.del_company').live('click',function(){
    $(this).parent().parent().nextAll().slice(0,5).remove();
    $(this).parent().parent().remove();
    $(this).remove();
  })

  //添加客户信息自动添加信息到状态跟进表中
  
  $('input[name ^= customer_name]').live('blur',function(){
    $('input[name ^= status_customer]').val($(this).val());
  })
  $('input[name ^=contacts_name]').live('blur',function(){
    $('input[name ^= status_contacts]').val($(this).val());
  })
  $('input[name ^=min_boss]').live('blur',function(){
    $('input[name = status_boss]').val($(this).val());
  })
  $('input[name ^= contacts_phone]').live('blur',function(){
    $('input[name ^= status_phone]').val($(this).val());
  })
  var index = $('select').eq(0).val();
  $('input[name ^= status_grade]').val(index);
  $('select').eq(0).change(function(){
    var index = $(this).val();
    $('input[name ^= status_grade]').val(index);
  });
  $('input[name = status_time]').val(nowDate);
  //提交信息的时候判断所需内容是否为空
  $('button').click(function(){
    var num = $('input[name ^= customer_name]').size();
    var contacts_num = $('input[name ^= contacts_name]').size();
    for(var i=0;i<num;i++){
      var name = $('input[name ^= customer_name]').eq(i).val();
      if(!$.trim(name)){
        alert('客户名称不能为空');
        $('input[name ^= customer_name]').eq(i).focus();
        return false;
      }
      var address = $('input[name ^= customer_address]').eq(i).val();
      if(!$.trim(address)){
        alert('地址不能为空');
        $('input[name ^= customer_address]').eq(i).focus();
        return false;
      }
      
    }
    for(var j=0;j<contacts_num;j++){
      var name = $('input[name ^= contacts_name]').eq(j).val();
      if(!$.trim(name)){
        alert('联系人姓名不能为空');
        $('input[name ^= contacts_name]').eq(j).focus();
        return false;
      }
      var phone = $('input[name ^= contacts_phone]').eq(j).val();
      var tel = $('input[name ^= contacts_tel]').eq(j).val();
      var email = $('input[name ^= contacts_email]').eq(j).val();
      if((!$.trim(phone)) && (!$.trim(tel)) && (!$.trim(email))){
        alert('联系人电话/手机,邮箱至少填写一项');
        if(!$.trim(phone)){
          $('input[name ^= contacts_phone]').eq(j).focus();
        } else if(!$.trim(tel)){
          $('input[name ^= contacts_tel]').eq(j).focus();
        } else {
          $('input[name ^= contacts_email]').eq(j).focus();
        }
        return false;
      }
    }
    var boss_val = $('#min_boss').val();
    if(boss_val == 0){
      alert('请选择负责人');
      $("#min_boss").focus();
      return false;
      }
    //判断跟进状态的必需字段不能为空
    var goal = $('input[name = status_goal]').val();
    if(!$.trim(goal)){
      alert('跟进目的不能为空');
      $('input[name = status_goal]').focus();
      return false;
    }
    var result = $('input[name = status_result]').val()
    if(!$.trim(result)){
      alert('跟进效果不能为空');
      $('input[name = status_result]').focus();
      return false;
    }
    var plan = $('input[name = status_plan]').val();
    if(!$.trim(plan)){
      alert('下步计划不能为空');
      $('input[name = status_plan]').focus();
      return false;
    }
    

  })
  //选择负责人后自动获取部门
  $('#min_boss').live('change',function(){
    var boss_val = $(this).val();
   
    $.post("../ajax_function/boss_dept.php",

    {boss_val:boss_val}, function(data,status){
     
      var depts = data.split('##');
      $('#boss_unit').val(depts[0]);
      $('#status_boss').val(depts[1]);

    });
  })
  //自动选择跟进状态的客户等级
  $('.customer_grades').live('change',function(){
    var customer_grades = $('.customer_grades:last').val();
    var status_grades = $('.status_grades').val(customer_grades);
  })
  })

</script>
<style type="text/css" media="screen">
  form{background:white;margin-left:-10px;}
  table tr td,table tr th{text-align:left;}
  table tr th{width:110px;}
  table:not(#customer_status) input{width:200px;height:25px;}
  .del{display:inline-block;width:40px;height:23px;background:#eee;text-align:center;line-height:23px;font-size:13px;cursor:pointer;}
  #save{clear:both;width:100%;height:100px;text-align:center;margin-top:20px;}
  #save button{width:180px;height:40px;cursor:pointer;margin-top:40px;}
  #customer{table-layout:fixed;}
  #customer_status tr td{border:1px solid #ddd;}
  #customer_status tr td{height:20px;width:100px;word-wrap:break-word;word-break:break-all}
  #customer_status tr th{text-align:center;}
  #customer_status tr td input{width:110px;}
</style>
<title>客户管理-嘉泰隆</title>
</head>

<body>
<?php include "header.php"; ?>
<div id="table_sheet">
  <?php
  if($action == 'add'){
    $sql_employee = "SELECT `employee_name`,`phone`,`email` FROM `db_employee` WHERE `employeeid` = '$employeeid'";
    $result_employee = $db->query($sql_employee);
    $array_employee = $result_employee->fetch_assoc();
  ?>
  <h4 style="background:#eee">客户信息</h4>
  <form action="customer_datado.php" method="post">
  <div style="width:1200px;margin:0px auto">
    <table style="width:390px;float:left;">
      <tr>
        <td colspan="4" style="text-align:center">客户基本信息</td>
      </tr>
      <tr>
          <th width="">代码：</th>
          <td style="width:70px">
            <input type="text" name="customer_code[]" style="width:70px" readonly/>
          </td>
          <td style="width:40px">等级：</td>
          <td>
            <select name="customer_grade[]" style="height:30px;width:70px" class="customer_grades">
              <?php foreach($array_customer_grade as $k=>$v){ 
                echo '<option value="'.$v.'">'.$v.'</option>';
                }
                ?>

            </select>
            
          </td>
    </tr>
    <tr>
          <th width="">名称：</th>
          <td width="" colspan="3">
            <input type="text" name="customer_name[]" />
          </td>
    </tr>
    <tr>
          <th>主营业务：</th>
          <td colspan="3">
            <input type="text" name="customer_business[]" />
          </td>
    </tr>
    <tr>
          <th>网址：</th>
          <td colspan="3">
            <input type="text" name="customer_url[]" />
          </td>
    </tr>
    <tr>
          <th>地址：</th>
          <td colspan="3">
            <input type="text" name="customer_address[]" />
          </td>
    </tr>
    <tr class="last_tr">
          <th>邮编：</th>
          <td class="post" colspan="3">
            <input type="text" name ="customer_post[]" />
          </td>
        </tr>
        <tr>
          <td colspan="4" style="text-align:center">
            <p id="add_company" style="width:100px;height:15px;background:grey;display:inline-block;cursor:pointer;border-radius:4px">新增分公司</p>
          </td>
        </tr>
    </table>
    <table style="width:390px;float:left;background:rgb(240,243,247)">
      <tr>
        <td colspan="2" style="text-align:center">客户联系人信息</td>
      </tr>
      <tr>
           <th>姓名：</th>
            <td width="">
                <input teyp="text" name="contacts_name[]" />
             </td>  
      </tr>
      <tr>
         <th>所属公司：</th>
           <td>
              <input type="text" name="contacts_company[]" />
           </td>  
      </tr>
      <tr>
         <th>职务：</th>
           <td>
              <input type="text" name="contacts_work[]" />
           </td>  
      </tr>
      
      <tr>
      <th>电话/手机：</th>
      <td>
           <input type="text" name="contacts_phone[]" />
      </td>
      </tr>
      <tr>
         <th>邮箱：</th>
            <td>
              <input type="text" name="contacts_email[]" />
            </td> 
      </tr>
      <tr>
          <th>备注：</th>
        <td>
            <input type="text" name="contacts_note[]" />
        </td>
      </tr>
      <tr>
        <td colspan="2" style="text-align:center">
          <p id="add_contacts" style="width:100px;height:15px;background:grey;display:inline-block;cursor:pointer;border-radius:4px">新增联系人</p>
        </td>
      </tr>
    </table>
    <table style="width:390px;float:left">
      <tr>
        <td colspan="2" style='text-align:center'>
          内部对接信息
        </td>
      </tr> 
      <tr>
        <th width="" >总负责人：</th>
         <td width="">
              <select name="boss_name[]" id="" style="width:200px;height:30px">
                 <?php foreach($employeess as $k=>$v){
                echo '<option value="'.$v['employee_name'].'">'.$v['employee_name'].'</option>';
                } ?>
            </select>
       </td>  
      </tr>
    <tr>
           <th width="">负责人：</th>
       <td>
            <select name="min_boss[]" id="min_boss" class="current_boss" style="width:200px;height:30px">
  
              <?php foreach($employees as $k=>$v){
                echo '<option value="'.$v['employee_name'].'">'.$v['employee_name'].'</option>';
                } ?>
            </select>
       </td>
       </tr>
      <tr>
         <th width="">所属部门：</th>
       <td>
            <input type="text" id="boss_unit" name="boss_unit[]" value="总经办" />
       </td>
      </tr>
      
    </table>
   </div>
<div style="clear:both"></div>
<h4 style="background:#eee">首次状态</h4>
<div style="border-bottom:1px solid #ddd"></div>
<div>
  <table id="customer_status" style="width:100%;margin:30px auto;">
    
    <tr>
      <th>时间</th>
      <th>客户代码</th>
      <th>客户等级</th>
      <th>客户公司</th>
      <th>客户联系人</th>
      <th>电话/手机</th>
      <th>负责人</th>
      <th>跟进目的</th>
      <th>跟进效果</th>
      <th>下步计划</th>
      <th>备注</th>
    </tr>
    <tr>
      <td>
        <input type="text" name="status_time" >
      </td>
      <td class="offset">
        <input type="text"  name="status_code" readonly="readonly">
      </td>
      <td class="offset">
        <select name="status_grade" style="width:112px" class="status_grades">
          <?php foreach($array_customer_grade as $k=>$v){ ?>
          <option value="<?php echo $v ?>"><?php echo $v ?></option>
          <?php } ?>

        </select>
      </td>
      
      <td >
        <input type="text" name="status_customer">
      </td> 
      <td class="offset">
        <input type="text" name="status_contacts">
      </td>
      <td>
        <input type="text" name="status_phone" >
      </td>
      
      <td class="offset">
        <input type="text" name="status_boss" id="status_boss" value="杨春民">
      </td>
      <td>
        <input type="text" name="status_goal">
      </td>
      <td>
        <input type="text" name="status_result">
      </td>
      <td>
        <input type="text" name="status_plan">
      </td>
      <td>
        <input type="text" name="status_note">
      </td>
    </tr>
  </table>
</div>
  <div id="save">
    <input type="hidden" name="submit" value="submit" />
    <input type="hidden" name="action" value="add" />
    <button>保存</button>
  </div>
</form>
   <!--
      <td colspan="2" style="text-align:center">
        
      </td>
    </tr>
   
    <tr id="add_button">
      <td colspan="6" style="text-align:center">
        <input type="submit" name="submit" value="添加"/>
      </td>
    </tr>
    
  </table>
  </form>
  <!--  <ul class="reg_ul">
      <li>
          <span>客户名称：</span>
          <input type="text" name="customer_name" value="" placeholder="4-8位用户名" class="customer_name">
          <span class="tip name_hint"></span>
      </li>
      <li>
          <span>客户代码：</span>
          <input type="text" name="customer_code" value="" placeholder="" class="customer_code">
          <span class="tip code_hint"></span>
      </li>
       <li>
          <span>客户系数：</span>
          <input type="text" name="customer_value" value="" placeholder="" class="customer_value">
          <span class="tip value_hint"></span>
      </li>
      <li>
          <span>联系人：</span>
          <input type="text" name="customer_contacts" value=""  placeholder="联系人姓名" class="customer_contacts">
          <span class="tip contacts_hint"></span>
      </li>
        <li>
          <span>手机号码：</span>
          <input type="text" name="customer_phone" value="" placeholder="手机号" class="customer_phone">
          <span class="tip phone_hint"></span>
      </li>
        <li>
          <span>邮箱：</span>
          <input type="text" name="customer_email" value="" placeholder="邮箱" class="customer_email">
          <span class="tip email_hint"></span>
      </li>
      <li>
          <span>地址：</span>
          <input type="text" name="customer_address" value="" placeholder="地址" class="customer_address">
          <span class="tip address_hint"></span>
      </li>
    
    <input type="hidden" value="add" name="action" >
      <li>
        <button type="submit" value="add" name="submit" class="red_button">添加</button>
      </li>
    </ul>
  </div>
 </form>
  <?php
    }  
  
  ?>-->
</div>
<?php include "../footer.php"; ?>
</body>
</html>