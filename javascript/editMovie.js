function buildEdit(id) {
	// this function will build the new UI for editing the film
	$("#movieinfo").html("");
	// blank the info currently on the screen
	$("#edit-btn").remove();
	// remove edit button

	var saveBtn = $(document.createElement("a")).attr("href", "javascript: void(0)").attr("data-role", "button").attr("data-icon", "check").attr("class", "ui-btn-right").attr("onclick", "javascript: saveEdit(" + id + ");").attr("id", "saveBtn").text("Save");
	var saveBtn2 = $(document.createElement("a")).attr("href", "javascript: void(0)").attr("data-role", "button").attr("data-icon", "check").attr("class", "ui-btn-right").attr("onclick", "javascript: saveEdit(" + id + ");").attr("id", "saveBtn").text("Save");

	$("#movieheader").append(saveBtn).append(saveBtn2).trigger('create');

	var titleText = "";
	var year;
	var genre;
	var actors;
	var media;
	var summary;

	// create the div that will hold the remove flipswitch
	var removeFlipField = $(document.createElement("div")).attr("data-role", "fieldcontain");
	
	// create the label for the flipswitch
	var removeFlipLabel = $(document.createElement("label")).attr("for", "removeFlip").text("Remove:");
	
	// create the select input
	var removeFlipSelect = $(document.createElement("select")).attr("name", "removeFlip").attr("id", "removeFlip").attr("data-role", "flipswitch").attr("data-theme", "b").attr("data-mini", "true");
	
	// create the two options
	var removeFlipOptOn = $(document.createElement("option")).attr("value", "on").text("Yes")
	var removeFlipOptOff = $(document.createElement("option")).attr("value", "off").text("No");
	
	removeFlipSelect.append(removeFlipOptOff).append(removeFlipOptOn);
	
	// merge all into one
	var removeFlipAll = removeFlipField.append(removeFlipLabel).append(removeFlipSelect);


	// create the div that will hold movie title
	var movieTitleField = $(document.createElement("div")).attr("data-role", "fieldcontain");

	// create label for movie title
	var movieTitleLabel = $(document.createElement("label")).attr("for", "movietitle").text("Movie Title:");

	// create input for movie title
	var movieTitle = $(document.createElement("input")).attr("type", "text").attr("name", "movietitle").attr("id", "movietitle").attr("placeholder", "Movie Title").attr("text", titleText);

	// merge all title elements into one
	var movieAll = movieTitleField.append(movieTitleLabel).append(movieTitle);

	// create div that will hold year
	var yearField = $(document.createElement("div")).attr("data-role", "fieldcontain");

	// create the label for year
	var yearLabel = $(document.createElement("label")).attr("for", "year").text("Year of Release:");

	// create input for year
	var yearInput = $(document.createElement("input")).attr("type", "text").attr("name", "year").attr("id", "year").attr("placeholder", "Year of Release");

	// merge all year elements into one
	var yearAll = yearField.append(yearLabel).append(yearInput);

	// create div that will hold genre
	var genreField = $(document.createElement("div")).attr("data-role", "fieldcontain");

	// create genre label
	var genreLabel = $(document.createElement("label")).attr("for", "genre").text("Genre:");

	// create genre input
	var genreInput = $(document.createElement("input")).attr("type", "text").attr("name", "genre").attr("id", "genre").attr("placeholder", "Genre");

	// merge all genre into one
	var genreAll = genreField.append(genreLabel).append(genreInput);

	// create div that will hold actors
	var actorField = $(document.createElement("div")).attr("data-role", "fieldcontain");

	// create actor label
	var actorLabel = $(document.createElement("label")).attr("for", "actor").text("Actor(s):");

	// create actor input
	var actorInput = $(document.createElement("input")).attr("type", "text").attr("name", "actors").attr("id", "actors").attr("placeholder", "Actor(s)");

	// merge all actor into one
	var actorAll = actorField.append(actorLabel).append(actorInput);

	// create div that will hold summary
	var summaryField = $(document.createElement("div")).attr("data-role", "fieldcontain");

	// create label for summary
	var summaryLabel = $(document.createElement("label")).attr("for", "summary").text("Plot:");

	var summaryText = $(document.createElement("textarea")).attr("rows", "5").attr("id", "summary").attr("name", "summary");

	var summaryAll = summaryField.append(summaryLabel).append(summaryText);

	// create div that will hold media
	var mediaField = $(document.createElement("div")).attr("data-role", "fieldcontain");

	// create label for media
	var mediaLabel = $(document.createElement("label")).attr("for", "media").text("Media:");

	// create select element
	var mediaSelect = $(document.createElement("select")).attr("name", "media").attr("id", "media");

	// create the options
	var optDVD = $(document.createElement("option")).attr("value", "DVD").text("DVD");
	var optBlu = $(document.createElement("option")).attr("value", "Blu-ray").text("Blu-ray");
	var optDVDBlu = $(document.createElement("option")).attr("value", "DVD / Blu-ray").text("DVD / Blu-ray");
	var optDVDP = $(document.createElement("option")).attr("value", "DVD / Plex").text("DVD / Plex");
	var optBluP = $(document.createElement("option")).attr("value", "Blu-ray / Plex").text("Blu-ray / Plex");
	var optBDP = $(document.createElement("option")).attr("value", "DVD / Blu-ray / Plex").text("DVD / Blu-ray / Plex");
	var optPlex = $(document.createElement("option")).attr("value", "Plex").text("Plex");

	$.ajax({
		type : "GET",
		url : "javascript/ajax.php?page=edit&id=" + id,
		dataType : "xml",
		beforeSend : loading,
		success : function(xml) {
			$(xml).find('movie').each(function() {
				//titleText = $(this).attr('title');
				$("#movietitle").val($(this).attr('title'));
				$("#year").val($(this).attr('year'));
				$("#actors").val($(this).attr('actors'));
				$("#genre").val($(this).attr('genre'));
				$("#summary").val($(this).attr('summary'));
				var media = $(this).attr('media');
				if (media == "DVD") {
					optDVD.attr("selected", "selected").change();
				} else if (media == "Blu-ray") {
					optBlu.attr("selected", "selected").change();
				} else if (media == "DVD / Blu-ray") {
					optDVDBlu.attr("selected", "selected").change();
				} else if (media == "DVD / Blu-ray / Plex") {
					optBDP.attr("selected", "selected").change();
				} else if (media == "Blu-ray / Plex") {
					optBluP.attr("selected", "selected").change();
				} else if (media == "DVD / Plex") {
					optDVDP.attr("selected", "selected").change();
				} else if (media == "Plex") {
					optPlex.attr("selected", "selected").change();
				}
				$('body').removeClass('ui-loading');
			});
		},
		error : function() {
			alert("Possible browser hijacking. Cannot fill in the fields. Please fill them in manually.");
			$('body').removeClass('ui-loading');
		}
	});

	// add options to select element
	mediaSelect.append(optDVD).append(optBlu).append(optDVDBlu).append(optDVDP).append(optBluP).append(optBDP).append(optPlex);

	// merge media into one
	var mediaAll = mediaField.append(mediaLabel).append(mediaSelect);

	var form = $(document.createElement("form")).attr("id", "edit-form");

	var everything = form.append(removeFlipAll).append(movieAll).append(yearAll).append(genreAll).append(actorAll).append(summaryAll).append(mediaAll);

	$("#movieinfo").append(everything).trigger('create');
}

function saveEdit(id) {
	$("#saveBtn").remove();
	var formData = $("#edit-form").serialize();

	$.ajax({
		type : "POST",
		url : "javascript/ajax.php?page=editsave&movieeditid=" + id,
		cache : false,
		beforeSend : loading,
		data : formData,
		success : function(data) {
			$('body').removeClass('ui-loading');
			$("#movieinfo").html("");
			$(data).find('movie').each(function() {
				title = $(this).attr('title');
				year = $(this).attr('year');
				genre = $(this).attr('genre');
				actors = $(this).attr('actors');
				actorsArray = actors.split(', ');
				media = $(this).attr('media');
				summary = $(this).attr('summary');
				directors = $(this).attr('directors');
				writers = $(this).attr('writers');
				runtime = $(this).attr('runtime');
				tmdbid = $(this).attr('tmdbid');

				// convert runtime depending how it's formatted
				if (runtime > 1000) {
					runtime = (runtime / 1000) / 60;
				}

				movieid = $(this).attr('movieid');
			});
			var poster = $(document.createElement("img")).attr("src", "images/posters/" + movieid + "_Poster.png").attr("style", "width: 200px; height: 300px; display: block; margin-left: auto; margin-right: auto;");

			var plotDiv = $(document.createElement("div")).attr("data-role", "collapsible");
			var h4Plot = $(document.createElement("h4")).text("Plot");
			var pPlot = $(document.createElement("p")).text(summary);

			plotDiv.append(h4Plot).append(pPlot);
			
			
			var directDiv = $(document.createElement("div")).attr("data-role", "collapsible");
			var h4Direct = $(document.createElement("h4")).text("Director(s)");
			var pDirect = $(document.createElement("p")).text(directors);

			directDiv.append(h4Direct).append(pDirect);

			var actorsDiv = $(document.createElement("div")).attr("data-role", "collapsible");
			var h4Actors = $(document.createElement("h4")).text("Actors");
			var pActors = $(document.createElement("p")).attr("id", "actorsp").attr("name", "actorsp");

			for (var i = 0; i < actorsArray.length; i++) {
				var actorsA = $(document.createElement("a")).attr("href", "javascript: submitSearch('url', '" + actorsArray[i] + "');").text(actorsArray[i]);
				if ((actorsArray.length - i) > 1) {
					pActors.append(actorsA).append(", ");
				} else {
					pActors.append(actorsA);
				}
			}

			actorsDiv.append(h4Actors).append(pActors);

			var genreDiv = $(document.createElement("div")).attr("data-role", "collapsible");
			var h4Genre = $(document.createElement("h4")).text("Genre(s)");
			var pGenre = $(document.createElement("p")).text(genre);

			genreDiv.append(h4Genre).append(pGenre);

			var writersDiv = $(document.createElement("div")).attr("data-role", "collapsible");
			var h4Writers = $(document.createElement("h4")).text("Writer(s)");
			var pWriters = $(document.createElement("p")).text(writers);

			writersDiv.append(h4Writers).append(pWriters);

			var runtimeDiv = $(document.createElement("div")).attr("data-role", "collapsible");
			var h4Runtime = $(document.createElement("h4")).text("Runtime");
			var pRuntime = $(document.createElement("p")).text(Math.round(runtime) + " mins");

			runtimeDiv.append(h4Runtime).append(pRuntime);

			var mediaDiv = $(document.createElement("div")).attr("data-role", "collapsible");
			var h4Media = $(document.createElement("h4")).text("Media");
			var pMedia = $(document.createElement("p")).text(media);

			mediaDiv.append(h4Media).append(pMedia);
			
			$.ajax({
				type: "GET",
				url: "http://api.themoviedb.org/3/movie/" + tmdbid + "/videos?api_key=7389b73b4c2e1a9f63dfcda64f2bb1cf",
				async: false,
				contentType: 'application/json',
				dataType: 'jsonp',
				success: function(jsonc) {
					if(jsonc.results[0].site == "YouTube") {
						showTrailer = 1;
						var trailerDiv = $(document.createElement("div")).attr("data-role", "collapsible");
						var h4Trailer = $(document.createElement("h4")).text("Trailer");
						var pTrailer = $(document.createElement("p"));
						var frameTrailer = $(document.createElement("iframe")).attr("style", "width: 100%; height: 315px; border: 0px;").attr("allowfullscreen","").attr("src", "//www.youtube.com/embed/" + jsonc.results[0].key);
						pTrailer.append(frameTrailer);
						
						trailerDiv.append(h4Trailer).append(pTrailer);
						
						$("#movieinfo").append(poster).append(plotDiv).append(trailerDiv).append(directDiv).append(actorsDiv).append(genreDiv).append(writersDiv).append(runtimeDiv).append(mediaDiv).trigger('create');
			
					} else {
						$("#movieinfo").append(poster).append(plotDiv).append(directDiv).append(actorsDiv).append(genreDiv).append(writersDiv).append(runtimeDiv).append(mediaDiv).trigger('create');
					}
				}
			});
			
			var editBtn = $(document.createElement("a")).attr("href", "javascript: void(0);").attr("id", "edit-btn").attr("data-role", "button").attr("data-icon", "edit").attr("class", "ui-btn-right").attr("onclick", "javascript: buildEdit(" + movieid + ");").text("Edit");

			$("#movieTitle").text(title);
			
			$("#movieheader").append(editBtn).trigger('create');
		},
		error : onError
	});
}

$(document).on('change', '#removeFlip', function () {
	if($("#removeFlip").val() == "on") {
		alert("WARNING\n\nThis will permanently delete the movie from this list, but will NOT remove it from Plex. If a film is removed from Plex, it must also be removed from this as well. If a movie was accidentally deleted from here, but still exists in Plex it will be re-added once Plex indexes a new movie added to the movie library.");
	}
});
