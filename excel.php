<?php


header("Content-type:application/vnd.ms-excel");
header("Content-type: text/html; charset=utf-8");
header("Content-Disposition:filename=赛事用户表.xls");
//$dbs =  new mysql($C->DB_HOST_M, $C->DB_USER, $C->DB_PASS, $C->DB_NAME1,$C->PORT);
//$info =$dbs->fetch_all('select * from saishiuser order by ctime desc') ;


echo '<table> <tr style="border:black 1px solid">
                            <th>单位</th>
                            <th>联系人</th>
                            <th>联系电话</th>
                            <th>领队</th>
                            <th>领队电话</th>
                            <th>男运动员</th>
                            <th>女运动员</th>
                            <th>替补</th>
                            <th>家长</th>
                            <th>孩子</th>
                            <th>报名时间</th>
                        </tr>';
//foreach($info as $val){
   // echo '<tr style="border:black 1px solid">';
  //  echo '<td>'.$val->danwei.'</td><td>'.$val->name.'</td><td>'.$val->phone.'</td><td>'.$val->lingdui.'</td><td>'.$val->ldphone.'</td><td>'.$val->nansporter1.','.$val->nansporter2.','.$val->nansporter3.','.$val->nansporter4.'</td><td>'.$val->nvsporter1.','.$val->nvsporter2.','.$val->nvsporter3.','.$val->nvsporter4.'</td><td>'.$val->tibu1.','.$val->tibu2.','.$val->tibu3.','.$val->tibu4.'</td><td>'.$val->jiazhang.'</td><td>'.$val->haizi.'</td><td>'.date('Y-m-d',$val->ctime).'</td>';
    echo '</tr>';
//}
echo "</table>";


?>