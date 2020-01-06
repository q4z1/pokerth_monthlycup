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
		
		// footer: admins - links to pokerth
		var footer_nicks = $('#footer_txt p').html();
		var footer_nicks_a = $('#footer_txt p').text().split(',');
		$(footer_nicks_a).each(function(i, item){
		  item = item.trim();
		  footer_nicks = footer_nicks.replace(item, "<a title='"+item+"' target='_blank' href='https://www.pokerth.net/leaderboard/"+item+"'>"+item+"</a>");
		});
		$('#footer_txt p').html(footer_nicks);
		
		$("ul.nav li a[href^='#']").on('click', function(e) {
		
			 // prevent default anchor click behavior
			 e.preventDefault();
		
			 // store hash
			 var hash = this.hash;
			try{
				 // animate
				 $('html, body').animate({
						 scrollTop: $(hash).offset().top
					 }, 300, function(){
			
						 // when done, add hash to url
						 // (default click behaviour)
						 window.location.hash = hash;
					 });
			}catch(e){}
		
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
          try{
            if (window.requestIdleCallback) {
                requestIdleCallback(function () {
                    Fingerprint2.get(function (components) {
                      //console.log(components) // an array of components: {key: ..., value: ...}
                      var values = components.map(function (component) { return component.value })
                      var murmur = Fingerprint2.x64hash128(values.join(''), 31);
                      $.post(
                        $('form#signup').prop('action'),
                        {
                          playername: $('input#playername').val(),
                          fp: fp(),
                          fpnew: murmur,
                        }
                      ).done(function(data){showModal(data)});
                    })
                })
            } else {
                setTimeout(function () {
                    Fingerprint2.get(function (components) {
                      //console.log(components) // an array of components: {key: ..., value: ...}
                      var values = components.map(function (component) { return component.value })
                      var murmur = Fingerprint2.x64hash128(values.join(''), 31);
                      $.post(
                        $('form#signup').prop('action'),
                        {
                          playername: $('input#playername').val(),
                          fp: fp(),
                          fpnew: murmur,
                        }
                      ).done(function(data){showModal(data)});
                    })  
                }, 500)
            }
          }catch(e){}
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

function fp()
{
	var sFP = "";
	sFP+="Resolution:"+window.screen.availWidth+"x"+window.screen.availHeight+"\n";
	sFP+="ColorDepth:"+screen.colorDepth+"\n";
	sFP+="UserAgent:"+navigator.userAgent+"\n";    
	sFP+="Timezone:"+(new Date()).getTimezoneOffset()+"\n";
	sFP+="Language:"+(navigator.language || navigator.userLanguage)+"\n";
	document.cookie="sFP";
	if (typeof navigator.cookieEnabled != "undefined" 
		&& navigator.cookieEnabled == true
		&& document.cookie.indexOf("sFP") != -1)
	sFP+="Cookies:true\n";
	else
	sFP+="Cookies:false\n";
	sFP+="Plugins:"+jQuery.map(navigator.plugins, function(oElement) 
	{ 
	  return "\n"+oElement.name+"-"+oElement.version; 
	});
	return md5(sFP);
}