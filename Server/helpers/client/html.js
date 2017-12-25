/*
	CAESAR

	Author : Michele '0blio' Cisternino
	Email  : miki.cisternino@gmail.com
	Github : https://github.com/0blio
	
	This project is released under the GPL 3 license. 	
*/


// Escape a string in HTML
function escape_html (string) {
	var entity_map = {
		'&': '&amp;',
		'<': '&lt;',
		'>': '&gt;',
		'"': '&quot;',
		"'": '&#39;',
		'/': '&#x2F;',
		'`': '&#x60;',
		'=': '&#x3D;'
	};

	return String(string).replace(/[&<>"'`=\/]/g, function (s) {
		return entity_map[s];
	});
}

function preserve_formatting (string) { return "<pre>" + string + "</pre>"; }
