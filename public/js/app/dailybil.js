function preview()
{
	$.post('/dailybil/preview',
		$('#dailybil').serialize(),
		function(msg){
			$('#preview').html(msg);
		});
	return false;
}