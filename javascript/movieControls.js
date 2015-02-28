var currentMode = 1;

var playBtn = $(document.createElement("img")).attr("id", "playBtn").attr("src", "images/play.png").attr("style", "width: 48px; height: 48px;").attr("alt", "Play Button").attr("onclick", "javascript: playPauseMovie();");

var pauseBtn = $(document.createElement("img")).attr("id", "pauseBtn").attr("src", "images/pause.png").attr("style", "width: 48px; height: 48px;").attr("alt", "Pause Button").attr("onclick", "javascript: playPauseMovie();");

var stopBtn = $(document.createElement("img")).attr("src", "images/stop.png").attr("style", "width: 48px; height: 48px;").attr("alt", "Stop Button").attr("onclick", "javascript: stopMovie();");

var backBtn = $(document.createElement("img")).attr("src", "images/back.png").attr("style", "width: 48px; height: 48px;").attr("alt", "Step Back Button").attr("onclick", "javascript: stepBack();");

var forwardBtn = $(document.createElement("img")).attr("src", "images/forward.png").attr("style", "width: 48px; height: 48px;").attr("alt", "Step Forward Button").attr("onclick", "javascript: stepForward();");

function playMovie(id, mid, name, actors) {
	$('body').addClass('ui-loading');
	$.ajax({
		type : "POST",
		url : "javascript/ajax.php?page=playMovie&id=" + id,
		dataType : "xml",
		cache : false,
		success : function(data) {
			status = 0;
			$(data).find('status').each(function() {
				status = $(this).attr('code');
			});

			if (status == 1) {
				// Show media controls
				$("#media-controls").append(backBtn);
				$("#media-controls").append(pauseBtn);
				$("#media-controls").append(stopBtn);
				$("#media-controls").append(forwardBtn);
				
				$("#currentPlayback").remove();
				var newPlaybackDiv = $(document.createElement("div")).attr("data-role", "content").attr("id", "currentPlayback").attr("name", "currentPlayback").attr("class", "ui-content");
				var h2PlayText = $(document.createElement("h2")).attr("style", "font-size: 15px; margin-top: -5px;").text("Current Playback:");
				
				var ulElm = $(document.createElement("ul")).attr("data-role", "listview").attr("data-inset", "true").attr("style", "margin-top: -5px;");
				var liElm = $(document.createElement("li"));
				
				var aHrefElm = $(document.createElement("a")).attr("href", "viewmovie.php?movieid=" + mid + "&displayPlayBack").attr("data-rel", "dialog").attr("data-transition", "pop");
				var imgElm = $(document.createElement("img")).attr("src", "http://home.kbnetwork.ca/movielist/images/posters/" + mid + "_Poster.png").attr("style", "width: 54px; height: 100%;");
				
				var h2Elm = $(document.createElement("h2")).text(name);
				var pElm = $(document.createElement("p")).text(actors);
				
				aHrefElm.append(imgElm).append(h2Elm).append(pElm);
				
				liElm.append(aHrefElm);
				ulElm.append(liElm);
				
				newPlaybackDiv.append(h2PlayText).append(ulElm);
				$("#header").after(newPlaybackDiv);
				$("#currentPlayback").trigger('create').trigger('pagecreate');
			} else {
				alert("There was a problem sending the playback to the device. Please ensure that the device is powered on, and the Plex app is running.");
			}
			$('body').removeClass('ui-loading');
		},
	});
}

function showMediaPlayback() {
	currentMode = 1;
	$("#media-controls").append(backBtn);
	$("#media-controls").append(pauseBtn);
	$("#media-controls").append(stopBtn);
	$("#media-controls").append(forwardBtn);
}

function playPauseMovie() {
	$('body').addClass('ui-loading');
	$.ajax({
		type : "POST",
		url : "javascript/ajax.php?page=playPauseMovie&mode=" + currentMode,
		cache : false,
		success : function(data) {
			$('body').removeClass('ui-loading');
			if (currentMode == 0) {
				$("#media-controls").html("");
				$("#media-controls").append(backBtn);
				$("#media-controls").append(playBtn);
				$("#media-controls").append(stopBtn);
				$("#media-controls").append(forwardBtn);
				currentMode = 1;
			} else {
				$("#media-controls").html("");
				$("#media-controls").append(backBtn);
				$("#media-controls").append(pauseBtn);
				$("#media-controls").append(stopBtn);
				$("#media-controls").append(forwardBtn);
				currentMode = 0;
			}
		},
	});
}

function stopMovie() {
	var confirmr = confirm("Are you sure you wish to stop playback?");
	if (confirmr == true) {
		$('body').addClass('ui-loading');
		$.ajax({
			type : "POST",
			url : "javascript/ajax.php?page=stopMovie",
			cache : false,
			success : function(data) {
				$("#media-controls").html("");
				$('body').removeClass('ui-loading');
				$("#currentPlayback").remove();
			},
		});
	}
}

function stepBack() {
	$('body').addClass('ui-loading');
	$.ajax({
		type : "POST",
		url : "javascript/ajax.php?page=stepBack",
		cache : false,
		success : function(data) {
			$('body').removeClass('ui-loading');
		},
	});
}

function stepForward() {
	$('body').addClass('ui-loading');
	$.ajax({
		type : "POST",
		url : "javascript/ajax.php?page=stepForward",
		cache : false,
		success : function(data) {
			$('body').removeClass('ui-loading');
		},
	});
}
