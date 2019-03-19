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
    //统计每月未完成数量
	$sql_job_no = "SELECT DATE_FORMAT(`db_job_plan`.`start_date`,'%Y-%m') AS `month`,COUNT(*) AS `count` FROM `db_job_plan_list` INNER JOIN `db_job_plan` ON `db_job_plan`.`planid` = `db_job_plan_list`.`planid` INNER JOIN `db_employee` ON `db_employee`.`employeeid` = `db_job_plan`.`employeeid` WHERE `db_job_plan`.`employeeid` = '$employeeid' AND DATE_FORMAT(`db_job_plan`.`start_date`,'%Y') = '$year' GROUP BY DATE_FORMAT(`db_job_plan`.`start_date`,'%Y-%m')";
	$result_job_no = $db->query($sql_job_no);
	if($result_job_no->num_rows){
		while($row_job_no = $result_job_no->fetch_assoc()){
			$array_job_no[$row_job_no['month']] = $row_job_no['count'];
		}
	}else{
		$array_job_no = array();
	}
	//统计每月总数
	$sql_job = "SELECT DATE_FORMAT(`db_job_plan`.`start_date`,'%Y-%m') AS `month`,COUNT(*) AS `count` FROM `db_job_plan` INNER JOIN `db_employee` ON `db_employee`.`employeeid` = `db_job_plan`.`employeeid` WHERE `db_job_plan`.`employeeid` = '$employeeid' AND DATE_FORMAT(`db_job_plan`.`start_date`,'%Y') = '$year' GROUP BY `db_job_plan`.`employeeid`,DATE_FORMAT(`db_job_plan`.`start_date`,'%Y-%m')";
	$result_job = $db->query($sql_job);
	if($result_job->num_rows){
		while($row_job = $result_job->fetch_assoc()){
			$array_job[$row_job['month']] = $row_job['count'];
		}
	}else{
		$array_job = array();
	}
	
	for($i=1;$i<=12;$i++){
		$month = date('Y-m',strtotime($year.'-'.$i));
		$job = array_key_exists($month,$array_job)?$array_job[$month]:0;
		$job_no = array_key_exists($month,$array_job_no)?$array_job_no[$month]:0;
		$job_yes = $job-$job_no;
		$ratio = round(@($job_yes/$job)*100,2);
		$array_date1[] = $ratio;
	}
	//print_r($array_date1);
	//图示处理
	$ydata1 = $array_date1;
	$titles = $year.'年'.$employee_name."计划完成率年报表";
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
	$yaxis = iconv("UTF-8", "gb2312", "百分比%");
	$line1 = iconv("UTF-8", "gb2312", "完成率");
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
	$lineplot1->value->SetFormat('%0.2f'); //格式化值
	$lineplot1->value->SetFont(FF_ARIAL,FS_NORMAL,9); //设置字体大小
	$lineplot1->SetLegend($line1); //设置图示值
	// Add the plot to the graph
	$graph->Add($lineplot1);
	echo $graph->Stroke();
}
?>