$(function() {
  'use strict';
  // Make opacity 1 for navbar when you make scroll
  /*$(window).scroll(function () {
    if($(window).scrollTop() >= 200) {
      $('.navbar').css({'opacity': 1})
    } else {
      $('.navbar').css({
        'opacity': .3,
        'borderBottom': '1px solid #CCC'

      })
    }
  });*/


// CountUp when you load the page
// $(window).on('load', function () {
//   var options = {
//   useEasing: true,
//   useGrouping: true,
//   separator: ',',
//   decimal: '.',
// };
// CountUp for every number
// var countingUp = new CountUp('number', 0, 6, 0, 2, options);
// countingUp.start();
//  var countingUp = new CountUp('number2', 0, 17, 0, 2, options);
//  countingUp.start();
// var countingUp = new CountUp('number3', 0, 15, 0, 2, options);
//  countingUp.start();
// var countingUp = new CountUp('number4', 0, 1007, 0, 2, options);
//  countingUp.start();
// });


// Filtering projects (our Projects section)
// $('.grid').isotope({
//
//   itemSelector: '.col-md-4',
//   layoutMode: 'fitRows'
// });
// $('.our-project ul li').click(function () {
//
//   $('.our-project ul li').removeClass('active');
//   $(this).addClass('active');
//
//   var selector = $(this).attr('data-filter');
//   $('.grid').isotope({
//     filter: selector
//   });
//   return false;
//
// });


  $('.top5 .first img').on('mouseenter' , function () {
    $('.top5 .first .popup').fadeIn(300, function () {
      $(this).css('display', 'flex');
    });
  });
  $('.top5 .first img').on('mouseleave' , function () {
    $('.top5 .first .popup').fadeOut(400);
  });



// scroll to top button
  $(window).scroll(function () {

    if ($(window).scrollTop() >= 700) {

      $('.scroll-top').fadeIn(500);
    } else {
      $('.scroll-top').fadeOut(500)
    }
  })

  $('.scroll-top').click(function () {
    $('html, body').animate({
      scrollTop: 0
    }, 700)
  })








// Count up when you scroll
/*$(window).scroll(function () {
  if ($(window).scrollTop() >= 1000) {
    var countingUp = new CountUp('number', 0, 6, 0, 2);
    countingUp.start();
     var countingUp = new CountUp('number2', 0, 17, 0, 2);
     countingUp.start();
    var countingUp = new CountUp('number3', 0, 15, 0, 2);
     countingUp.start();
    var countingUp = new CountUp('number4', 0, 1007, 0, 2);
     countingUp.start();
  }
})*/





// filtering projects
/*   $('.portfolio .our-project li').click(function () {
     $(this).addClass('active').siblings('li').removeClass('active');
   Make it disappear
  $('.our-project .img').css(
    'display', 'none');

  $($(this).data('show')).fadeIn(1000);

  //By Opacit way

  if ($(this).data('show') === '.all') {

    $('.our-project .img').css('opacity', 1);

  } else {

       $('.our-project .img').css('opacity', 0.06);
       $($(this).data('show')).parent().find('.img').css('opacity', 1);

     }

 });*/



});
