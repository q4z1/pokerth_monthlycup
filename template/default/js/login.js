/**
 * login
 *
 * Validierung des Formulars
 */
$(document).ready
(
	function()
	{
		if($('div.login').length > 0)
		{
			$('#signin').click
			(
				function(event)
				{
					if(typeof event !== "undefined" && event !== null)
					{
						if(event.preventDefault){ event.preventDefault(); }else{event.returnValue = false;}
						if(event.stopPropagation){event.stopPropagation();}else{event.cancelBubble = true;}
					}
					validate()
				}
			);
			$('#login_form').bind
			(
				'submit',
				function(event)
				{
					if(typeof event !== "undefined" && event !== null)
					{
						if(event.preventDefault){ event.preventDefault(); }else{event.returnValue = false;}
						if(event.stopPropagation){event.stopPropagation();}else{event.cancelBubble = true;}
					}
					validate();
				}
			);
		}
	}
);

/*
 * validiere Formular
 */
function validate()
{
	var state = true;
	var notice = "";
	/*
	 * Passwort lang genug?
	 */
	if($('#password').val().length < 4)
	{
		notice = 'Passwort muss mindestens 4 Zeichen lang sein!';
		state = false;
	}
	/*
	 * wenn ein Fehler aufgetreten ist, gib notice aus
	 */
	if(state === false)
	{
		$('#notice').html(notice);
		$('#notice').css('display', 'block');
		return;
	}
	else
	{
		$('#notice').html();
		$('#notice').css('display', 'none');
	}
	/*
	 * im Erfolgsfall Passwortverschlüsselung und submit
	 */
	//$('#passhash').val(hex_sha256($('#password').val()));
	$('#passhash').val(md5($('#password').val()));
	$('#password').attr('disabled', 'disabled');
	$('#login_form').unbind('submit');
	$('#login_form').submit();
	return;
}

/*
 * prüfe, ob email existiert
 */
function check_email(email)
{
	if(email.length == 0)
	{
		return;
	}
	var url = window.location.href;
	if(url.indexOf("main") == -1)
	{
		url += "main/";
	}
	url += "ajax/checkemail";
	$.post
	(
		url,
		{email: email},
		function(response)
		{
			if(response == 'false')
			{
				$('.notice').addClass('red');
				$('.notice').html('Die E-Mail Adresse ' + email + ' existiert nicht!');
				return false;
			}
			else
			{
				$('.notice').removeClass('red');
				$('.notice').empty();
				return true;
			}
		},
		dataType='html'
	);
}
