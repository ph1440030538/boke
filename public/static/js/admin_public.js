var fuyun = function(){
};
//分页
fuyun.prototype.page = function(){
  var requestUrl = document.URL;
  Vue.component('fuyun-page', {
    props: ['currentPage','pagesize','total'], 
    template: ' \
      <div id="page">\
        <el-pagination\
          @current-change="handleCurrentChange"\
          :current-page="currentPage"\
          :page-size="pagesize"\
          layout="prev, pager, next, jumper"\
          :total="total">\
        </el-pagination>\
      </div>',
    data: function () {
      return {
        requestUrl: requestUrl,          
      }
    },
    methods: {
      handleCurrentChange(page) {
        var url = this.requestUrl;
        if(url.indexOf("?")==-1){
          window.location.href = url+"?page="+page;
        }else{
          if(url.indexOf("page")==-1){
            window.location.href = url+"&page="+page;
          }else{
            window.location.href = url.replace(/page=\d*/, 'page='+page)
          }                            
        }
      }
    },
  })
}

fuyun.components = function(components, main){
  var fy = new fuyun();
  for(var i in components){
    eval("fy."+components[i]+"();");
  }
  new Vue(main);
}


layui.use('jquery', function(){
  var $ = layui.jquery;
  //删除单个
  $(document).on("click",".btn-del-id", function(){
    var url = $(this).data('url');
    var id = $(this).data("id");
    var successtip = $(this).data("successtip");
    var confirm = $(this).data("confirm");
    var layerDelIndex = layer.confirm(confirm, {
      btn: ['是','否'] //按钮
    }, function(){
      $.ajax({
        type:'post',
        url: url,
        data:{id:id},
        success: function(data){
          $("tr[data-id='"+id+"']").remove();
          layer.msg(successtip);
          layer.close(layerDelIndex);
        }
      });
    }, function(){
      return;
    });
  }); 
  //删除多个
  $(document).on("click",".btn-del-ids", function(){
    var url = $(this).data('url');
    var id = $(this).data("id");
    var successtip = $(this).data("successtip");
    var confirm = $(this).data("confirm");
    var layerDelIndex = layer.confirm(confirm, {
      btn: ['是','否'] //按钮
    }, function(){
      var ids = [];
      $($("input[name='id[]']")).each(function(){
          if( this.checked ){
              ids.push($(this).val());
          }
      });
      if(ids.length==0){
        layer.msg("没有选中任何选项");
        return;
      }
      $.ajax({
        type:'post',
        url: url,
        data:{id: ids},
        success: function(data){
          $(ids).each(function(value,id){
            $("tr[data-id='"+id+"']").remove();
          })
          layer.msg(successtip);
          layer.close(layerDelIndex);
        }
      });
    }, function(){
      return;
    });

    
  })
});



  
 

