
layui.define('jquery', function(exports){
  "use strict";
  
  var $ = layui.jquery;
  var hint = layui.hint();
  
  var enterSkin = 'layui-tree-enter', Tree = function(options){
    this.options = options;
  };
  
  //图标
  var icon = {
    arrow: ['&#xe623;', '&#xe625;'] //箭头
    ,checkbox: ['&#xe626;', '&#xe627;'] //复选框
    ,radio: ['&#xe62b;', '&#xe62a;'] //单选框
    ,branch: ['&#xe622;', '&#xe624;'] //父节点
    ,leaf: '&#xe621;' //叶节点
  };
  
  //初始化
  Tree.prototype.init = function(elem){
    var that = this;
    elem.addClass('layui-elem-field layui-field-title'); //添加tree样式
    // console.log( this.options.nodes );
    // if(that.options.skin){
    //   elem.addClass('layui-tree-skin-'+ that.options.skin);
    // }
    that.tree(elem);
    that.on(elem);
  };
  
  //树节点解析
  Tree.prototype.tree = function(elem, children, length = 0,parentid = 0){
    var that = this, options = that.options
    var nodes = children || options.nodes;
    var is_spread = options.is_spread || false;

    if(length > 3){
    	return;
    }
    length ++;
   

    layui.each(nodes, function(index, item){
      var hasChild = item['child'] && item['child'].length > 0;
      var tr = $('<tr'+
      	function(){
      		return " class='tree-child' data-spread='"+is_spread+"' data-parentid='"+parentid+"'";
      	}()
      	+
      	function(){
      		return hasChild ? " data-id='"+item['id']+"'":"";
      	}()
      	+
      	function(){
      		if(parentid != 0&&is_spread == true){
      			return " style='display:none'";
      		}
      	}()

      	+'></tr>');
      var td = $(['<td>'+item.id+'</td>',
      	'<td>'
      	+
  		function(){
  			var space = "";
  			for (var i = 1; i < length; i++) {
  				space += "<span class='tree-space'></span>";
  			}
  			return space;
  		}()
      	+
      	function(){
			return hasChild ? '<i class="layui-icon layui-tree-spread">'+ ( 
				item.spread ? icon.arrow[1] : icon.arrow[0]
			) +'</i>' : '';
      	}()
      	+item.name+'</td>',
      	'<td>'+item.sort+' </td>',
      	'<td>'
        +'\
  <div class="layui-btn-group">\
    <a class="layui-btn layui-btn-mini" href="add?id='+item.id+'" >添加子菜单</a>\
    <a class="layui-btn layui-btn-mini"  href="edit?id='+item.id+'" >编辑</a>\
    <a class="layui-btn layui-btn-mini"  href="javascript:;" onclick="del('+item.id+',this)">删除</a>\
  </div>'
      ].join(''));
      $(tr).append(td);
      elem.append(tr);

      //如果有子节点，则递归继续生成树
      if(hasChild){
        that.tree(elem, item['child'], length, item['id']);
      }


      // //触发点击节点回调
      typeof options.click === 'function' && that.click(tr, item); 
      // console.log( typeof options.click );
      // //伸展节点
      that.spread(tr, item);
      
      // //拖拽节点
      // options.drag && that.drag(li, item); 
    });
  };
  
  //点击节点回调
  Tree.prototype.click = function(elem, item){
    // var that = this, options = that.options;
    // elem.children('a').on('click', function(e){
    //   layui.stope(e);
    //   options.click(item)
    // });
  };
  
  //伸展节点
  Tree.prototype.spread = function(elem, item){
  	var id = $(elem).attr("data-id");
  	if(!id){
  		return;
  	}
   	
  	// $("tr[data-id="+id+"]").togger();
    // console.log( $("tr[data-id="+id+"]").length );

    var that = this, options = that.options;
    var arrow = elem.children('tr[data-id="+id+"]')
    var ul = elem.children('ul'), a = elem.children('a');
    
    //执行伸展
    var open = function(){
    	
    	if($(elem).attr('data-spread') == "false"){
    		//展开
    		$(elem).attr('data-spread','true');
	    	var parentid = $(elem).attr("data-parentid");
	    	var temp = elem;
	    	var i = 0;
	    	while($(temp).length){
	    		if($(temp).attr("data-parentid") == parentid&& i!=0){
	    			return;
	    		}

	    		if(i != 0){
	    			$(temp).hide();
	    		}

	    		temp = $(temp).next();
	    		i ++;
	    	}
    	}else{ 
    		$(elem).attr('data-spread','false');
    		$("tr[data-parentid="+id+"]").show();


    	}
	    
    };
    //如果没有子节点，则不执行
    if(item['child'].length == 0) return;
    $("tr[data-id="+id+"]").on('click', open);
  }


  Tree.prototype.on = function(){
    
  }

  //暴露接口
  exports('tabletree', function(options){
    var tree = new Tree(options = options || {});
    var elem = $(options.elem);
    if(!elem[0]){
      return hint.error('layui.tree 没有找到'+ options.elem +'元素');
    }
    tree.init(elem);
  });
});