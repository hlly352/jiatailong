 $(function(){
 //点击图片
    $('.img').live('click',function(e){
      //图片地址
      var img_file = $(this).html();
      var client_width = e.originalEvent.pageX+40;
      var client_height = e.originalEvent.pageY;
      var divs = '<div  id="divs" style="position:absolute;top:'+client_height+'px;left:'+client_width+'px">'+img_file+'</div>';
      $('#table_list').prepend(divs);
      $('#divs').children('img').css('width','600px');
      $('#divs').children('img').css('height','300px');
    })
      $(document).mouseup(function (e) {
        var con = $("#divs");   // 设置目标区域
        if (!con.is(e.target) && con.has(e.target).length === 0) {
            $('#divs').remove();
        }
    });
//评审表点击图片放大
//点击图片
    $('.pic').live('click',function(e){
      //图片地址
      var img_file = $(this);
      img_file = img_file.context.src;
      var client_height = e.originalEvent.pageY;
      var divs = '<div  id="divs" style="position:absolute;top:'+client_height+'px;left:35%"><img src="'+img_file+'"></div>';
      $('#table_list').prepend(divs);
      $('#divs').children('img').css('width','600px');
      $('#divs').children('img').css('height','300px');
    })
      $(document).mouseup(function (e) {
        var con = $("#divs");   // 设置目标区域
        if (!con.is(e.target) && con.has(e.target).length === 0) {
            $('#divs').remove();
        }
    });
})