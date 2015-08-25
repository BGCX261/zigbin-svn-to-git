var viewFieldContainerIdGlobal = "" ;
var listContainerIdGlobal = "" ;
var fileObjectGlobal = "" ;

function uploadFile(fileObject,zigHash,fieldName,viewFieldContainerId,listContainerId)
{
	viewFieldContainerIdGlobal = viewFieldContainerId ;
	listContainerIdGlobal = listContainerId ;
	fileObjectGlobal = fileObject ;
	var fd = new FormData();
	fd.append(fieldName, fileObject.files[0]) ;
	var xhr = new XMLHttpRequest() ;
	xhr.upload.addEventListener("progress", uploadProgress, false) ;
	xhr.addEventListener("load", uploadComplete, false) ;
	xhr.addEventListener("error", uploadFailed, false) ;
	xhr.addEventListener("abort", uploadCanceled, false) ;
    document.getElementById(viewFieldContainerIdGlobal).innerHTML = "<div id='divFileBarUploadingId' class='progressBarInProgress'></div>" ;
	xhr.open("POST", "../zig-api/decoder.php?zig_hash="+zigHash+"&"+fieldName+"="+fileObject.value) ;
	xhr.send(fd) ;
	fileObject.value = "" ;
}

function uploadProgress(evt)
{
    if(evt.lengthComputable)
    {
      var percentComplete = Math.round(evt.loaded * 100 / evt.total) ;
      document.getElementById("divFileBarUploadingId").style.width = percentComplete.toString() + '%' ;
    }
    else
    {
      document.getElementById(viewFieldContainerIdGlobal).innerHTML = "Uploading..." ;
    }
}

function uploadComplete(evt)
{
	var response = JSON.parse(evt.target.responseText) ;
    document.getElementById("divFileBarUploadingId").className = "progressBarComplete" ;
	zigMessenger("File Uploaded") ;
	if(response['listing'])
	{
		document.getElementById(listContainerIdGlobal).innerHTML = response['listing'] ;
	}
	document.getElementById(viewFieldContainerIdGlobal).innerHTML = response['html'] ;
	document.getElementById(fileObjectGlobal.id + "_zigHiddenFileName").value = response['field_value'] ;
}

function uploadFailed(evt)
{
    document.getElementById("divFileBarUploadingId").className = "progressBarError" ;
	zigMessenger("There was an error attempting to upload the file") ;
}

function uploadCanceled(evt)
{
    document.getElementById("divFileBarUploadingId").className = "progressBarError" ;
	zigMessenger("The upload has been canceled by the user or the browser dropped the connection") ;
}

function uploadResize(file)
{
	var img = document.createElement("img");
	img.src = window.URL.createObjectURL(file);

	var canvas = document.createElement("canvas");
	var ctx = canvas.getContext("2d");
	ctx.drawImage(img, 0, 0);

	var bw = img.width ;
	var bh = img.height ;
	alert("w:"+bw+" bh:"+bh) ;
	alert(" w:"+img.width+" h:"+img.height) ;
	var MAX_WIDTH = 800;
	var MAX_HEIGHT = 600;
	var width = img.width;
	var height = img.height;

	if (width > height)
	{
		if (width > MAX_WIDTH)
		{
			height *= MAX_WIDTH / width;
			width = MAX_WIDTH;
		}
	}
	else
	{
		if (height > MAX_HEIGHT)
		{
			width *= MAX_HEIGHT / height;
			height = MAX_HEIGHT;
		}
	}

	canvas.width = width;
	canvas.height = height;
	var ctx = canvas.getContext("2d");
	ctx.drawImage(img, 0, 0, width, height);
	return canvas ;
}