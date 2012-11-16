<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <title>addcomment.php</title>
        <link rel="stylesheet" href="css/style.css">
		<link rel="stylesheet" href="bootstrap/css/bootstrap.css">
		<script src="js/jquery-1.8.2.js"></script>
		<style>
			body { background: none; }
			.inline-top {
				display: inline-block;
				vertical-align: top;
			}
			#add-comment-form {
				background: #fff;
				padding: 20px;
				margin: 50px;
			}
		</style>
    </head>
    <body>
		<div id="add-comment-form" class="inline-top">
			<legend>addcomment.php</legend>
			<label>Market ID</label>
		    <input id="comment-marketid" type="text" placeholder="marketid">
		    <label>User ID</label>
		    <input id="comment-userid" type="text" placeholder="userid">
		    <label>Оценка</label>
		    <input id="comment-mark" type="text" placeholder="Оценка">
		    <label>Комментарий</label>
			<textarea id="comment-text" class="span6" rows="3"></textarea>
			<label>Token</label>
		    <input id="comment-token" class="span6" type="text" placeholder="token">
			<div>
            	<button id="comment-send-button" class="btn btn-info" onclick="AddComment.ajaxSendComment()">Отправить</button>
            </div>
        </div>
       	<script src="bootstrap/js/bootstrap.min.js"></script>
       	<script src="js/addcomment.js"></script>
    </body>
</html>