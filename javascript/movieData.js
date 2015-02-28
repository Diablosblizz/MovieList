function grabMovie(movietitle, year) {
	if(year == "" || year == 0) {
		$(document).ready(function() {
			$.ajax({
				type: "GET",
				url: "http://api.themoviedb.org/3/search/movie?query=" + movietitle + "&api_key=7389b73b4c2e1a9f63dfcda64f2bb1cf",
				async: false,
				contentType: 'application/json',
				dataType: 'jsonp',
				//beforeSend : loading,
				success: function(json) {
					var id = json.results[0].id;
					$.ajax({
						type: "GET",
						url: "http://api.themoviedb.org/3/movie/" + id + "?api_key=7389b73b4c2e1a9f63dfcda64f2bb1cf",
						async: false,
						contentType: 'application/json',
						dataType: 'jsonp',
						success: function(jsonm) {
							$('body').removeClass('ui-loading');
							var overview = jsonm.overview;
							var runtime = jsonm.runtime;
							var year = jsonm.release_date.substr(0, 4);
							var genres = "";
							for(var i = 0; i < jsonm.genres.length; i++) {
								if((jsonm.genres.length - i) != 1) {
									genres += jsonm.genres[i].name + ', ';
								} else {
									genres += jsonm.genres[i].name;
								}
							}
							$("#year").val(year);
							$("#displaytitle").val(movietitle);
							$("#summary").val(overview);
							$("#genre").val(genres);
							$("#runtime").val(runtime);
							$("#tmdbid").val(id);
							
							var compilePosterPath = "https://image.tmdb.org/t/p/w342" + jsonm.poster_path;
							$("#imagepath").val(compilePosterPath);
							
							$.ajax({
								type: "GET",
								url: "http://api.themoviedb.org/3/movie/" + id + "/credits?api_key=7389b73b4c2e1a9f63dfcda64f2bb1cf",
								async: false,
								contentType: 'application/json',
								dataType: 'jsonp',
								success: function(jsonc) {
									var actors = "";
									var amountAct = jsonc.cast.length;
									var loopAmount
									
									if(amountAct >= 5) {
										loopAmount = 5;
									} else {
										loopAmount = amountAct;
									}
									for(var i = 0; i < loopAmount; i++) {
										if(i < loopAmount-1) {
											actors += jsonc.cast[i].name + ', ';
										} else {
											actors += jsonc.cast[i].name;
										}
									}
									$("#actors").val(actors);
									
									var crews = jsonc.crew;
									var directors = "";
									var writers = "";
									
									crews.forEach(function(crew) {
										if(crew.department == "Directing") {
											directors += crew.name + ', ';
										}
										if(crew.department == "Writing") {
											writers += crew.name + ', ';
										}
									});
									directors = directors.replace(/,\s*$/, "");
									writers = writers.replace(/,\s*$/, "");
									$("#directors").val(directors);
									$("#writers").val(writers);
									alert("From the movie title provided, we've gathered the following information about the movie.\n\n" + movietitle + "\n" + year + "\n" + genres + "\n" + actors + "\n\nIf the information is incorrect, please change the year of the movie and press the button again. Please also check that the title of the movie is exactly the same compared to it's IMDb counterpart. For example, \"Wreck It Ralph\" will have different results compared to \"Wreck-It Ralph\".");
								}
							});
						}
					});
				}
			});
		});
	} else {
				$(document).ready(function() {
			$.ajax({
				type: "GET",
				url: "http://api.themoviedb.org/3/search/movie?query=" + movietitle + "&year=" + year + "&api_key=7389b73b4c2e1a9f63dfcda64f2bb1cf",
				async: false,
				contentType: 'application/json',
				dataType: 'jsonp',
				//beforeSend : loading,
				success: function(json) {
					var id = json.results[0].id;
					$.ajax({
						type: "GET",
						url: "http://api.themoviedb.org/3/movie/" + id + "?api_key=7389b73b4c2e1a9f63dfcda64f2bb1cf",
						async: false,
						contentType: 'application/json',
						dataType: 'jsonp',
						success: function(jsonm) {
							$('body').removeClass('ui-loading');
							var overview = jsonm.overview;
							var runtime = jsonm.runtime;
							var year = jsonm.release_date.substr(0, 4);
							var genres = "";
							for(var i = 0; i < jsonm.genres.length; i++) {
								if((jsonm.genres.length - i) != 1) {
									genres += jsonm.genres[i].name + ', ';
								} else {
									genres += jsonm.genres[i].name;
								}
							}
							$("#year").val(year);
							$("#displaytitle").val(movietitle);
							$("#summary").val(overview);
							$("#genre").val(genres);
							$("#runtime").val(runtime);
							$("#tmdbid").val(id);
							
							var compilePosterPath = "https://image.tmdb.org/t/p/w342" + jsonm.poster_path;
							$("#imagepath").val(compilePosterPath);
							$.ajax({
								type: "GET",
								url: "http://api.themoviedb.org/3/movie/" + id + "/credits?api_key=7389b73b4c2e1a9f63dfcda64f2bb1cf",
								async: false,
								contentType: 'application/json',
								dataType: 'jsonp',
								success: function(jsonc) {
									var actors = "";
									for(var i = 0; i < 5; i++) {
										if(i < 4) {
											actors += jsonc.cast[i].name + ', ';
										} else {
											actors += jsonc.cast[i].name;
										}
									}
									$("#actors").val(actors);
									
									var crews = jsonc.crew;
									var directors = "";
									var writers = "";
									
									crews.forEach(function(crew) {
										if(crew.department == "Directing") {
											directors += crew.name + ', ';
										}
										if(crew.department == "Writing") {
											writers += crew.name + ', ';
										}
									});
									directors = directors.replace(/,\s*$/, "");
									writers = writers.replace(/,\s*$/, "");
									$("#directors").val(directors);
									$("#writers").val(writers);
									alert("We've re-run the query with the year provided. Please ensure the metadata is correct.");
								}
							});
						}
					});
				}
			});
		});
	}
}

/*function grabMovie(movietitle, year) {
	if(year == "" || year == 0) {
	$(document).ready(function() {
		$.ajax({
			type: "GET",
			url: "http://www.omdbapi.com/?t=" + movietitle + "&r=xml",
			dataType: "xml",
			success: function(xml) {
				$(xml).find('movie').each(function() {
					var year = $(this).attr('year');
					var genre = $(this).attr('genre');
					var actors = $(this).attr('actors');
					var summary = $(this).attr('plot');
					var writers = $(this).attr('writer');
					var runtime = $(this).attr('runtime');
					var director = $(this).attr('director');
                    $("#displaytitle").val($("#movietitle").val());
					$("#year").val(year);
					$("#genre").val(genre);
					$("#actors").val(actors);
					$("#summary").val(summary);
					$("#runtime").val(runtime);
					$("#writers").val(writers);
					$("#directors").val(director);
					alert("From the movie title provided, we've gathered the following information about the movie.\n\n" + movietitle + "\n" + year + "\n" + genre + "\n\n If the information is incorrect, please change the year of the movie below and press the button again. Please also check that the title of the movie is exactly the same compared to it's IMDb counterpart. For example, \"Wreck It Ralph\" will have different results compared to \"Wreck-It Ralph\".");
				});
			}
		});
	});
	} else {
		$(document).ready(function() {
		$.ajax({
			type: "GET",
			url: "http://www.omdbapi.com/?t=" + movietitle + "&y=" + year + "&r=xml",
			dataType: "xml",
			success: function(xml) {
				$(xml).find('movie').each(function() {
					var year = $(this).attr('year');
					var genre = $(this).attr('genre');
					var actors = $(this).attr('actors');
					var summary = $(this).attr('plot');
					var writers = $(this).attr('writer');
					var runtime = $(this).attr('runtime');
					var director = $(this).attr('director');
                    $("#displaytitle").val($("#movietitle").val());
					$("#year").val(year);
					$("#genre").val(genre);
					$("#actors").val(actors);
					$("#summary").val(summary);
					$("#runtime").val(runtime);
					$("#writers").val(writers);
					$("#directors").val(director);
					alert("Please ensure the metadata is correct.");
				});
			}
		});
	});	
	}
}*/