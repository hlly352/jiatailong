
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

    $(file).next().html('<img src="' + evt.target.result + '" width="95px" height="50px" />'); 

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