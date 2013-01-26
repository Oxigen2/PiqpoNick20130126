var _parentCall_loading;
var _parentCall_ready;
var _parentCall_finished;
var _parentCall_startFunction;
var _startFunction;
var _ready = true;	// assume ready as soon as registration is called, unless otherwise specified.
var _params;
var _frameId;

// For IE's benefit
if (typeof console == "undefined") 
{
	var console = { log: function() {}, info: function() {} , warn: function() {}, error: function() {} }
}

function slideSetStartFunction(startFunction, isLoading, params)
{
	console.log("client call to slideSetStartFunction");
	if (isLoading == null)
	{
		isLoading = false;
	}
	_ready = !isLoading;
	_startFunction = startFunction;
	if (typeof _parentCall_startFunction !== "undefined") _parentCall_startFunction(_frameId, startFunction, isLoading, params);
}		
function slideLoading()
{
	_ready = false;
	if (typeof _parentCall_loading !== "undefined") _parentCall_loading(_frameId);
}
function slideReady()
{
	_ready = true;
	if (typeof _parentCall_ready !== "undefined") _parentCall_ready(_frameId);
}
function slideFinished()
{
	if (typeof _parentCall_finished !== "undefined") _parentCall_finished(_frameId);
}
function browserDimensions()
{
	rr = new Object();
	if (window != window.top)
	{
		rr.width = parent.browserWidth();
		rr.height = parent.browserHeight();
	}
	else
	{
		if (document.body && document.body.offsetWidth) 
		{
			rr.width = document.body.offsetWidth;
			rr.height = document.body.offsetHeight;
		}
		else if (document.compatMode=='CSS1Compat' &&
			document.documentElement &&
			document.documentElement.offsetWidth ) 
		{
			rr.width = document.documentElement.offsetWidth;
			rr.height = document.documentElement.offsetHeight;
		}
		else if (window.innerWidth && window.innerHeight) 
		{
			rr.width = window.innerWidth;
			rr.height = window.innerHeight;
		}			
	}
	return rr;
}
function registration(data)
{
	_parentCall_loading = data.loading;
	_parentCall_ready = data.ready;
	_parentCall_finished = data.finished;
	_parentCall_startFunction = data.startFunction;
	_parentCall_debug = data.debugFunction;
	_frameId = data.frameId;
	
	rr = new Object();
	rr.frameId = _frameId;
	rr.ready = _ready;
	rr.startFunction = _startFunction;
	rr.params = _params;
	
	return rr;
}
