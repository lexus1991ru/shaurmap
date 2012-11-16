var Registration = (function() {
	function ajaxCheckMail(mail) {
		var query = 'req=checkmail&mail=' + mail;
		
		notify('mail', 'loading');
					
        $.ajax({
            url: 'php/register.php',
            type: 'POST',
            data: query,
            success: function(data) {
                var response = JSON.parse(data);
				if(response) {
					if(response.status == 0)
						notify('mail', 'success');
					if(response.status == 3)
						notify('mail', 'error', response.data);
					if(response.status == 4)
						notify('mail', 'error', response.data);
				}
				console.log(response);
            },
            error: function(e) {
                console.log(e.message);
                notify('mail', 'error', e.message);
            }
        });
	}
	
	function ajaxSignUp() {
		var mail = $('#mail-control input').val();
		var pass1 = $('#pass1-control input').val();
		var pass2 = $('#pass2-control input').val();
		
		var query = 'req=register&mail=' + mail + '&pass1=' + pass1 + '&pass2=' + pass2;
		
        $.ajax({
            url: 'php/register.php',
            type: 'POST',
            data: query,
            success: function(data) {
                console.log(data);
            },
            error: function(e) {
                console.log(e.message);
            }
        });
	}
	
	function regExpCheckMail(mail) {
		var mailRegExp = new RegExp('^[_A-Za-z0-9-\\+]+(\\.[_A-Za-z0-9-]+)*@[A-Za-z0-9-]+(\\.[A-Za-z0-9]+)*(\\.[A-Za-z]{2,})$');
		
		if(mailRegExp.test(mail))
			ajaxCheckMail(mail);
		else
			notify('mail', 'error', 'E-mail введен некорректно');

		if(mail == '')
			notify('mail', 'reset');
	}
	
	function regExpCheckPass() {
		var passRegExp = new RegExp('.{6,32}');
		var pass1 = $('#pass1-control input').val();
		var pass2 = $('#pass2-control input').val();
		
		if(passRegExp.test(pass1)) {
			notify('pass1', 'success');
			
			if(pass1 == pass2)
				notify('pass2', 'success');
			else
				notify('pass2', 'error', 'Пароли не совпадают')
		}
		else
			notify('pass1', 'error', 'Длина пароля ограничена от 6 до 32 символов');
			
		if(pass1 == '')
			notify('pass1', 'reset');
		
		if(pass2 == '')
			notify('pass2', 'reset');
	}
	
	function notify(selector, type, message) {
		var field = $('#' + selector + '-control');
		var input = field.find('input');
		
		field.removeClass('info error success');
		
		switch(type) {
			case 'success':
				field.addClass('success');
				field.find('.help-inline').html('<i class="icon-ok icon-green"></i> ');
				break;
			case 'error':		
				field.addClass('error');
				field.find('.help-inline').html('<i class="icon-remove icon-red"></i> ' + message);
				break;
			case 'loading':
				field.addClass('info');
				field.find('.help-inline').html('<i class="icon-ajax-loader"></i>');
				break;
			case 'reset':
				field.find('.help-inline').empty();
				break;
		}
		
		checkFormButton();
	}
	
	function checkFormButton() {
		if($('#mail-control').hasClass('success') && $('#pass1-control').hasClass('success') && $('#pass2-control').hasClass('success'))
			$('#sign-up-button').removeAttr('disabled');
		else
			$('#sign-up-button').attr('disabled', 'disabled');
	}
	
	return {
		regExpCheckMail: regExpCheckMail,
		regExpCheckPass: regExpCheckPass,
		ajaxSignUp: ajaxSignUp
	}
})();
