<?php
require_once '../global_mysql_connect.php';
require_once '../function/function.php';
require_once ('../jpgraph/jpgraph.php');
require_once ('../jpgraph/jpgraph_line.php');
require_once 'shell.php';
if($_GET['submit']){
	$year = $_GET['year']?$_GET['year']:date('Y');
	//统计预算费用
	$sql_budget = "SELECT `budget_cost`,DATE_FORMAT(`budget_month`,'%Y-%m') AS `budget_month` FROM `db_vehicle_budget` WHERE DATE_FORMAT(`budget_month`,'%Y') = '$year'";
	$result_budget = $db->query($sql_budget);
	if($result_budget->num_rows){
		while($row_budget = $result_budget->fetch_assoc()){
			$array_budget[$row_budget['budget_month']] = $row_budget['budget_cost'];
		}
	}else{
		$array_budget = array();
	}
	//统计实际费用
	$sql_vehicle = "SELECT DATE_FORMAT(`apply_date`,'%Y-%m') AS `apply_month`,COUNT(*) AS count,SUM((`odometer_finish`-`odometer_start`)*`charge`+(ROUND(`wait_time`)*`charge_wait`)+`charge_toll`+`charge_parking`) AS `cost` FROM `db_vehicle_list` WHERE `vehicle_status` = 1 AND `approve_status` = 'B' AND `reckoner` != 0 AND DATE_FORMAT(`apply_date`,'%Y') = '$year' GROUP BY DATE_FORMAT(`apply_date`,'%Y-%m')";
	$result_vehicle = $db->query($sql_vehicle);
	if($result_vehicle->num_rows){
		while($row_vehicle = $result_vehicle->fetch_assoc()){
			$array_vehicle[$row_vehicle['apply_month']] = $row_vehicle['cost'];
		}
	}else{
		$array_vehicle = array();
	}
	for($i=1;$i<=12;$i++){
		$month = date('Y-m',strtotime($year.'-'.$i));
		$cost = array_key_exists($month,$array_vehicle)?$array_vehicle[$month]:'0';
		$budget_cost = array_key_exists($month,$array_budget)?$array_budget[$month]:'0';
		$array_cost[] = $cost;
		$array_budget_cost[] = $budget_cost;
	}
	//图示处理
	$ydata1 = $array_cost;
	$ydata2 = $array_budget_cost;
	$titles = $year."年用车费用月报表";
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
	$xaxis = iconv("UTF-8", "gb2312", "日期");
	$yaxis = iconv("UTF-8", "gb2312", "费用");
	$line1 = iconv("UTF-8", "gb2312", "实际");
	$line2 = iconv("UTF-8", "gb2312", "预算");

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
	//$a=array(1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16,17,18,19,20,21,22,23,24,25,26,27,28,29,30,31);
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
	$lineplot2->SetColor('green'); //折线颜色
	$lineplot2->mark->SetType(MARK_FILLEDCIRCLE); //设置数据坐标点为圆形标记						
	$lineplot2->mark->SetFillColor("green");	//设置填充的颜色	
	$lineplot2->mark->SetWidth(4); //设置圆形标记的直径为4像素
	$lineplot2->value->Show(); //值是否显示                     
	$lineplot2->value->SetFormat('%0.1f'); //格式化值
	$lineplot2->value->SetFont(FF_ARIAL,FS_NORMAL,9); //设置字体大小
	$lineplot2->SetLegend($line2); //设置图示值
	// Add the plot to the graph
	$graph->Add($lineplot2);
	echo $graph->Stroke();
}
?>