function nxs_js_getwidth() 
{
  return Math.max(
    document.body.scrollWidth,
    document.documentElement.scrollWidth,
    document.body.offsetWidth,
    document.documentElement.offsetWidth,
    document.documentElement.clientWidth
  );
}

function nxs_js_getviewports()
{
	var result = 
	[
	320, 		// iphone portrait
	480, 		// 
	720, 		// 
	960, 		// ipad landscape / desktop
	1200, 	// desktop
	1440		// desktop
	];	
	return result;
}

function nxs_js_deriveactiveviewport()
{
	var widthofbrowserwindow = nxs_js_getwidth();
	var viewports = nxs_js_getviewports();
	var biggest = 0;
	for (var i = 0; i < viewports.length; i++)
	{
		var currentviewport = viewports[i];
		if (currentviewport >= biggest)
		{
			if (widthofbrowserwindow >= currentviewport)
			{
				biggest = currentviewport;
			}
		}
	}
	return biggest;
}

function nxs_js_tagviewports()
{
	var element = document.body.parentNode;	// html element
	
	var widthofbrowserwindow = nxs_js_getwidth();
	
	var viewports = nxs_js_getviewports();
	for (var i = 0; i < viewports.length; i++)
	{
		var currentviewport = viewports[i];
		
		// greater than classes
		var compareto = currentviewport - 1;
		var classname = "nxs-viewport-gt-" + compareto;
		if (widthofbrowserwindow >= compareto)
		{
			element.classList.add(classname);
		}
		else
		{
			element.classList.remove(classname);
		}
		
		// less than or equal classes
		var compareto = currentviewport - 1;
		var classname = "nxs-viewport-lte-" + compareto;
		if (widthofbrowserwindow <= compareto)
		{
			element.classList.add(classname);
		}
		else
		{
			element.classList.remove(classname);
		}
		
		// active viewport
		var compareto = currentviewport;
		var classname = "nxs-viewport-is-" + compareto;
		element.classList.remove(classname);
		
		// the adding of the active class happens below
	}
	
	// equal classes
	var classname = "nxs-viewport-is-" + nxs_js_deriveactiveviewport();
	element.classList.add(classname);
}

document.addEventListener("DOMContentLoaded", function() {
  // tag it firs the first time
	nxs_js_tagviewports();

	window.addEventListener
	(
		'resize', function(event)
		{
			// todo: are throttling
		  nxs_js_tagviewports();
		}
	);
});


function nxs_js_menu_mini_expand_v2(obj, placeholderid, action) 
{
	if (action == "toggle")
	{
		//show the collapser
		var id = "a_nav_collapser_" + placeholderid;
		var e = document.getElementById(id);
		console.log(e);
		if (e == null)
		{
		}
		else
		{
			if (e.style.display == "none")
			{
				e.style.display = "block";
				
				var id = "icon_nav_" + placeholderid;
				var e = document.getElementById(id);
				e.style.transition = "0.4s";
				e.style.transform = "rotate(180deg)";
			}
			else if (e.style.display == "block")
			{
				e.style.display = "none";
	
				var id = "icon_nav_" + placeholderid;
				var e = document.getElementById(id);
				e.style.transition = "0.4s";
				e.style.transform = "rotate(-180deg)";
			}		
		}
	}
	return false;
}