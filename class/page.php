<?php
/*
 * page 分页类  Build By zzs In 2011/02/15 
 * Creat object:New page(总记录数,每页数)
 * User function getpage()
 */
class page{
	
	private $total;         //总记录数
	private $url;           //当前页面地址
	private  $page;      //当前页码,初始值为1
	private $pagesize;      //每页默认记录数
	private  $totalpage;     //总页面数
	private $prepg;         //上一页
	private $nextpg;        //下一页
	public  $limitsql;      //limit sql条件

function  __construct($total,$pagesize){
	
	$this->total = $total;
	$this->pagesize = $pagesize;
	//判断page值
	$this->page = (!preg_match("/^[1-9]\d*$/", $_GET['page'])) ? 1 : $_GET['page'];
//	if(!preg_match("/^[1-9]\d*$/", $_GET['page'])){
//		$this->page = 1;
//	}else{
//		$this->page = $_GET['page'];
//	}
	$this->url = $_SERVER['REQUEST_URI']; // 获取当前页地址
	$this->parseUrl(); //解析地址
	$this->totalpage = ceil($this->total/$this->pagesize); //总页数
	if(preg_match("/^[1-9]\d*$/", $this->totalpage)){
		$this->page = min($this->totalpage,$this->page);
	}
		
//	if($this->totalpage <= '0'){
//		$this->page = $this->page;
//	}else{
//		$this->page = min($this->totalpage,$this->page); //如果当前页大于最大页,当前页为最大页面
//	}
	$this->prepg =  $this->page-1; //上一页
	$this->nextpg = $this->page+1; //下一页
	$this->limitsql = " LIMIT " . ($this->page-1) * $this->pagesize . "," . $this->pagesize; //limit sql 语句
	
}

//地址解析方法
private function parseUrl(){
	
	$parse_url = parse_url($this->url);
	$url_query = $parse_url['query'];
	if($url_query){
		$url_query = preg_replace("/(^|&)page=" . $_GET['page']."/", "", $url_query);	
	    $this->url = str_replace($parse_url['query'], $url_query, $this->url);
	    if($url_query){
	    	$this->url .= "&page"; //带ID=参数
		 }else{
		 	$this->url .= "page"; //?page=页面
		 }	 
	}else{
		$this->url .="?page"; //不带参数
	}
	
}

//输出页面方法
public function getPage(){
	if($this->total > 0){
		if($this->page == 1){
			echo "<span class=\"disabled\"> < </span>";
		}else{
			echo "<a href=\"$this->url=$this->prepg\"> < </a>";
		}
		if($this->page <= 4){
			$first = 1;
			if($this->totalpage < 7){
				$end = $this->totalpage;
			}else{
				$end = 7;
			}
		}elseif($this->page > 4 AND ($this->totalpage - $this->page) >= 3){
			$first = $this->page-3;
			$end = $this->page+3;
		}elseif(($this->totalpage - $this->page) < 3){
			$first = $this->totalpage-6;
			if($first <= 0) $first = 1;
			$end = $this->totalpage;
		}
		if($this->page >= 5 AND ($this->totalpage > 7)){
			echo "<a href=\"$this->url=1\">1...</a>";
		}
		for($i=$first; $i<= $end; $i++){
			if($i == $this->page){
				echo "<span class=\"current\">$i</span>"; 
			}else{
				echo "<a href=\"$this->url=$i\">$i</a>";
			}
		}
		if(($this->totalpage - $this->page) >= 4  AND $this->totalpage > 7){
			echo "<a href=\"$this->url=$this->totalpage\">...$this->totalpage</a>";
		}
		if($this->page == $this->totalpage){
			echo "<span class=\"disabled\"> > </span>";
		}else{
			echo "<a href=\"$this->url=$this->nextpg\"> > </a>";
		}
		echo "<span class=\"pagetxt\">页码:" . $this->page . "/" . $this->totalpage . " 总计:" .$this->total . " 条记录</span>";
	}
}
}
?>