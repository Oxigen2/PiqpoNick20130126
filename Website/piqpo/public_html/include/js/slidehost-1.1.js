// For IE's benefit
if (typeof console == "undefined") 
{
	var console = { log: function() {} }
}

function slideLoading(frameId)
{
	// Called with a slide has indicated that it has started loading, and is therefore not ready to be run
	console.log("%s slideLoading",frameId);
	$("#"+frameId).data('isReady', false);
	$("#"+frameId).trigger('slideLoading');
}
function slideReady(frameId)
{
	// Called when a slide has indicated that it is ready to be run.
	console.log("%s slideReady",frameId);
	$("#"+frameId).data('isReady', true);
	$("#"+frameId).trigger('slideReady');
	
	if (typeof(startLateSlide) != "undefined")
	{
		startLateSlide();
	}
}
function slideFinished(frameId)
{
	// Called when a slide has indicated that it has finished.
	console.log("%s slideFinished",frameId);
	$("#"+frameId).trigger('slideFinished');
}
function setSlideStartFunction(frameId, func, isLoading, params)
{
	// Associate this function, to be activated on showing the frame, with the frame object
	console.log("%s slideSetStartFn",frameId);
	$("#"+frameId).data('startFunction', func);
	if (isLoading)
	{
		slideLoading(frameId);
	}
	else
	{
		slideReady(frameId);
	}
	if (typeof(params) != "undefined")
	{
		if (params.noFadeIn)
		{
			console.log("%s fade in turned off",frameId);
			$("#"+frameId).data('noFadeIn', true);
		}
	}	
}
// This is also called by client iframes
function browserWidth()
{
	return $(window).width();
}

// This is also called by client iframes
function browserHeight()
{
	return $(window).height();
}

function formFrameId(num)
{
	return "iframe_" + num;
}

function numFromFrameId(frameId)
{
	return frameId.split('_')[1];
}

// Calls the iframe to register callback functions.
function registerFrame(frameId)
{
	console.log("%s registerFrame",frameId);
	var registrationFunction = document.getElementById(frameId).contentWindow.registration;

	if (registrationFunction != null)
	{
		data = new Object();
		data.loading = slideLoading;
		data.ready = slideReady;
		data.finished = slideFinished;
		data.startFunction = setSlideStartFunction;
		data.frameId = frameId;

		// Call the iframe registration function
		rr = registrationFunction(data);
		
		// Update frame data based on response
		setSlideStartFunction(frameId, rr.startFunction, !(rr.ready), rr.params);
	}
	else if ( typeof $("#"+frameId).attr("src") !== "undefined" )
	{
		// Need to decide what to do, may be because the slide doesn't have the necessary include.
		// Probably want to communicate the illegal slide to the server, remove it, and move on.
		console.error("%s registration fn not found",frameId);
	}
}

// creates a new frame and appends it to the container with id frameholder
function createFrame(num)
{
	frameId = formFrameId(num);

	// Create frame without a source
	$("<iframe frameborder='0' id='"+frameId+"' />").appendTo($("#frameholder"));
}

function removeFrames()
{
	$("#frameholder iframe").remove();
}

// load the given source into the given frame
function loadFrame(frameId, source)
{
	// Clear start function if there is one
	console.log("%s clearing start fun", frameId);
	$("#"+frameId).removeData('startFunction');

	// Assume not ready until the load event triggers
	slideLoading(frameId);
	
	// Register on load callback to perform registration
	// The call will be unregistered after the invocation, not necessary, just was easier to do it this way
	$("#"+frameId).one("load", function(){registerFrame(frameId)});
	
	// Load the new source
	console.log("%s loading %s", frameId, source);
	$("#"+frameId).attr('src', source);		
}
