
function newImage(arg) {
	if (document.images) {
		rslt = new Image();
		rslt.src = arg;
		return rslt;
	}
}

function changeImages() {
	if (document.images && (preloadFlag == true)) {
		for (var i=0; i<changeImages.arguments.length; i+=2) {
			document[changeImages.arguments[i]].src = changeImages.arguments[i+1];
		}
	}
}

var preloadFlag = false;
function preloadImages() {
	if (document.images) {
		eb_01_over = newImage("/images/menu/eb_04-over.gif");
		eb_02_over = newImage("/images/menu/eb_05-over.gif");
		eb_03_over = newImage("/images/menu/eb_06-over.gif");
		eb_04_over = newImage("/images/menu/eb_07-over.gif");
		eb_05_over = newImage("/images/menu/eb_08-over.gif");
		eb_06_over = newImage("/images/menu/eb_09-over.gif");
		eb_07_over = newImage("/images/menu/eb_10-over.gif");
		eb_08_over = newImage("/images/menu/eb_11-over.gif");
		eb_09_over = newImage("/images/menu/eb_12-over.gif");
		eb_10_over = newImage("/images/menu/eb_13-over.gif");
		eb_11_over = newImage("/images/menu/eb_14-over.gif");
		eb_12_over = newImage("/images/menu/eb_15-over.gif");
		preloadFlag = true;
	}
}
