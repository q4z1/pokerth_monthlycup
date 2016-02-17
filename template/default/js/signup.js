$(window).load
(
	function()
	{
    if ($('button.validatesup').length > 0) {
      $('button.validatesup').each(function(i,item){
        $(item).click(function(event){
          validate_signup($(this).attr('__sup_id__'));
        });
      });
    }
  }
);

function validate_signup(id) {
  $.post(
    "/ajax/signup/validate",
    { id: id}
  ).done(function(data){
    showModal(data);
    $('button.validatesup[__sup_id__='+id+']').parent().parent().children().eq(4).text("Yes");
    $('button.validatesup[__sup_id__='+id+']').remove();
  });

}