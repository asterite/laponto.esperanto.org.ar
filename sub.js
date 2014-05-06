function xAlUtf8(x) {
	cursorPos = getSelectionStart(x);
	
	t=x.value;
    t = t.replace(/c[xX]/g, "\u0109");
    t = t.replace(/g[xX]/g, "\u011d");
    t = t.replace(/h[xX]/g, "\u0125");
    t = t.replace(/j[xX]/g, "\u0135");
    t = t.replace(/s[xX]/g, "\u015d");
    t = t.replace(/u[xX]/g, "\u016d");
    t = t.replace(/C[xX]/g, "\u0108");
    t = t.replace(/G[xX]/g, "\u011c");
    t = t.replace(/H[xX]/g, "\u0124");
    t = t.replace(/J[xX]/g, "\u0134");
    t = t.replace(/S[xX]/g, "\u015c");
    t = t.replace(/U[xX]/g, "\u016c");
    if (t != x.value) {
      x.value = t;
      setSelectionRange(x, cursorPos - 1, cursorPos - 1);
    }
}

function kvazauxElsaluti() {
	document.getElementById('l').src = 'kvazaux_elsaluti.php';
}

function montriPasvorton() {
	var x = document.getElementById('repetirClave').style.display = '';
	var x = document.getElementById('repetirClave2').style.display = '';
}

function kasxiPasvorton() {
	var x = document.getElementById('repetirClave').style.display = 'none';
	var x = document.getElementById('repetirClave2').style.display = 'none';
}

function montriProfilon(id) {
	openCenteredWindow('fenestro_profilo.php?id=' + id, 'profilo', 400, 400, 'scrollbars=yes, resizable=yes');
}

function respondiMesagxon(id, eventId) {
	openCenteredWindow('fenestro_profilo.php?id=' + id + '&eventId=' + eventId, 'profilo', 400, 400, 'scrollbars=yes, resizable=yes');
}

function montriNovanMesagxon() {
	openCenteredWindow('fenestro_nova_mesagxo.php', 'nova_mesagxo', 500, 160);
}

function sxangxiRegulojn() {
	document.getElementById('rules_text').style.display = 'none';
	document.getElementById('rules_form').style.display = '';
}

function forvisxiElektitajn() {
	document.getElementById('theMessages').submit();
}

function malfermiRapidanSercxon() {
	openCenteredWindow('', 'rapida_sercxo', 500, 400, 'scrollbars=yes, resizable=yes');
	return true;
}

function malfariSxangxojn(id, message, film_id) {
	if (confirm(message)) {
		window.location = 'malfari_sxangxojn.do.php?film_id=' + film_id + '&user_id=' + id;
	}
}

function blokiUzanton(id, message, film_id) {
	if (confirm(message)) {
		window.location = 'bloki_uzanton.do.php?film_id=' + film_id + '&user_id=' + id;
	}
}

function malblokiUzanton(id, message, film_id) {
	if (confirm(message)) {
		window.location = 'bloki_uzanton.do.php?film_id=' + film_id + '&user_id=' + id;
	}
}

function montriLiterkodhelpon() {
	openCenteredWindow('fenestro_literkodhelpo.php', 'literkodhelpo', 400, 320, 'scrollbars=yes, resizable=yes');
}

function montriPublikhelpon() {
	openCenteredWindow('fenestro_publikhelpo.php', 'publikhelpo', 400, 150, 'scrollbars=yes, resizable=yes');
}

function aperigiKomentojn(num) {
	var x = document.getElementById('fc' + num);
	x.style.display = '';
	var y = -x.clientHeight - 23;
	x.style.top = y + 'px';
}

function kasxiKomentojn(num) {
	var x = document.getElementById('fc' + num);
	x.style.display = 'none';
}

function sxangxiLingvon(context_path, lingvo) {
  window.location = context_path + '/sxangxi_lingvon.do.php?lingvo=' + lingvo;
}

function openCenteredWindow(url, name, width, height, features) {
  if(screen.width){
	  var winl = (screen.width-width)/2;
	  var wint = (screen.height-height)/2;
  } else {
		winl = 0;
		wint =0;
  }
  if (winl < 0) winl = 0;
  if (wint < 0) wint = 0;
  var settings = 'height=' + height + ',';
  settings += 'width=' + width + ',';
  settings += 'top=' + wint + ',';
  settings += 'left=' + winl + ',';
  settings += features;
  win = window.open(url, name, settings);
  win.window.focus();
}

// ----------------------------------------------------
// http://www.bazon.net/mishoo/articles.epl?art_id=1292
// ----------------------------------------------------

var is_gecko = /gecko/i.test(navigator.userAgent);
var is_ie    = /MSIE/.test(navigator.userAgent);

function setSelectionRange(input, start, end) {
	if (is_gecko) {
		input.setSelectionRange(start, end);
	} else {
		// assumed IE
		var range = input.createTextRange();
		range.collapse(true);
		range.moveStart("character", start);
		range.moveEnd("character", end - start);
		range.select();
	}
};

function getSelectionStart(input) {
	if (is_gecko)
		return input.selectionStart;
	var range = document.selection.createRange();
	var isCollapsed = range.compareEndPoints("StartToEnd", range) == 0;
	if (!isCollapsed)
		range.collapse(true);
	var b = range.getBookmark();
	return b.charCodeAt(2) - 2;
};

function getSelectionEnd(input) {
	if (is_gecko)
		return input.selectionEnd;
	var range = document.selection.createRange();
	var isCollapsed = range.compareEndPoints("StartToEnd", range) == 0;
	if (!isCollapsed)
		range.collapse(false);
	var b = range.getBookmark();
	return b.charCodeAt(2) - 2;
};