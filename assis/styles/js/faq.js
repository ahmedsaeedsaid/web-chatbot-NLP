/*
$(document).ready(function()

{

	$('body').on("click",".show-answer",function()

	{

		

		if($(this).hasClass('glyphicon-plus')){

			$('.show-answer').removeClass('glyphicon-minus').addClass('glyphicon-plus');

			$(this).removeClass('glyphicon-plus').addClass('glyphicon-minus');

			if($('.active_div').length > 0)

			{

				$('.active_div').addClass('hidden-lg').addClass('hidden-md').addClass('hidden-sm').addClass('hidden-xs');

				$(this).parents('.row').find('.hide_div').addClass('active_div').removeClass('hidden-lg').removeClass('hidden-md').removeClass('hidden-sm').removeClass('hidden-xs');

			}

			else

			{

				$('.active_div').removeClass('active_div');	

				$(this).parents('.row').find('.hide_div').addClass('active_div').removeClass('hidden-lg').removeClass('hidden-md').removeClass('hidden-sm').removeClass('hidden-xs');

			}



		}

		else

		{

			$(this).removeClass('glyphicon-minus').addClass('glyphicon-plus');

			$('.active_div').addClass('hidden-lg').addClass('hidden-md').addClass('hidden-sm').addClass('hidden-xs');

		}

	});







});*/