set "Ymd=%date:~,4%%date:~5,2%%date:~8,2%" 

"D:\wamp\bin\mysql\mysql5.6.17\bin\mysqldump.exe"  -uroot -pjiatailong xeldb > d:\site\dbbak\%Ymd%.sql 