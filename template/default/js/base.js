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
		

	}
);


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


