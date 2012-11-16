var AddComment = (function() {
	function ajaxSendComment() {
		var marketid = $('#comment-marketid').val();
		var userid = $('#comment-userid').val();
		var mark = $('#comment-mark').val();
		var text = $('#comment-text').val();
		var token = $('#comment-token').val();
		
		var query =
			'req=postcomment' + 
			'&marketid=' + marketid +
			'&userid=' + userid +
			'&mark=' + mark + 
			'&text=' + text + 
			'&token=' + token;
			
        $.ajax({
            url: 'php/comments.php',
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
		ajaxSendComment: ajaxSendComment
	}
	
})();
