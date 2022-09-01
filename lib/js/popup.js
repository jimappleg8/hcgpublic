
	var putItThere = null;
	
	var chasm = screen.availWidth;
	var mount = screen.availHeight;

	function popup (page, poptype) {
			
		var windowprops = "location=no,toolbar=no,menubar=no,scrollbars=yes,resizable=yes";
	
		if (poptype == "caffeine") {
			w = 380;
			h = 300;
		}
		else if (poptype == "nutrition") {
			w = 300;
			h = 425;
		}
		else if (poptype == "herb") {
			w = 450;
			h = 300;
		}
		else if (poptype == "picture") {
			w = 450;
			h = 450;
		}
		else if (poptype == "preview") {
			w = 500;
			h = 550;
		}
		if (document.layers) {
		putItThere = window.open(page,'','width=' + w + ',height=' + h + ',' + windowprops);
		} else {
		putItThere = window.open(page,'','width=' + w + ',height=' + h + ',left=' + ((chasm - w - 10) * .5) + ',top=' + ((mount - h - 30) * .5) + ',' + windowprops);
		}
	}

