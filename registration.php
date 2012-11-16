<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <title>registration.php</title>
        <link rel="stylesheet" href="css/style.css">
		<link rel="stylesheet" href="bootstrap/css/bootstrap.css">
		<script src="js/jquery-1.8.2.js"></script>
		<style>
			body { background: none; }
			.inline-top {
				display: inline-block;
				vertical-align: top;
			}
			#registration-form {
				background: #fff;
				padding: 20px;
				margin: 50px;
			}
		</style>
    </head>
    <body>
		<div id="registration-form" class="form-horizontal inline-top">
            <legend>registration.php</legend>                
			<div id="mail-control" class="control-group">
			    <label class="control-label" for="input-mail">E-mail</label>
			    <div class="controls">
			        <input type="email" placeholder="E-mail" onkeyup="Registration.regExpCheckMail(this.value)">
			        <span class="help-inline"></span>
			    </div>
			</div>
			<div id="pass1-control" class="control-group">
			    <label class="control-label" for="input-pass1">Пароль</label>
			    <div class="controls">
			        <input type="password" placeholder="Пароль" onkeyup="Registration.regExpCheckPass()">
			        <span class="help-inline"></span>
			    </div>
			</div>
			<div id="pass2-control" class="control-group">
			    <label class="control-label" for="input-pass2">Пароль еще раз</label>
			    <div class="controls">
			        <input type="password" placeholder="Повторите пароль" onkeyup="Registration.regExpCheckPass()">
			        <span class="help-inline"></span>
			    </div>
			</div>
            <button disabled id="sign-up-button" class="btn btn-info" onclick="Registration.ajaxSignUp()">Зарегистрироваться</button>
        </div>
       	<script src="bootstrap/js/bootstrap.min.js"></script>
       	<script src="js/registration.js"></script>
    </body>
</html>