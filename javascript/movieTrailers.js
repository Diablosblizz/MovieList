function showTrailer(movietitle) {
    var url = "http://trailersapi.com/trailers.json?movie=" + encodeURI(movietitle) + "&limit=1&width=320";
$.getJSON(url, function(data) {
  var frame = data[0].code; 
  $("#movieinfo").html(frame);
  $("iframe").attr("id", "movietrailer");
  
  var src = $("#movietrailer").attr("src");
  src = src + "?autoplay=1";
    
  alert(url);
  $("#movietrailer").attr("src", src);
});
}