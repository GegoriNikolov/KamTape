
function getXmlHttpRequest()
{
	var httpRequest = null;
	try
	{
		httpRequest = new ActiveXObject("Msxml2.XMLHTTP");
	}
	catch (e)
	{
		try
		{
			httpRequest = new ActiveXObject("Microsoft.XMLHTTP");
		}
		catch (e)
		{
			httpRequest = null;
		}
	}
	
	if (!httpRequest && typeof XMLHttpRequest != "undefined")
	{
		httpRequest = new XMLHttpRequest();
	}
	
	return httpRequest;
}

function getUrlSync(url)
{
	return getUrl(url, false, null);
}

function getUrlAsync(url, handleStateChange)
{
	return getUrl(url, true, handleStateChange);
}


// call a url
function getUrl(url, async, handleStateChange) {
	var xmlHttpReq = getXmlHttpRequest();

	if (!xmlHttpReq)
		return;

	if (handleStateChange)
	{
		xmlHttpReq.onreadystatechange = function()
			{
				handleStateChange(xmlHttpReq);
			};
	}
	else
	{
		xmlHttpReq.onreadystatechange = function() {;}
	}

	xmlHttpReq.open("GET", url, async);
	xmlHttpReq.send(null);
}

function postUrl(url, data, async, stateChangeCallback)
{ 
	var xmlHttpReq = getXmlHttpRequest(); 

	if (!xmlHttpReq)
		return;

	xmlHttpReq.open("POST", url, async);
	xmlHttpReq.onreadystatechange = function()
		{
			stateChangeCallback(xmlHttpReq);
		};
	xmlHttpReq.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
	xmlHttpReq.send(data);
}

function urlEncodeDict(dict)
{ 
	var result = "";
	for (var i in dict)
		result += "&" + encodeURIComponent(i) + "=" + encodeURIComponent(dict[i]);

	return result;
}

function execOnSuccess(stateChangeCallback)
{
	return function(xmlHttpReq)
		{
			if (xmlHttpReq.readyState == 4 &&
					xmlHttpReq.status == 200)
				stateChangeCallback(xmlHttpReq);
			// fimxe: do something on error?
		};
}

function postForm(formName, async, successCallback)
{
	var form = document.forms[formName];

	var formVars = new Array();
	for (var i = 0; i < form.elements.length; i++)
	{
		var formElement = form.elements[i];
		formVars[formElement.name] = formElement.value;
	}

	postUrl(form.action, urlEncodeDict(formVars), async, execOnSuccess(successCallback));
}

function replaceDivContents(xmlHttpRequest, dstDivId)
{
	var dstDiv = document.getElementById(dstDivId);
	dstDiv.innerHTML = xmlHttpRequest.responseText;
}
