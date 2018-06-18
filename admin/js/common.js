
function add_file()
{
    var html = '<div style="margin-top:5px;"><input type="file" name="picList[]" style="display: inline;"><span style="cursor: pointer;color:orange;margin-left: -4px;" onclick="delete_file(this)">删除</span></div>';

    $("#pic_box").append(html);
}

function delete_file(delete_button)
{
  $(delete_button).parent().remove();
}

 function selectall(type)
 {
    if (type == 'all'){
        $("input[name='noList[]']").each(function (index, domEle) { 
            $(this).prop("checked", true);
        });
    } 
    else if( type == 'cancel' )
    {
        $("input[name='noList[]']").each(function (index, domEle) { 
            $(this).prop("checked", false);
        });
    }
    else if( type == 'invert' ) 
    {
        //反选
        $("input[name='noList[]']").each(function (index, domEle) { 
         if ($(this).prop('checked')) {
             $(this).prop("checked", false);
         }  else {
                 $(this).prop("checked", true);
         }
        });
    }
}

function more_choose(delete_button)
{
   if ($(delete_button).prop('checked')) 
   {
       $("input[name='noList[]']").each(function (index, domEle) { 
            $(this).prop("checked", true);
        });
   }
   else
   {
        $("input[name='noList[]']").each(function (index, domEle) { 
            $(this).prop("checked", false);
        });
   }

}

var ajax_flag = 1;  //防止AJAX重复提交

function myAjax(url, param, method, succ_callback, error_callback)
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
            if(data['result']['status']['code'] == 0){
              if(succ_callback == undefined) {
                alert('操作成功');
              }else{
                succ_callback(data['result']['data']);
              }
            }else{
              if(error_callback == undefined) {
                alert(data['result']['status']['msg']);
              }else{
                error_callback(data['result']['status']);
              }
            }
        },
        error: function(html){
            alert('ajax请求失败');
        },
        complete: function (XMLHttpRequest, textStatus) {
          ajax_flag = 1;
        }
    });
}



  function delete_more_make(url)
  {
    if( confirm("确定删除？") )
    {
        var queryData = "";

        var no_list = [];

        $("input[name='noList[]']").each(function (index, domEle) { 

            if( $(this).prop("checked") )
            {
              no_list.push($(this).val());
            }
        });

        no_list = no_list.join(',');

        queryData = "no_list="+ no_list;

        myAjax(url, queryData, 'POST');

        window.location.reload();
    }

  }

  function delete_one_make(url, no)
  {
      if( confirm("确定删除？") )
      {

        queryData = "no="+ no;

        myAjax(url, queryData, 'POST');

        window.location.reload();

      }
  }

  function modify_one_make(url, no)
  {
      if( confirm("确定修改？") )
      {

        queryData = "no="+ no;

        myAjax(url, queryData, 'POST');

        window.location.reload();

      }
  }

  function pic_upload()
  {
      var formData = new FormData();

      formData.append("file",$("#picList")[0].files[0]);

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


  function modify_make(url, field, value)
  {
    if( confirm("确定修改？") )
    {
        var queryData = "";

        var no_list = [];

        $("input[name='noList[]']").each(function (index, domEle) { 

            if( $(this).prop("checked") )
            {
              no_list.push($(this).val());
            }
        });

        no_list = no_list.join(',');

        queryData = "no_list="+ no_list+"&"+field+"="+value;

        myAjax(url, queryData, 'POST');

        window.location.reload();
    }

  }
