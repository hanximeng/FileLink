<!DOCTYPE html>
<html lang="zh-cn">
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>文件上传Github并获取外链</title>
	<meta http-equiv="Cache-Control" content="no-siteapp"/>
	<link rel="shortcut icon" href="https://q1.qlogo.cn/g?b=qq&nk=1017959770&s=640" type="image/x-icon"/>
	<!-- zui -->
	<link href="https://lib.baomitu.com/zui/1.9.1/css/zui.min.css" rel="stylesheet">
</head>
<script type="text/javascript">
function F_Open_dialog(){ 
	$("#btn_file").click(); 
}
</script>
<body>
<br>
<div class="container">
	<div class="panel">
		<div class="panel-body">
			<input type="file" id="btn_file" style="display:none" multiple onchange="chose(event);">
			<div class="btn-group">
				<button class="btn btn-primary" type="button" onclick="F_Open_dialog()">选择文件或拖拽</button>
				<button class="btn" type="button" onclick="postData(0)">上传已选中文件</button>
			</div>
		</div>
	</div>
	<div class="cards underBtn"></div>
</div>
<script src="https://cdn.bootcss.com/jquery/3.5.0/jquery.min.js"></script>
<script src="https://cdn.bootcss.com/zui/1.9.1/js/zui.min.js"></script>
<script src="https://cdn.bootcss.com/clipboard.js/2.0.6/clipboard.min.js"></script>
<script>
	var data64 = [];
	var filenames = [];
    window.addEventListener("dragenter", function(event) { event.preventDefault(); }, false);
    window.addEventListener("dragover", function(event) { event.preventDefault(); }, false);
    window.addEventListener("drop", function(event) {
		for(var i=0;i<event.dataTransfer.files.length;i++) {
			var reader = new FileReader();
			reader.onload = function(e) {
				data64.push(e.target.result);
			};
			reader.readAsDataURL(event.dataTransfer.files[i]);
			filenames.push(event.dataTransfer.files[i].name);
			event.preventDefault();
			$(".cards").append("<div id=\"cardDiv" + filenames.length.toString() + "\" class=\"col-md-4 col-sm-6 col-lg-3\"><div class=\"panel\"><div class=\"panel-body\"><strong>" + event.dataTransfer.files[i].name + "</strong><div class=\"progress progress-striped active\"> <div id=\"progress" + filenames.length.toString() + "\" class=\"progress-bar progress-bar-success\" role=\"progressbar\" aria-valuenow=\"0\" aria-valuemin=\"0\" aria-valuemax=\"100\" style=\"width: 0%\"><span class=\"sr-only\"> </span></div></div><button type=\"button\" id=\"" + filenames.length.toString() + "\" class=\"btn btn-danger\" onclick=\"deleteFile(this)\"><i class=\"icon icon-times\"></i></button></div></div></div>");

    new $.zui.Messager('全部导入后需点击上传按钮才能上传喔', {
        type: 'success',
        icon: 'bell',
        placement: 'center'
    }).show();
		}
        document.body.classList.remove('empty');
    }, false);
	function chose(event) {
		var k = event.target.files.length;
		for(var i=0; i<k; i++) {
			var reader = new FileReader();
			reader.onload = function(e) {
				data64.push(e.target.result);
			};
			reader.readAsDataURL(event.target.files[i]);
			filenames.push(event.target.files[i].name);
			$(".cards").append("<div id=\"cardDiv" + filenames.length.toString() + "\" class=\"col-md-4 col-sm-6 col-lg-3\"><div class=\"panel\"><div class=\"panel-body\"><strong>" + event.target.files[i].name + "</strong><div class=\"progress progress-striped active\"> <div id=\"progress" + filenames.length.toString() + "\" class=\"progress-bar progress-bar-success\" role=\"progressbar\" aria-valuenow=\"0\" aria-valuemin=\"0\" aria-valuemax=\"100\" style=\"width: 0%\"><span class=\"sr-only\"> </span></div></div><button type=\"button\" id=\"" + filenames.length.toString() + "\" class=\"btn btn-danger\" onclick=\"deleteFile(this)\"><i class=\"icon icon-times\"></i></button></div></div></div>");
			
    new $.zui.Messager('全部导入后需点击上传按钮才能上传喔', {
        type: 'success',
        placement: 'center',
        icon: 'bell'
    }).show();
			
			event.preventDefault();
		}
	}
	function deleteFile(obj) {
		deleteId = parseInt(obj.getAttribute("id"));
		data64.splice(deleteId - 1, 1);
		filenames.splice(deleteId - 1, 1);
		$("#cardDiv" + deleteId.toString())[0].parentNode.removeChild($("#cardDiv" + deleteId.toString())[0]);
		for(var i=deleteId+1; i<=data64.length+1; i++)
		{
			$("#cardDiv" + i.toString()).attr('id', 'cardDiv'+(i-1).toString());
			$("#" + i.toString()).attr('id', (i-1).toString());
			$("#progress" + i.toString()).attr('id', 'progress' + (i - 1).toString());
		}
	}
	function postData(i) {
		$.ajax({
			url:"api.php",
			type: 'post',
			async: true,
			dataType: 'text',
			jsonp: 'callback',
			scriptCharset: 'utf-8',
			contentType: "application/json",
			jsonpCallback: "jsonpCallback",
			data: filenames[i] + " " + data64[i],
			xhr: function() {
				myXhr = $.ajaxSettings.xhr();
				if(myXhr.upload){
					myXhr.upload.addEventListener('progress',function(e){                            
						var loaded = e.loaded;
						var total = e.total;
						var percent = Math.floor(100*loaded/total)+"%";
						$("#progress" + (i+1).toString()).css("width",percent);                                                                
					}, false);
				}
				return myXhr;
			},
			success: function(data) {
                                console.log(data);
				$("#" + (i+1).toString())[0].parentNode.innerHTML+=("<button type=\"button\" data-clipboard-text=\"" + data.split(' ')[0] + "\" class=\"btn btn-success\"><i class=\"icon icon-copy\"></i>CDN</button><button type=\"button\" data-clipboard-text=\"" + data.split(' ')[1] + "\" class=\"btn btn-success\"><i class=\"icon icon-copy\"></i>Github</button>");
if(i<filenames.length - 1) {
           postData(i+1);
}
			},
			error:function(err) {
				console.log(err);
if(i<filenames.length - 1) {
           postData(i+1);
}
			}
		});
	}

				var clipboard = new ClipboardJS('button');
clipboard.on('success',function(e){
  e.clearSelection();
    new $.zui.Messager('复制成功 (￣▽￣)"', {
        time: 1000,
        type: 'success',
        placement: 'center',
        icon: 'bell'
    }).show();
 });
clipboard.on('error',function(e){
  e.clearSelection();
  console.log('复制失败');
    new $.zui.Messager('复制失败 (●ˇ∀ˇ●)', {
        time: 1000,
        type: 'danger',
        placement: 'center',
        icon: 'bell'
    }).show();
 });
</script>
</body>
</html>