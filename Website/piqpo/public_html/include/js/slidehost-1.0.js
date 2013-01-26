function slideLoading(frameId)
{
	// Called with a slide has indicated that it has started loading, and is therefore not ready to be run
	$("#"+frameId).data('isReady', false);
	$("#"+frameId).trigger('slideLoading');
}
function slideReady(frameId)
{
	// Called when a slide has indicated that it is ready to be run.
	slideDebug(frameId, "slideReady");
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
	$("#"+frameId).trigger('slideFinished');
}
function setSlideStartFunction(frameId, func, isLoading)
{
	// Associate this function, to be activated on showing the frame, with the frame object
	slideDebug(frameId, "slideSetStartFn");
	$("#"+frameId).data('startFunction', func);
	$("#"+frameId).data('isReady', !isLoading);
}
function slideDebug(frameId, text)
{
	writeDebug(frameId+" "+text);
}
function writeDebug(text)
{
	if (typeof(dbgMsg) != "undefined")
	{
		dbgMsg(text);
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
	var registrationFunction = document.getElementById(frameId).contentWindow.registration;

	if (registrationFunction != null)
	{
		data = new Object();
		data.loading = slideLoading;
		data.ready = slideReady;
		data.finished = slideFinished;
		data.startFunction = setSlideStartFunction;
		data.debugFunction = slideDebug;
		data.frameId = frameId;

		// Call the iframe registration function
		rr = registrationFunction(data);
		
		// Update frame data based on response
		setSlideStartFunction(frameId, rr.startFunction);							
		if( rr.ready )
		{
			slideDebug(frameId, "reg ready");
			slideReady(frameId);
		}
		else
		{
			slideDebug(frameId, "reg loading");
			slideLoading(frameId);
		}
	}
	else if ( typeof $("#"+frameId).attr("src") !== "undefined" )
	{
		// Need to decide what to do, may be because the slide doesn't have the necessary include.
		// Probably want to communicate the illegal slide to the server, remove it, and move on.
		//alert("No registration function found on frame " + frameId);
	}
}

// creates a new frame and appends it to the container with id frameholder
function createFrame(num)
{
	frameId = formFrameId(num);

	// Create frame without a source
	$("<iframe frameborder='0' id='"+frameId+"' />").appendTo($("#frameholder"));
}

// load the given source into the given frame
function loadFrame(frameId, source)
{
	// Clear start function if there is one
	$("#"+frameId).removeData('startFunction');

	// Assume not ready until the load event triggers
	slideLoading(frameId);
	
	// Register on load callback to perform registration
	// The call will be unregistered after the invocation, not necessary, just was easier to do it this way
	$("#"+frameId).one("load", function(){registerFrame(frameId)});
	
	// Load the new source
	$("#"+frameId).attr('src', source);		
}
