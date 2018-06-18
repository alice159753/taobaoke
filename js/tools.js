function showHide(password)
{
    type = $("#"+password).attr("type");  

    if( type == 'text' )
    {
        $("#"+password).attr("Type","password");
    }
    else
    {
        $("#"+password).attr("Type","text");
    }
}


var ajax_flag = 1;  //防止AJAX重复提交

function myAjax(url, param, method)
{
  if(ajax_flag != 1) {
    return false;
  }

  ajax_flag = 0;  
  
  if(method == undefined) {
    method = 'GET';
  }
  
  if(param == undefined) {
    param = '';
  }
  
  $.ajax({
        url: url,
        type: method,
        data: param,
        dataType: 'json',
        success: function(data){
          alert(data['result']['status']['msg']);
        },
        error: function(html){
            alert('ajax请求失败');
        },
        complete: function (XMLHttpRequest, textStatus) {
          ajax_flag = 1;
        }
    });
}


function myAjax2(url, param, method)
{
  if(ajax_flag != 1) {
    return false;
  }

  ajax_flag = 0;  
  
  if(method == undefined) {
    method = 'GET';
  }
  
  if(param == undefined) {
    param = '';
  }
  
  result = {'count':0};

  $.ajax({
        url: url,
        type: method,
        data: param,
        dataType: 'json',
        async: false,
        success: function(data){
          result = data['result'];
          return data['result'];
        },
        error: function(html){
            alert('ajax请求失败');
        },
        complete: function (XMLHttpRequest, textStatus) {
          ajax_flag = 1;
        }
    });


  return result;
}



  function pic_upload()
  {
      var formData = new FormData();

      formData.append("file",$("#picList")[0].files[0]);
console.log(formData);
      $.ajax({ 
      url : 'img_upload.ajax.php', 
      type : 'POST', 
      data : formData, 
      processData : false, 
      contentType : false,
      dataType: 'json',
      beforeSend:function(){
        console.log("正在进行，请稍候");
      },
      success : function(responseStr) 
      { 
        if( responseStr['result']['status']['code']==0 )
        {
            $("#show_img").attr("src", responseStr['result']['data']['url']);

            $("#fileList").val(responseStr['result']['data']['url']);
        }
      }, 
      error : function(responseStr) { 
        console.log("error");
      } 

      });
      
  }


function clickfile()
{
  $("#picList").trigger("click");
}
