<?php 
function getTailOutputLog($file)
{
	if(!isset($_SESSION)){
		session_start();
	}

	$run = Run::findOrFail($id);
	$host = $run->image->getHost();

	if (\Session::has("fileOffset_run-$id")) {
		$offset = \Session::get("fileOffset_run-$id");
	}
	else {
		$offset = 0;
	}

	$tuple = phpReadTail($file, $offset);
	
	\Session::put("fileOffset_run-$id", $tuple['offset']);
	return nl2br($tuple['data']);
	exit();
}

// Using bash commands
function bashReadTail($file, $offset){
	if (`if [ -f "$file" ]; then echo true; else echo false; fi` == "false") {
		return "Error: File does not exist. $file";
	}
	$totalLines=`wc -l < "$file"`;

	$diff=$totalLines - $offset;

	$lines = `tail -n '$diff' '$file'`;
	return ['data' => $lines, 'offset' => $totalLines];
}
//Php only method
function phpReadTail($file, $offset){
	$handle = fopen($file, 'r');
	if ($offset > 0) {
		// Find the end of the file
		fseek($handle, 0, SEEK_END);
		$fileEnd = ftell($handle);
		// Rewind the file pointer
		fseek($handle, 0);
		//Caluate the amount to read from the offset to the calculated end of
		// the file (incase the file has changed)
		$readLength = $fileEnd - $offset;
		// Read the calculated amount after the offset.
		$data = stream_get_contents($handle, $readLength, $offset);
		// Store the previously caculated end as the new offset.
		$offset = ftell($handle);
	} else {
		// Find the end of the file
		fseek($handle, 0, SEEK_END);
		// Store the file end in the session
		$offset = ftell($handle);
		// Rewind the file pointer
		fseek($handle, 0);
		// Grab content up to the previously saved offset (so that new data isn't printed twice or skipped)
		$data = stream_get_contents($handle, $offset);
	}
	return ['offset' => $offset, 'data' => $data];
}
