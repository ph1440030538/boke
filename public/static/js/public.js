function preview_img(obj){
	layer.photos({
		photos: {
			data:[{src:$(obj).parent().parent().find(".preview_img").val()}]
		}
		,anim: 5
	});
}