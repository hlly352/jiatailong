
//上传产品图片预览
function view_data(file){
    var filepath = $(file).val();  
    var extStart = filepath.lastIndexOf(".")+1;
    var ext = filepath.substring(extStart, filepath.length).toUpperCase();
    var allowtype = ["JPG","GIF","PNG"];
    if($.inArray(ext,allowtype) == -1)
    {
      alert("请选择正确文件类型");
      $(file).val('');
      return false;
    }
    $(file).prev().empty()
    if (file.files && file.files[0]){ 

    var reader = new FileReader(); 

    reader.onload = function(evt){ 

    $(file).prev().html('<img src="' + evt.target.result + '" width="300px" height="150px" />'); 

    } 

    reader.readAsDataURL(file.files[0]); 

    }else{

    $(file).prev().html('<p style="filter:progid:DXImageTransform.Microsoft.AlphaImageLoader(sizingMethod=scale,src=\'' + file.value + '\'"></p>'); 

    } 
    var files = ' <input type="file" name="file[]" onchange="view(this)"/><span style="margin-left:20px"></span>';
    $(file).next().after(files);
  } 


 //上传图片之前预览图片
function view(file){
    $(file).prev('.mould_image').remove();
    var filepath = $(file).val();
    var extStart = filepath.lastIndexOf(".")+1;
    var ext = filepath.substring(extStart, filepath.length).toUpperCase();
    var allowtype = ["JPG","GIF","PNG"];
    if($.inArray(ext,allowtype) == -1)
    {
      alert("请选择正确文件类型");
      $(file).val('');
      return false;
    }
    if($(file).prevAll().size()<11){
    $(file).css('display','none');
    if (file.files && file.files[0]){ 

    var reader = new FileReader(); 

    reader.onload = function(evt){ 

    $(file).next().html('<img src="' + evt.target.result + '" width=95px" height="50px" />'); 

    } 

    reader.readAsDataURL(file.files[0]); 

    }else{

    $(file).next().html('<p style="filter:progid:DXImageTransform.Microsoft.AlphaImageLoader(sizingMethod=scale,src=\'' + file.value + '\'"></p>'); 

    } 
    var files = ' <input type="file" name="file[]" onchange="view(this)"/><span style="margin-left:20px"></span>';
    $(file).next().after(files);
  } else {
    alert('最多上传六张图片');
    $(file).remove();
  }
 }
  //上传图片之前预览图片
function views(file){
    $(file).prevAll('.mould_image').remove();
    var filepath = $(file).val();
    var extStart = filepath.lastIndexOf(".")+1;
    var ext = filepath.substring(extStart, filepath.length).toUpperCase();
    var allowtype = ["JPG","GIF","PNG"];
    if($.inArray(ext,allowtype) == -1)
    {
      alert("请选择正确文件类型");
      $(file).val('');
      return false;
    }
    $(file).css('display','none');
    if (file.files && file.files[0]){ 
    var reader = new FileReader(); 

    reader.onload = function(evt){ 

    $(file).next().html('<img src="' + evt.target.result + '" height="200px" />'); 

    } 

    reader.readAsDataURL(file.files[0]); 

    }else{

    $(file).next().html('<p style="filter:progid:DXImageTransform.Microsoft.AlphaImageLoader(sizingMethod=scale,src=\'' + file.value + '\'"></p>'); 

    } 
    var files = ' <input type="file" name="file[]" onchange="views(this)"/><span style="margin-left:20px"></span>';
    $(file).next().after(files);

 }
  //设计评审上传图片之前预览图片
function review_scan(file,names){
    console.log(names);
    $(file).prevAll('.mould_image').remove();
    var filepath = $(file).val();
    var extStart = filepath.lastIndexOf(".")+1;
    var ext = filepath.substring(extStart, filepath.length).toUpperCase();
    var allowtype = ["JPG","GIF","PNG"];
    if($.inArray(ext,allowtype) == -1)
    {
      alert("请选择正确文件类型");
      $(file).val('');
      return false;
    }
     $(file).css('display','none');
    if (file.files && file.files[0]){ 
    var reader = new FileReader(); 

    reader.onload = function(evt){ 

    $(file).next().html('<img src="' + evt.target.result + '" height="100px" />'); 

    } 

    reader.readAsDataURL(file.files[0]); 

    }else{

    $(file).next().html('<p style="filter:progid:DXImageTransform.Microsoft.AlphaImageLoader(sizingMethod=scale,src=\'' + file.value + '\'"></p>'); 

    } 
      var files = ' <input type="file" name="'+names+'[]" onchange="review_scan(this,\''+names+'\')"/><span style="margin-left:20px"></span>';
    $(file).next().after(files);

 }
   //上传图片之前预览图片
function mould_change(file){
    $(file).prevAll('.mould_image').remove();
    var filepath = $(file).val();
    var extStart = filepath.lastIndexOf(".")+1;
    var ext = filepath.substring(extStart, filepath.length).toUpperCase();
    var allowtype = ["JPG","GIF","PNG"];
    if($.inArray(ext,allowtype) == -1)
    {
      alert("请选择正确文件类型");
      $(file).val('');
      return false;
    }
    $(file).css('display','none');
    if (file.files && file.files[0]){ 
    var reader = new FileReader(); 

    reader.onload = function(evt){ 

    $(file).next().html('<img width="510px" height="230px"  src="' + evt.target.result + '"  /><input type="text" name="pic_remark[]" style="display:block;margin:5px auto;width:445px"/>'); 

    } 

    reader.readAsDataURL(file.files[0]); 

    }else{

    $(file).next().html('<p style="filter:progid:DXImageTransform.Microsoft.AlphaImageLoader(sizingMethod=scale,src=\'' + file.value + '\'"></p>'); 

    } 
    var files = '<input type="file" style="margin-bottom:20px" name="file[]" onchange="mould_change(this)"/><div style="margin-left:20px;float:left;"></div>';
    $(file).next().after(files);

 }
 $(function(){
 //正常订单提交时，判断是否填写了缩水率
 $('#saves').live('click',function(){
    var shrink = $('input[name=shrink]').val();
    if(shrink == ''){
        alert('请填写缩水率');
        $('input[name=shrink]').focus();
        return false;
    }
 })

})