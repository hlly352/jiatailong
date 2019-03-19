<?php
require_once '../global_mysql_connect.php';
require_once '../function/function.php';
require_once ('../jpgraph/jpgraph.php');
require_once ('../jpgraph/jpgraph_line.php');
require_once 'shell.php';
$employeeid = fun_check_int($_GET['id']);
$year = $_GET['year']?$_GET['year']:date('Y');
$sql = "SELECT `employee_name` FROM `db_employee` WHERE `employeeid` = '$employeeid'";
$result = $db->query($sql);
if($result->num_rows){
	$array = $result->fetch_assoc();
	$employee_name = $array['employee_name'];
	//查询请假
	$sql_leave = "SELECT DATE_FORMAT(`start_time`,'%Y-%m') AS `month`,SUM(`leavetime`) AS `leavetime` FROM `db_employee_leave` WHERE `applyer` = '$employeeid' AND DATE_FORMAT(`start_time`,'%Y') = '$year' AND `approve_status` = 'B' AND `leave_status` = 1 AND `confirmer` !=0 GROUP BY DATE_FORMAT(`start_time`,'%Y-%m')";
	$result_leave = $db->query($sql_leave);
	if($result_leave->num_rows){
		while($row_leave = $result_leave->fetch_assoc()){
			$array_leave[$row_leave['month']] = $row_leave['leavetime'];
		}
	}else{
		$array_leave = array();
	}
	//print_r($array_leave);
	//查询加班
	$sql_overtime = "SELECT DATE_FORMAT(`start_time`,'%Y-%m') AS `month`,SUM(`overtime`) AS `overtime` FROM `db_employee_overtime` WHERE `applyer` = '$employeeid' AND DATE_FORMAT(`start_time`,'%Y') = '$year' AND `approve_status` = 'B' AND `overtime_status` = 1 AND `confirmer` !=0 GROUP BY DATE_FORMAT(`start_time`,'%Y-%m')";
	$result_overtime = $db->query($sql_overtime);
	if($result_overtime->num_rows){
		while($row_overtime = $result_overtime->fetch_assoc()){
			$array_overtime[$row_overtime['month']] = $row_overtime['overtime'];
		}
	}else{
		$array_overtime = array();
	}
	//print_r($array_overtime);
	for($i=1;$i<=12;$i++){
		$month = date('Y-m',strtotime($year.'-'.$i));
		$array_date1[] = number_format(array_key_exists($month,$array_leave)?$array_leave[$month]:0,1);
		$array_date2[] = number_format(array_key_exists($month,$array_overtime)?$array_overtime[$month]:0,1);
	}
	//print_r($array_date1);
	//print_r($array_date2);
	//图示处理
	$ydata1 = $array_date1;
	$ydata2 = $array_date2;
	$titles = $year.'年'.$employee_name."考勤年报表";
	//全局变量
	$graph = new Graph(1208,600,"jpg"); //设置画布大小	
	$graph->SetScale("textlin"); //设置为折线
	$graph->SetShadow(); //设置阴影
	$graph->SetMarginColor("lightblue"); //画布背景色
	$graph->img->SetAntiAliasing();	// 设置折线平滑度
	$graph->img->SetMargin(90,120,80,80); //设置画布的边界
	$graph->legend->Pos(0.02,0.5,"right","center"); //设置图列的位置
	$graph->legend->SetFont(FF_SIMSUN,FS_NORMAL); //设置图列字体
	//$graph->legend->SetFillColor('lightblue@0.3'); //设置图列填充颜色
	//$graph->legend->SetShadow('darkgray@0.1'); //设置图列阴影
	//转换UTF-8
	$title = iconv("UTF-8", "gb2312", $titles);
	$xaxis = iconv("UTF-8", "gb2312", "月份");
	$yaxis = iconv("UTF-8", "gb2312", "小时");
	$line1 = iconv("UTF-8", "gb2312", "请假");
	$line2 = iconv("UTF-8", "gb2312", "加班");
	//设置标题
	$graph->title->Set($title); //设置标题
	$graph->title->SetMargin(30); //设置标题边距
	$graph->title->SetFont(FF_SIMSUN,FS_BOLD,16); //设置标题字体与大小
	//$graph->title->SetColor('red');  ///标题颜色
	//设置X轴属性
	$graph->xaxis->title->Set($xaxis); //设置X轴标题
	$graph->xaxis->title->SetMargin(10); //设置X轴标题位置
	//$graph->xaxis->SetLabelAngle(30); //设置X轴的显示值的角度;
	$graph->xaxis->title->SetFont(FF_SIMSUN,FS_BOLD,9); //设置X轴字体大小
	$a = array();
	for($i=1;$i<=12;$i++){
		$a[] = $i;
	}
	$graph->xaxis->SetTickLabels($a); //设置X轴刻度值
	//设置Y轴属性
	$graph->yaxis->title->Set($yaxis); //设置Y轴标题
	$graph->yaxis->title->SetMargin(25); //设置Y轴标题位置
	$graph->yaxis->title->SetFont(FF_SIMSUN,FS_BOLD,9); //设置Y轴字体大小
	//$graph->yaxis->scale->SetGrace(20); //设置刻度最大值
	//$graph->ygrid->Show(true); //是隔行显示
	$graph->ygrid->SetFill(true,'#EFEFEF@0.5','#BBCCFF@0.5'); //设置Y是否填充隔行换色
	//设置X Y字体
	$graph->xaxis->SetFont(FF_ARIAL,FS_NORMAL,9);
	$graph->yaxis->SetFont(FF_ARIAL,FS_NORMAL,9);
	//设置折线1
	$lineplot1=new LinePlot($ydata1);
	$lineplot1->SetWeight(2); // 折线宽度
	$lineplot1->SetColor('red'); //折线颜色
	$lineplot1->mark->SetType(MARK_FILLEDCIRCLE); //设置数据坐标点为圆形标记						
	$lineplot1->mark->SetFillColor("red");	//设置填充的颜色	
	$lineplot1->mark->SetWidth(4); //设置圆形标记的直径为4像素
	$lineplot1->value->Show(); //值是否显示                     
	$lineplot1->value->SetFormat('%0.1f'); //格式化值
	$lineplot1->value->SetFont(FF_ARIAL,FS_NORMAL,9); //设置字体大小
	$lineplot1->SetLegend($line1); //设置图示值
	// Add the plot to the graph
	$graph->Add($lineplot1);
	//设置折线2
	$lineplot2=new LinePlot($ydata2);
	$lineplot2->SetWeight(2); // 折线宽度
	$lineplot2->SetColor('orange'); //折线颜色
	$lineplot2->mark->SetType(MARK_FILLEDCIRCLE); //设置数据坐标点为圆形标记							
	$lineplot2->mark->SetFillColor("orange"); //设置填充的颜色
	$lineplot2->mark->SetWidth(4); //设置圆形标记的直径为4像素
	$lineplot2->value->Show(); //值是否显示                 
	$lineplot2->value->SetFormat('%0.1f'); //格式化值
	$lineplot2->value->SetFont(FF_ARIAL,FS_NORMAL,9); //设置字体大小
	$lineplot2->SetLegend($line2); //设置图示值
	// Add the second plot to the graph
	$graph->Add($lineplot2);
	echo $graph->Stroke();
}
?>