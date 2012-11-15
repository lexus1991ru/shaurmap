function ajaxLoadComments(marketid, start, count, token, userid) {
	var query =
		'marketid=' + marketid + '&' +
		'start=' + start + '&' +
		'count=' + count + '&' +
		'token=' + token + '&' +
		'userid=' + userid;
	
	$.ajax({
		url : '../php/comments.php',
		type : 'POST',
		data : query,
		success : function(data) {
			console.log(data);
		},
		error : function(e) {
			console.log(e.message);
		}
	}); 
}


