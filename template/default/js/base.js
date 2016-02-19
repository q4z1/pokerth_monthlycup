/**
 * base.js
 *
 */
var hideNoticeTime = 3000; // 3 seconds

$(window).load
(
	function()
	{
		$('#backtop').click(function(event){
			event.preventDefault();
			$('html,body').animate({scrollTop:0},'slow');
		});
		
		$("ul.nav li a[href^='#']").on('click', function(e) {
		
			 // prevent default anchor click behavior
			 e.preventDefault();
		
			 // store hash
			 var hash = this.hash;
		
			 // animate
			 $('html, body').animate({
					 scrollTop: $(hash).offset().top
				 }, 300, function(){
		
					 // when done, add hash to url
					 // (default click behaviour)
					 window.location.hash = hash;
				 });
		
		});
		
		if ( ($(window).height() + 100) < $(document).height() ) {
				$('#top-link-block').removeClass('hidden').affix({
						// how far to scroll down before link "slides" into view
						offset: {top:100}
				});
		}
		
		// short signup/registration handling because its public
		if ($('form#signup').length > 0) {
      $('button#submit').click(function(event){
				event.preventDefault();
				$.post(
					$('form#signup').prop('action'),
					{
						playername: $('input#playername').val()
					}
				).done(function(data){showModal(data)});
			});
    }
	}
);

function showModal(html){
	if ($('form#final').length > 0) {
    $('form#final').trigger('reset');
  }
	else if ($('form#firstround').length > 0) {
    $('form#firstround').trigger('reset');
  }
	else if ($('form#signup').length > 0) {
    $('form#signup').trigger('reset');
  }
	
	if ($('#amodal').length > 0) {
			$('#amodal').remove();
	}
	if ($('#modal').length > 0) {
			$('#modal').remove();
	}
	$('.modal-backdrop').remove();
	
	$("body").append(html);
	$('#amodal').modal();
	window.setTimeout(function(){
		$('#amodal').modal("hide");
		}, hideNoticeTime);
}