<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <title>AJAX</title>
		<link rel="stylesheet" href="bootstrap/css/bootstrap.css">
		<script src="js/jquery-1.8.2.js"></script>
		<style>
			#wrapper {
				text-align: center;
			}
			.inline-top {
				display: inline-block;
				vertical-align: top;
			}
		</style>
    </head>
    <body>
		<div id="wrapper">
			<h3>AJAX request test page</h3>
			<div class="input-prepend input-append">
				<span class="add-on">URL</span>
				<input id="page-url" class="span8" type="url" value="http://" placeholder="URL" />
				<span class="add-on">URL</span>
			</div>
			<div id="kv-list">
				<div class="key-val">
					<div class="input-prepend inline-top">
						<span class="add-on">KEY</span>
						<input class="key span3" type="text" placeholder="key" />
					</div>
					<div class="input-prepend inline-top">
						<span class="add-on">VAL</span>
						<input class="value span3" type="text" placeholder="value" />
					</div>
					<div class="btn-group">
						<div class="add-btn btn btn-info"><i class="icon-plus icon-white"></i> Add</div>
						<div class="remove-btn btn btn-info"><i class="icon-trash icon-white"></i> Remove</div>
					</div>
				</div>
			</div>
			<br>
			<div class="btn-send-request btn btn-large btn-info">Send AJAX request</div>
			<h3>Debug info</h3>
			<div>
				<textarea id="debug" rows="10" class="span9"></textarea>
			</div>
		</div>
       	<script src="bootstrap/js/bootstrap.min.js"></script>
		<script type="text/javascript">
			$(function() {
				var debug = $('#debug');
				var keyVal = $('.key-val:first-child');
				
				$('.add-btn').live('click', function() {
					var kv = keyVal.clone().removeAttr('id');
					kv.find('input').val('');
					$(this).closest('.key-val').after(kv);
				});
				
				$('.remove-btn').live('click', function() {
					$(this).closest('.key-val').remove();
				});
				
				$('.btn-send-request').click(function() {
					var params = getRequestParams();
					console.log(params);
				});
				
				var getRequestParams = function() {
					var params = { url: '', key: [], value: [] };
					
					params.url = $('#page-url').val();
					
					$('input.key').each(function() {
						params.key.push($(this).val());
					});
					
					$('input.value').each(function() {
						params.value.push($(this).val());
					});
					
					return params;
				};
			});
		</script>
    </body>
</html>