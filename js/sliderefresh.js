var utils = {
  isSlideRefresh: function(){
    /*判断是否需要下拉刷新*/
    /*
    <input type="text" id="url" value="" data-box="" data-data="" />
    value: 请求地址，不填取浏览器地址
    data-box: 接口成功后填充的容器
    data-data: 要提交的json数据
    */
    var url = '';
    var box;
    var data = {};
    var pages = -1;
    try{
      if(!$('#url').val()){
        url = window.location.href.substr(window.location.href.indexOf(window.location.hostname)+window.location.hostname.length,window.location.href.indexOf('?'));
      }else{
        url = $('#url').val();
      }
      box = $('#url').attr('data-box') || 'ul';
    }catch(e){}

    try{
      if(!!$('#url').attr('data-data')){
        
        if(typeof $('#url').attr('data-data') == 'object'){
          data = $('#url').attr('data-data');
        }else{
          data = JSON.parse($('#url').attr('data-data'));
        }

      }else{
        data = utils.searchPar();
      }
    }catch(e){}

    try{
      if(!!$('#url').attr('data-pages')){
        pages = parseInt($('#url').attr('data-pages'));
      }
    }catch(e){}

    utils.slideRefresh({
      contentBox: box,
      url: url,
      data: data,
      pages: pages
    });
  },
  slideRefresh: function(op){
    var options = $.extend({
      rootBox: 'body',
      contentBox: '',
      type: 'get',
      url: '',
      dataType: 'text',
      data: {},
      pages: 1,
      islastpage: false,//是否最后一页 true为最后一页并不请求
    },op);

    if(!options.page){
      options.data.page = 2;
    }
    // 翻页多刷新一次
    if((options.pages <= options.data.page && options.pages != 2) || options.pages == 0){
      return false;
    }

    if($('.slideShowText').length <= 0){
      $(options.contentBox).after('<div class="slideShowText"><div class="showmore ss">加载更多</div><div class="nodis showloading ss">努力加载中<i class="reflush"></i></div><div class="showend nodis ss">已到底部了哦</div></div>');
    }

    if(!!$('#url').hasClass('mlr030')){
      $(options.contentBox).siblings('.slideShowText').addClass('mlr030');
    }

    $(window).scroll(function(){
      // 最大滚动高度
      var maxH = $(document).height()-$(window).height();
      // 当前滚动高度
      var curH = document.body.scrollTop;

      if(curH >= maxH - 550){
        if(!!options.islastpage){
          return false;
        }

        try{
          if(options.data.page <= 2){
            options.data.page = 2;
          }
        }catch(e){}
        $(options.contentBox).siblings('.slideShowText').find('.ss').hide(0,function(){
          $(this).addClass('nodis');
          $(options.contentBox).siblings('.slideShowText').find('.showloading').removeClass('nodis').show();
        });
        if(!!window.ajax){
          return false;
        }
        utils.clickAjax({
          type: options.type,
          url: options.url,
          dataType: options.dataType,
          data: options.data,
          callbackSuccess: function(data){
            try{
              options.data.page = options.data.page + 1;
              $(options.rootBox).find(options.contentBox).append(data.html);
              if(!data.html){
                options.islastpage = true;
                $(options.contentBox).siblings('.slideShowText').find('.ss').hide(0,function(){
                  $(this).addClass('nodis');
                  $(options.contentBox).siblings('.slideShowText').find('.showend').removeClass('nodis').show();
                });
              }else{
                $(options.contentBox).siblings('.slideShowText').find('.ss').hide(0,function(){
                  $(this).addClass('nodis');
                  $(options.contentBox).siblings('.slideShowText').find('.showmore').removeClass('nodis').show();
                });
              }
            }catch(e){
              options.islastpage = true;
              $(options.contentBox).siblings('.slideShowText').find('.ss').hide(0,function(){
                $(this).addClass('nodis');
                $(options.contentBox).siblings('.slideShowText').find('.showend').removeClass('nodis').show();
              });
            }
            //utils._resizeFun();
          },
          callbackComplete: function(){
            $(options.rootBox).find('.common_refresh').addClass('nodis');
            //utils._resizeFun();
          },
        });
      }
    });

    $(window).trigger('scroll');
  },
  clickAjax: function(op){
    var options = $.extend({
      type: 'get',
      url: '',
      dataType: 'text',
      data: {},
      callbackSuccess: function(){},
      callbackError: function(){},
      callbackComplete: function(){},
    },op);
    if(!!window.ajax){
      return false;
    }
    window.ajax = true;
    $.ajax({
      type: options.type,
      url: options.url,
      dataType: options.dataType,
      data: options.data,
      success: function(json){
        var msg = {
          result:{
            status:{
              code: -9999
            }
          }
        };
        try{
          msg = JSON.parse(json);
        }catch(e){}

        if(msg.result.status.code=="0"){
          try{
            options.callbackSuccess.call(this,msg.result.data);
          }catch(e){}
        }else{
          options.callbackError.call(this,msg.result.data);
        }
      },
      complete: function(){
        options.callbackComplete.call(this);
        window.ajax = false;
      }
    });
  },
};