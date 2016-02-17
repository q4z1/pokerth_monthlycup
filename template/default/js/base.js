/**
 * base.js
 *
 */
var logoutTimeout = null;
var logoutPeriodVal = 300000; // 5 minutes
var noticeInterval = null;
var noticePeriodVal = 15000 // 15 seconds
var hideNoticeTime = 3000; // 3 seconds
var olPeriod = null;
$(window).load
(
	function()
	{
		fetch_notice();
		if ($('.login').length == 0)
		{
			noticeInterval = setInterval("fetch_notice()", noticePeriodVal);
		}
		
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
		
		if ($('.removesup').length > 0) {
      $('.removesup').each(function(i,item){
        $(item).click(function(event){
          delete_signup($(this).attr('__sup_id__'));
        });
      });
    }
	}
);

function delete_signup(id) {
	if ($('#modal').length > 0) {
    	$('#modal').remove();
      $('.modal-backdrop').remove();
  }
	var modal = "<div id='modal' class='modal fade' role='dialog'>"
		+ "<div class='modal-dialog modal-lg'><div class='modal-content'>"
		+ "<div class='modal-body'>"
		+ "Are you sure to delete singup id " + id + "</div><div class='modal-footer'>"
		+ "<button type='button' class='btn btn-danger'"
		+ " data-dismiss='modal' id='cancel'>Cancel</button>"
		+ "<button type='button' class='btn btn-success'"
		+ " data-dismiss='modal' id='remove'>Delete</button>"
		+ "</div></div></div>";
		$(".middle").append(modal);
		$('#modal').modal();
		
		$('button#cancel').click(function(){
			$('button#remove').unbind('click');
		});
		
		$('button#remove').click(function(){
			$('button#cancel').unbind('click');
			$.post(
				"/ajax/signup/delete",
				{ id: id}
			).done(function(data){
        showModal(data);
        $('button[__sup_id__='+id+']').parent().parent().remove();
      });
		});
}

function fetch_notice()
{
	$('#notice').html();
	$.get('/ajax/notice', function(data){render_notice(data);});
}

function render_notice(notice)
{
	/*
	if (notice == 'none')
	{
		$('#notice').css('display', 'none');
		$('#notice').html();
	}
	else if (notice == "stop")
	{
		$('#notice').css('display', 'none');
		$('#notice').html();
		// clear all timeouts and Intervals
		clearTimeout(logoutTimeout);
		//clearInterval(noticeInterval);
	}
	else
	{
		$('#notice').html(notice);
		$('#notice').css('display', 'block');
		setTimeout("hide_notice()", hideNoticeTime); // 3 Sekunde
	}
	*/
}

function hide_notice()
{
	$('#notice').css('display', 'none');
	$('#notice').html();
}


function reload_page()
{
	window.location.reload();
}

/**
 * automatical logout()
 */
function logout()
{
	window.open("/main/logout", "_self");
}

function clearLogoutTimer()
{
	clearTimeout(logoutTimeout);
	logoutTimeout = setTimeout("logout()", logoutPeriodVal);
}


function processPing(data)
{
	if (data == "stop")
	{
		clearInterval(sessionInterval);
		clearTimeout(logoutTimeout);
		clearInterval(olPeriod);
	}
	else if (data == "admin")
	{
		clearTimeout(logoutTimeout);
	}
}

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