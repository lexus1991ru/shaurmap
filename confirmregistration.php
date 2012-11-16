<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <title>confirmregistration.php</title>
        <link rel="stylesheet" href="css/style.css">
		<link rel="stylesheet" href="bootstrap/css/bootstrap.css">
		<script src="js/jquery-1.8.2.js"></script>
		<style>
			body { background: none; }
			.inline-top {
				display: inline-block;
				vertical-align: top;
			}
			#confirm-registration-form {
				background: #fff;
				padding: 20px;
				margin: 50px;
			}
		</style>
    </head>
    <body>
		<div id="confirm-registration-form">
            <legend>confirmregistration.php</legend>
            <label>Имя</label>
            <input id="input-login" type="text" placeholder="Имя" onkeyup="ConfirmRegistration.ajaxCheckUser(this.value)">
            <label>E-mail</label>
            <input id="input-mail" type="email" placeholder="E-mail">
            <label>Ключ активации</label>
            <input id="input-key" type="text" placeholder="Ключ активации">
            <div>
            	<button class="btn btn-info" onclick="ConfirmRegistration.ajaxConfirmActivation()">Активировать</button>
            </div>
		</div>
       	<script src="bootstrap/js/bootstrap.min.js"></script>
       	<script src="js/confirmregistration.js"></script>
    </body>
</html>