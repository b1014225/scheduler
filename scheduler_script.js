$(function(){

  $('.put').click(function(){　//workのopenとclose
    var $none=$('.form_none');
    if($none.hasClass('open')){
      $none.removeClass('open');
      $none.slideUp();

    } else{
      $none.addClass('open');
      $none.slideDown();
    }
  });
});
