	subHover = function()
	{
		var subEls = document.getElementById("nav").getElementsByTagName("LI");
		
		for (var i=0; i<subEls.length; i++)
		{
			subEls[i].onmouseover=function()
			{
				this.className+=" subhover";
			};
			
			subEls[i].onmouseout=function()
			{
				this.className=this.className.replace(new RegExp("subhover\\b"), "");
			};
		}
	};
	
	if (window.attachEvent)
	{
		window.attachEvent("onload", subHover);
	}
