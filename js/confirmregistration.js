var ConfirmRegistration = (function() {
	function ajaxCheckUser(username) {
		var query = 'req=checkuser&username=' + username;
		
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
	
	function ajaxConfirmActivation() {
		var mail = document.querySelector('#input-mail').value;
		var key = document.querySelector('#input-key').value;
		var login = document.querySelector('#input-login').value;
		
		var query = 'req=confirm&mail=' + mail + '&key=' + key + '&login=' + login;
		console.log(query);
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
	
	return {
		ajaxCheckUser: ajaxCheckUser,
		ajaxConfirmActivation: ajaxConfirmActivation
	}
})();
