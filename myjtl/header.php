<div id="myjtl_top">
	 <p>
	   <img src="../images/system_ico/calendar_10_10.png" width="10" height="10" />
	   <?php echo date('Y-m-d')." 星期".$array_week[date("w")]; ?> 
	   <img src="../images/system_ico/employee_10_10.png" width="10" height="10" /> 
	   <?php echo $_SESSION['employee_info']['employee_name']; ?> 
	   <a href="../passport/logout.php" style="color: red;">退出</a>
	</p>
</div>
<div id="myjtl_header">
  <div id="myjtl_header_banner">
	  	<span class="header_left"></span>
	  	<span class="header_right">信息平台
	  		<em>v1.1.20190314</em>
	  	</span>
	  	<span class="clear"></span>
  </div>
</div>