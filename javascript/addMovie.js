function addMovie() {
	var formData = $("#addMovieForm").serialize();
	$.ajax({
		type : "POST",
		url : "javascript/ajax.php?page=addmovie",
		cache : false,
		beforeSend : loading,
		data : formData,
		success : function(data) {
			$('body').removeClass('ui-loading');
			$(data).find('result').each(function() {
				var result = $(this).attr('code');
				if(result == 0) {
					alert("We've detected a duplicate for this movie with the same title. If you'd like to edit the details, please search for the movie. If you believe this is in error, please double check the movie title.");
				} else {
					$("#saveBtn").remove();
					$("#addMoviePage").html("Added movie to collection successfully.");
				}
			});
		},
		error : function() {alert("There was an error.");}
	});
}
