<?php
//配置连接数据库参数
$db_host       = "localhost";  //数据库主机
$db_user       = "root";       //数据库用户名
$db_pw         = "jiatailong";    //数据库密码
$db_dataname   = "xeldb";       //数据库名
$db_chareset   = "utf8";       //数据库连接字符串
//密码前缀
define('ALL_PW',"JTL");
//基础
$array_status = array('1'=>'有效','0'=>'无效');
$array_is_status = array('1'=>'是','0'=>'否');
$array_finish_status = array('1'=>'完成','0'=>'进行');
$array_week = array("日","一","二","三","四","五","六");
$array_system_type = array('A'=>'我的系统','B'=>'公用系统');
//登录
$array_login_status = array('A'=>'登录成功','B'=>'密码错误','C'=>'账号关闭','D'=>'账号不存在');
//人事
$array_employee_status = array('1'=>'在职','0'=>'离职');
$array_position_type = array('A'=>'总经理','B'=>'经理主管','C'=>'班组长','D'=>'员工');
$array_education_type = array('A'=>'小学','B'=>'初中','C'=>'高中','D'=>'中专','E'=>'大专','F'=>'本科');
$array_work_shift = array('A'=>'正常班8H','D'=>'白班12H','E'=>'夜班12H');
//PDCA
$array_pdca_status = array('P'=>'计划','D'=>'执行','C'=>'检查','A'=>'处理');
$array_pdca_result = array('A'=>'未接受','B'=>'执行中','C'=>'按时完成','D'=>'超期完成','E'=>'超期未完成');
$array_pdca_update_type = array('A'=>'工作反馈','B'=>'申请延期','C'=>'申请关闭');
//我的办公
$array_office_approve_status = array('A'=>'审核','B'=>'同意','C'=>'退回');
$array_express_paytype = array('A'=>'寄方付','B'=>'收方付');
$array_job_plan_type = array('A'=>'日计划','B'=>'周计划','C'=>'月计划','D'=>'年计划');
$array_routine_work_type = array('A'=>'按星期','B'=>'按月','C'=>'固定日期');
//用车
$array_vehicle_type = array('A'=>'公车','B'=>'外叫车');
$array_vehicle_dotype = array('A'=>'返工','B'=>'接送客户','C'=>'补货','D'=>'拜访','E'=>'送货','F'=>'其他');
$array_vehicle_roundtype = array('A'=>'单程','B'=>'往返');
$array_vehicle_category = array('A'=>'小车','B'=>'货车1T','C'=>'货车2T','D'=>'货车3T','E'=>'货车5T','F'=>'货车10T');
$array_vehicle_pathtype = array('A'=>'市区','B'=>'长途');
$array_express_get_status = array('1'=>'已领','0'=>"未领");
$array_express_apply_status = array('1'=>'是','0'=>"否");
//模具数据
$array_mould_cavity_type = array('A'=>'1*1','B'=>'1*2','C'=>'1*4','D'=>'1+1','E'=>'2+2');
$array_mould_quality_grade = array('A+','A','B','C');
$array_mould_assembler = array('A'=>'一组','B'=>'二组','C'=>'三组','D'=>'四组');
$array_mould_inout_status = array(0=>'未回',1=>'已回');
$array_tax_rate = array('0.16','0.17','0.05','0.03','0.00');
//物料系统
$array_order_status = array(0=>'未下单',1=>'已下单');
$array_inout_dotype = array('I'=>'入库','O'=>'出库');
//刀具数据
$array_cutter_texture = array('A'=>'钨钢','B'=>'合金','C'=>'预硬钢');
//模具报价
$array_quote_status = array(0=>'报价',1=>'成交');
//热处理数据
$array_mould_heat = array('tempered'=>'调质/Tempered','hardened'=>'淬火/Hardened','nitridation'=>'氮化/Nitridation');
//材料名称
$array_mould_material = array('base'=>'模架/Mode Base','cavity'=>'型腔/Cavity','core'=>'型芯/Core','silde'=>'滑块/Slide&Lifters','inserts'=>'镶件/Inserts','electrode'=>'电极/Electrode');
$array_mould_materials = array('base'=>'模架/Mode Base','cavity'=>'型腔1/Cavity','cavitys'=>'型腔2/Cavity','core'=>'型芯1/Core','cores'=>'型芯2/Core','silde'=>'滑块/Slide&Lifters','inserts'=>'镶件/Inserts','electrode'=>'电极/Electrode');
//材料牌号
$array_material_specification = array(0=>'1.2312',1=>'1.2343',2=>'Cu');
//模具配件数据
$array_mold_standard = array('Inserts'=>'镶件、日期章/Inserts','sleeve'=>'顶杆、顶管/Ejection Pin\\Sleeve','connector'=>'水管、油管接头/Connector','components'=>'标准件/Standard Components','hotrunner'=>'热流道/Hot Runner','tempcontroller'=>'温控器/Temp Controller','cylinder'=>'油缸/Hydro-cylinder');
//模具设计项目
$array_mould_design = array('scanning'=>'扫描测绘/Scanning','cad'=>'结构设计/CAD','cam'=>'CAM设计/CAM','cae'=>'CAE分析/CAE');
//模具加工费数据
$array_mould_manufacturing = array('maching'=>'一般机床/Maching','grinding'=>'磨床/Grinding','cnc'=>'数控机床/CNC','precision_cnc'=>'精密数控机床','wc'=>'线切割/W.C.','edm'=>'电火花/EDM','polish'=>'抛光/Polish','fitting'=>'钳工/Fitting','laser'=>'激光烧焊/Laser Welding','texture'=>'皮纹/Texture cost');
?>