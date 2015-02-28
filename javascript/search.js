function changeSearch() {
	$("#search-input").remove();

	var search = $(document.createElement("input")).attr("type", "search").attr("name", "search").attr("id", "search-basic").attr("value", "");

	$("#search-div").append(search).trigger("create");
	$("#search-basic").focus();
}

function submitSearch(type, funQuery) {
	var screenWidth = window.screen.availWidth;
	$('body').addClass('ui-loading');
	$("#search-basic").blur();
	$("#sidebar").panel("close");
	$("#header").nextAll('div').html("");

	if (type == "form") {
		var query = encodeURI($("#search-basic").val());
	} else if (type == "url") {
		$('.ui-dialog').dialog('close');
		var query = funQuery;
	}

	$.ajax({
		type : "POST",
		url : "javascript/ajax.php?page=search&query=" + query,
		cache : false,
		success : function(data) {
			var divTitle = $(document.createElement("h2")).attr("style", "font-size: 15px; margin-top: 10px; margin-left: 15px;").text("Search Results for \"" + decodeURI(query) + "\":");

			var exitSearch = $(document.createElement("a")).attr("href", "javascript: location.reload();").attr("data-role", "button").attr("data-icon", "delete").attr("class", "ui-btn-right");

			if (screenWidth <= 320) {
				exitSearch.text("Exit");
			} else {
				exitSearch.text("Exit Search");
			}

			$("#header").append(exitSearch).trigger('create');

			var contentDiv = $(document.createElement("div")).attr("data-role", "content").attr("class", "ui-content").attr("role", "main");

			var displaytype = $(data).find("movies").attr("displaytype");
			var countAmount = $(data).find("film").length;

			if (displaytype == 1) {
				var width = countAmount * 111;
				if (width == 0) {
					width = 500;
				}

				var outerDiv = $(document.createElement("div")).attr("style", "overflow: scroll; -webkit-overflow-scrolling: touch;");
				var innerDiv = $(document.createElement("div")).attr("style", "width: " + width + "px;");
				if (countAmount != 0) {
					$(data).find("film").each(function() {
						var id = $(this).attr('id');
						var year = $(this).attr('year');
						var movietitle = $(this).attr('movietitle');
						var anchorTag = $(document.createElement("a")).attr("href", "viewmovie.php?movieid=" + id).attr("data-rel", "dialog").attr("data-transition", "pop");
						var imgTag = $(document.createElement("img")).attr("src", "images/posters/" + id + "_Poster.png").attr("style", "width: 100px; height: 150px; padding-right: 10px").attr("onerror", "this.src='image.php?title=" + movietitle + "&year=" + year + "&id=" + id + "&atitle=" + movietitle + "';");

						anchorTag.append(imgTag);
						innerDiv.append(anchorTag);
					});
				} else {
					innerDiv.append("Your search query returned no results.");
				}
				outerDiv.append(innerDiv);
				contentDiv.append(outerDiv);
			} else if (displaytype == 2) {
				if (countAmount != 0) {
					var outerUL = $(document.createElement("ul")).attr("data-role", "listview").attr("data-inset", "true").attr("data-autodividers", "true").attr("style", "margin-top: -1%;");
					$(data).find("film").each(function() {
						var id = $(this).attr('id');
						var movietitle = $(this).attr('movietitle');
						var innerLI = $(document.createElement("li"));
						var innerHref = $(document.createElement("a")).attr("href", "viewmovie.php?movieid=" + id).attr("data-rel", "dialog").attr("data-transition", "pop").text(movietitle);

						innerLI.append(innerHref);
						outerUL.append(innerLI);
					});
					contentDiv.append(outerUL).trigger('create');
				} else {
					contentDiv.text("Your search query returned no results.");
				}
			} else if (displaytype == 3) {
				if (countAmount != 0) {
					var outerUL = $(document.createElement("ul")).attr("data-role", "listview").attr("data-inset", "true").attr("data-autodividers", "true").attr("style", "margin-top: -1%;");
					$(data).find("film").each(function() {
						var id = $(this).attr('id');
						var movietitle = $(this).attr('movietitle');
						var actors = $(this).attr('actors');
						var innerLI = $(document.createElement("li"));
						var innerHref = $(document.createElement("a")).attr("href", "viewmovie.php?movieid=" + id).attr("data-rel", "dialog").attr("data-transition", "pop");
						var h2Text = $(document.createElement("h2")).text(movietitle);
						var actorsText = $(document.createElement("p")).text(actors);
						var innerImg = $(document.createElement("img")).attr("src", "images/posters/" + id + "_Poster.png").attr("style", "width: 54px; height: 100%").attr("alt", movietitle);
						//echo "<li><a href=\"viewmovie.php?movieid=" . $fetchLetter[0] . "\" data-rel=\"dialog\" data-transition=\"pop\"><img src=\"images/posters/" . $fetchLetter[0] . "_Poster.png\" style=\"width: 54px; height: 100%;\" alt=\"" . $fetchLetter[1] . "\"  onerror=\"this.src='image.php?title=" . rawurlencode($fetchLetter[1]) . "&amp;year=" . $fetchLetter[3] . "&amp;id=" . $fetchLetter[0] . "&amp;atitle=" . rawurlencode($fetchLetter[4]) . "'\"><h2>" . $fetchLetter[1] . "</h2><p>" . $fetchLetter[2] . "</p></a></li>";
						
						innerHref.append(innerImg).append(h2Text).append(actorsText);
						innerLI.append(innerHref);
						outerUL.append(innerLI);
					});
					contentDiv.append(outerUL).trigger('create');
				} else {
					contentDiv.text("Your search query returned no results.");
				}
			} else {
				contentDiv.text("There is currently no layout selected! Please click on the sidebar for settings.");
			}

			$("#header").nextAll('div').append(divTitle).append(contentDiv).trigger('create');
		},
	});

	$('body').removeClass('ui-loading');
}