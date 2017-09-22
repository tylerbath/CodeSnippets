<?php
	//Required Variables
	//$tailUrl
	if(empty($stopMethod){
		$stopMethod = "text";
	}
	// If stopMethod is text, then each loop looks for this text to end
	// If stopMethod is url, then each loop makes an ajax call, and ends if this is the response
	if(empty($stopText){ 
		echo "No stop text defined";
		return;
	}
	if(stopMethod == "url") { //Stop method is url
		if(empty($stopUrl){
			echo "Stop from url selected, but stop url is undefined";
			return;
		}
	}
	//Function to call after finish detected. E.G. redirect('/next')
	if(empty($callBack){ 
		$callBack = function(){};
	}
?>

<div class="row">
<div class="columns large-8 large-centered">
	<h1>Run Progress</h1>
  <samp id="tail"></samp>
</div>
</div>

@section('customScripts')
<script>
var ansispan = function (str) {
  Object.keys(ansispan.foregroundColors).forEach(function (ansi) {
    var span = '<span style="color: ' + ansispan.foregroundColors[ansi] + '">';

    //
    // `\033[Xm` == `\033[0;Xm` sets foreground color to `X`.
    //

    str = str.replace(
      new RegExp('\033\\[' + ansi + 'm', 'g'),
      span
    ).replace(
      new RegExp('\033\\[0;' + ansi + 'm', 'g'),
      span
    );
  });
  //
  // `\033[1m` enables bold font, `\033[22m` disables it
  //
  str = str.replace(/\033\[1m/g, '<b>').replace(/\033\[22m/g, '</b>');

  //
  // `\033[3m` enables italics font, `\033[23m` disables it
  //
  str = str.replace(/\033\[3m/g, '<i>').replace(/\033\[23m/g, '</i>');

  str = str.replace(/\033\[m/g, '</span>');
  str = str.replace(/\033\[0m/g, '</span>');
  return str.replace(/\033\[39m/g, '</span>');
};

ansispan.foregroundColors = {
  '30': 'black',
  '31': 'red',
  '32': 'green',
  '33': 'yellow',
  '34': 'blue',
  '35': 'purple',
  '36': 'cyan',
  '37': 'white'
};

if (typeof module !== 'undefined' && module.exports) {
  module.exports = ansispan;
}
function ajaxLoopForOutputLog(){
  $.get('{{$tailUrl}}', function(data) {
    $('#tail').append(ansispan(data));
    window.scrollTo(0,document.body.scrollHeight);
    // If window.focused pause say 2 seconds
    // If window not focused pause say 10 seconds
    // If looking at a different tab, stop, make listener for when switched back to this tab
    if (data.indexOf('{{$stopText}}') > -1) {
      return;
    } else{
      setTimeout(ajaxLoopForOutputLog, 5000);
    };
  });
};
ajaxLoopForOutputLog();
</script>
@stop
