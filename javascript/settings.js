var removeIsChecked = 0;

$(document).ready(function() {
	$("#removeClients").click(function() {
		if(removeIsChecked == 0) {
			alert("WARNING\n\nThis option will completely wipe all active clients synced with MovieList. In order for them to reconnect, you must navigate to the home page again with Plex running on the device (with it connected to your Plex server).");
			removeIsChecked = 1;
		} else {
			removeIsChecked = 0;
		}
	});
});

function refreshPage() {
  $.mobile.changePage(
    window.location.href,
    {
      allowSamePageTransition : true,
      transition              : 'none',
      showLoadMsg             : false,
      reloadPage              : true
    }
  );
}

function onSuccess(data, status) {
	data = $.trim(data);
    $("#settings-all").html(data);
    $("#settingsSave").remove();
    
    var refreshBtn = $(document.createElement("a")).attr("href", "javascript: location.reload();").attr("data-role", "button").text("Refresh for Best Results");
	$("#settingsSuccess").append(refreshBtn).trigger('create');
    
	$('body').removeClass('ui-loading');
}

function onError(data, status) {
	$('body').removeClass('ui-loading');
}

function loading() {
	$('body').addClass('ui-loading');
}


function submitForm() {
	var formData = $("#settings-form").serialize();
	$.ajax({
		type: "POST",
		url: "javascript/ajax.php?page=settings",
		cache: false,
		beforeSend: loading,
		data: formData,
		success: onSuccess,
		error: onError
	});
}

function sleep(miliseconds) {
    var currentTime = new Date().getTime();
    while (currentTime + miliseconds >= new Date().getTime()) {
    }
}

function closeSettings() {
    $("#settings").dialog('close');
    refreshPage();
}