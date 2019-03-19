<?php
require_once '../global_mysql_connect.php';
require_once '../function/function.php';
require_once ('../jpgraph/jpgraph.php');
require_once ('../jpgraph/jpgraph_line.php');
require_once 'shell.php';
$month = $_GET['month']?$_GET['month']:date('Y-m');
$year = date('Y',strtotime($month));
$sql = "SELECT DATE_FORMAT(`db_employee_express`.`apply_date`,'%Y-%m') AS `month`,SUM(`db_employee_express`.`cost`) AS `cost` FROM `db_employee_express` INNER JOIN `db_employee` ON `db_employee`.`employeeid` = `db_employee_express`.`applyer` WHERE DATE_FORMAT(`db_employee_express`.`apply_date`,'%Y') = '$year' AND `db_employee_express`.`approve_status` = 'B' AND `db_employee_express`.`express_status` = 1 AND `db_employee_express`.`reckoner` != 0 GROUP BY DATE_FORMAT(`db_employee_express`.`apply_date`,'%Y-%m')";
$result = $db->query($sql);
if($result->num_rows){
	while($row = $result->fetch_assoc()){
		$array_express[$row['month']] = $row['cost'];
	}	
	for($i=1;$i<=12;$i++){
		$month = date('Y-m',strtotime($year.'-'.$i));
		$array_date1[] = array_key_exists($month,$array_express)?$array_express[$month]:0;
	}
	//print_r($array_date1);
	//图示处理
	$ydata1 = $array_date1;
	$titles = $year.'年快递(寄)费年用报表';
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
	$yaxis = iconv("UTF-8", "gb2312", "费用");
	$line1 = iconv("UTF-8", "gb2312", "快递");
	
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
	echo $graph->Stroke();
}
?>