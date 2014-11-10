<?php
if(!empty($this->data['htmlinject']['htmlContentPost'])) {
	foreach($this->data['htmlinject']['htmlContentPost'] AS $c) {
		echo $c;
	}
}
?>
	</div><!-- #content -->


</div><!-- #wrap -->
<!--<script src="//ajax.googleapis.com/ajax/libs/jquery/2.1.1/jquery.min.js"></script>
<script type="text/javascript">

$(function () {


	$('<div></div>', {
		class: 'social-block'
	}).insertAfter('.social-connect h2');


	$('<div></div>', {
		class: 'lms-block'
	}).insertAfter('.social-block');

	$('<h4></h4>', {
		class: 'block-title',
		text: 'Social network account'
	}).insertBefore('.social-block');

	$('<h4></h4>', {
		class: 'block-title',
		text: 'LMS account'
	}).insertAfter('.social-block');

	$(function () {
		$('#button-facebook').appendTo('.social-block');
		$('#button-twitter').appendTo('.social-block');
		$('#button-linkedin').appendTo('.social-block');
		$('#button-pedanet').appendTo('.lms-block');
		$('#button-educloud-sp').appendTo('.lms-block');


	});

	$('.social-buttons').remove();


})();
</script>-->
</body>
</html>
