<? if(!defined('IN_MOOPHP')) exit('Access Denied');?>
<script>
function send(){
	var content=$("#push_content").val();
	$.post("command/command_base.php?mod=push&action=send", {
		content:content
	}, function(data) {
		alert(data);
	}, "html");
}
</script>
<div class="container">

<h3>发推送</h3>
<textarea class="form-control" style="width:100%" id='push_content'></textarea>
<button type="button" class="btn btn-default btn-sm" onclick='send()'>立即发送</button>

</div>