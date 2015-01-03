$(document).ready(function() {

	// Confirm delete
	$("a.delete").on("click", function(){
		return confirm("Är du säker?");
	});

	$('.selectFreeMovie').on('click', function() {
		// Id for the selected movie
		var movieId = $(this).data('movie-id');

		// "Deselect" all movies 
		$('.selectFreeMovie').each(function() {
			$(this).css('border-color', '#ccc');
		});

		// Change the border color to a light green
		$(this).css('border-color', '#B3E05F');

		// Set the input field to the selected movie ID
		$('#freeMovieId').val(movieId);
	});

});