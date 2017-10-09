var MouseX = 0;
var MouseY = 0;

var IE = document.all ? true : false;

function getMouseXY(e)
{
	if(IE)
	{
		MouseX = event.clientX + document.body.scrollLeft;
		MouseY = event.clientY + document.body.scrollTop;
	}
	else
	{
		MouseX = e.pageX;
		MouseY = e.pageY;
	}
	if(MouseX < 0)
	{
		MouseX = 0;
	}
	if(MouseY < 0)
	{
		MouseY = 0;
	}
}

if(!IE)
{
	document.captureEvents(Event.MOUSEMOVE);
}

document.onmousemove = getMouseXY;

function ShowTooltip(contents)
{
	var tooltip = $("tooltip");
	tooltip.innerHTML = contents;
	tooltip.style.top = MouseY;
	tooltip.style.left = MouseX + 5;
	tooltip.show();
}

function HideTooltip()
{
	$('tooltip').hide();
}


function ShowSearch(searchType)
{
	if(searchType == "Simple")
	{
		$('SimpleSearch').show();
		$('AdvancedSearch').hide();
	}
	else if(searchType == "Advanced")
	{
		$('AdvancedSearch').show();
		$('SimpleSearch').hide();
	}
}

function ShowAddForm(equipmentType)
{
	if(equipmentType == "SU")
	{
		$('AddSU').show();
		$('AddAP').hide();
	}
	else if(equipmentType == "AP")
	{
		$('AddAP').show();
		$('AddSU').hide();
	}
}

function showPOP()
{
	$('SelectPOP').submit();
}

function changeTech(obj)
{
	new Ajax.Request('/api/technology.php?technology=' + obj.value, {
	  onSuccess: function(response) {
	      var technology = JSON.parse(response.responseText);
	      if('hasColorCode' in technology && technology.hasColorCode == 1)
	      	$('colorCodeRow').show();
	      else
	      	$('colorCodeRow').hide();
	      
	  }
	});
}

function getPOPColorCodes()
{
	
}

function updateType(type)
{
	$('owner').update(type + " owner");
}

function FillSUGeocode(point)
{
	var form = $("SUForm");
	form.CustomerGeocode.value = point;
}

function setSpeed(value)
{
	var form = $("SUForm");
	var speedDowns = {
        '5': 2000,
        '6': 3000,
        '1': 256,
        '2': 512,
        '3': 768,
        '4': 1500,
        '7': 1,
        '8': 512,
        '9': 1500,
        '10': 3000,
        '11': 3000,
        '12': 512,
	'13': 1500,
	'14': 10000
	}
	form.elements.Speed_Down.value = speedDowns[value];
	
	var speedUps = {
	'5': 2000,
	'6': 3000,
	'1': 256,
	'2': 512,
	'3': 768,
	'4': 1500,
	'7': 1,
	'8': 256,
	'9': 256,
	'10': 384,
	'11': 1000,
	'12': 1000,
	'13': 1000,
	'14': 1000
	}
	form.elements.Speed_Up.value = speedUps[value];
}

function DoGeocode(form)
{
	var geocoder = new GClientGeocoder();
	var address = "";

	if(form.EquipmentType.value == "SU")
	{
		address += form.CustomerAddress.value + ", " + form.CustomerCity.value + ", " + form.CustomerState.value + " " + form.CustomerZIP.value;
		form.CustomerGeocode.value = "loading...";
		geocoder.getLatLng(address, 
			function(point)
			{
				if(!point)
				{
					alert(address + " not found.");
					FindAddress(form.CustomerCity.value + ", " + form.CustomerState.value + " " + form.CustomerZIP.value);
				}
				FillSUGeocode(point);
			});
	}
	else if(form.EquipmentType.value == "AP")
	{
		address += form.LocationAddress.value + ", " + form.LocationCity.value + ", " + form.LocationState.value + " " + form.LocationZIP.value;
		form.LocationGeocode.value = "loading...";
		geocoder.getLatLng(address, 
			function(point)
			{
				if(!point)
				{
					alert(address + " not found.");
					FindAddress(form.LocationCity.value + ", " + form.LocationState.value + " " + form.LocationZIP.value);
				}
				FillAPGeocode(point);
			});
	}
}

function FillAPGeocode(point)
{
	var form = $("APForm");
	form.LocationGeocode.value = point;
}

function EditRow(rowID)
{
	type = rowID.substr(0, 2);
	id = rowID.substr(2);
	window.open('detail.php?id=' + id + '&type=' + type,'detail', 'width=900, height=400, toolbar=1, resizable=1, location=1');
}

function FindAddress(address)
{
	window.open('findAddress.php?address=' + address, 'findAddress', 'width=900, height=500, toolbar=1, resizable=1, location=1');
}

function FillToday(form)
{
	var now = new Date();
	var day = now.getDate();
	var month = now.getMonth() + 1;
	var year = now.getFullYear();
	
	form.InstallDate.value = month + "/" + day + "/" + year;
}

var map;
var kmlURL;
var exml;

function mapLoad(search, show)
{
	if (GBrowserIsCompatible())
	{
	    var icon = new GIcon();
	    icon.shadow = "https://hurricane.somedomain/image/markerShadow.png";
	    icon.iconSize = new GSize(12, 20);
	    icon.shadowSize = new GSize(22, 20);
	    icon.iconAnchor = new GPoint(6, 20);
	    icon.infoWindowAnchor = new GPoint(5, 1);

		map = new GMap2(document.getElementById("map"));
		map.addControl(new GLargeMapControl());
		map.addControl(new GMapTypeControl());
		map.enableDoubleClickZoom();
		map.enableContinuousZoom();
		map.enableScrollWheelZoom();
		map.setCenter(new GLatLng(32.901804, -105.960318), 12);

		kmlURL = 'https://hurricane.somedomain/kml.php?search=' + search;
		if(show)
			kmlURL += '&show=' + show;
		exml = new EGeoXml("exml", map, kmlURL, {iwwidth:300,baseicon:icon});
		exml.parse();
	}
}

var findMarker;

function mapInitializeFind(address)
{
	var geocoder = new GClientGeocoder();
	geocoder.getLatLng(address, mapStartFind);
}

function mapStartFind(point)
{
	if (GBrowserIsCompatible())
	{
		if(!point)
			point = new GLatLng(32.901804, -105.960318);
		
		map = new GMap2(document.getElementById("map"));
		map.addControl(new GLargeMapControl());
		map.addControl(new GMapTypeControl());
		map.enableDoubleClickZoom();
		map.enableContinuousZoom();
		map.enableScrollWheelZoom();
		map.setCenter(point, 12);
		
		GEvent.addListener(map, "click", mapPlaceFind);
	}
}

function mapPlaceFind(overlay, point)
{
	map.clearOverlays();

    var icon = new GIcon();
    icon.image = "https://hurricane.somedomain/image/markerWhite.png";
    icon.shadow = "https://hurricane.somedomain/image/markerShadow.png";
    icon.iconSize = new GSize(12, 20);
    icon.shadowSize = new GSize(22, 20);
    icon.iconAnchor = new GPoint(6, 20);
    icon.infoWindowAnchor = new GPoint(5, 1);

	if(point)
	{
		findMarker = new GMarker(new GLatLng(point.y.toFixed(6), point.x.toFixed(6)), {icon:icon});
		map.addOverlay(findMarker);
	}
}

function mapDoneFind(point)
{
	opener.document.getElementById('geocode1').value = point;
	opener.document.getElementById('geocode2').value = point;
}

function suppressEnterKey(e) {
  if (!e) var e = window.event;
  if (e.keyCode) var code = e.keyCode;
  else if (e.which) var code = e.which;
  return code!=13;
}

function confirmDelete() {
  return confirm("Are sure you wish to delete this record?\n\nPress OK to delete, or Cancel to cancel.");
}

function loadEvtHandlers() {
  document.getElementById('input_serial').onkeypress=suppressEnterKey;
  document.getElementById('input_mac').onkeypress=suppressEnterKey;
  return true;
}

function loadConfig(script, suId)
{
	window.open('config.php?id=' + suId + '&script=' + script,'config', 'width=900, height=400, toolbar=1, resizable=1, location=1, scrollbars=1');
}

function loadRadius(script, suId)
{
	window.open('radius.php?id=' + suId + '&script=' + script,'config', 'width=900, height=400, toolbar=1, resizable=1, location=1, scrollbars=1');	
}
