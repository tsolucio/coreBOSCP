/*
 * Chive - web based MySQL database management
 * Copyright (C) 2010 Fusonic GmbH
 *
 * This file is part of Chive.
 *
 * Chive is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public
 * License as published by the Free Software Foundation; either
 * version 3 of the License, or (at your option) any later version.
 *
 * Chive is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU
 * General Public License for more details.
 *
 * You should have received a copy of the GNU General Public
 * License along with this library. If not, see <http://www.gnu.org/licenses/>.
 */

var globalPost = {};

function init() 
{
	$('table.list').each(function() {
		var tBody = this.tBodies[0];
		var rowCount = tBody.rows.length;
		var currentClass = 'odd';
		for(var i = 0; i < rowCount; i++)
		{
			if(!tBody.rows[i].className.match('noSwitch') || i == 0)
			{
				if(currentClass == 'even')
				{
					currentClass = 'odd';
				}
				else
				{
					currentClass = 'even';
				}
			}
			tBody.rows[i].className += ' ' + currentClass;
		}
	});
	
}

function deleteRecord(recID,msg,module,controller) {
	if(!confirm(msg)) return false;
	if (controller==undefined || controller == '')
		controller='vtentity';
	$.post(baseUrl + '/' + controller + '/' + module + '/delete/' + recID, {}, AjaxResponse.handle);
	return false;
}

function navigateTo(_url, _post)
{
	globalPost = _post;
	window.location.href = _url;
	
	return false;
}

$(document).ready(function()
{
	// Load sideBar
	var sideBar = $("#sideBar");
	
	$('body').layout({
		
		// General
		applyDefaultStyles: true,

		// North
		north__size: 40,
		north__resizable: false,
		north__closable: false,
		north__spacing_open: 1,

		// West
		west__size: userSettings.sidebarWidth,
		west__initClosed: userSettings.sidebarState == 'closed',
		west__onresize_end: function () {
			sideBar.accordion('resize');
			if($('.ui-layout-west').width() != userSettings.sidebarWidth)
			{
				// Save
				userSettings.sidebarWidth = $('.ui-layout-west').width(); 
				$.post(baseUrl + '/ajaxSettings/set', {
						name: 'sidebarWidth',
						value: $('.ui-layout-west').width()
					}
				);
			}
			return;
		},
		west__onclose_end: function () {
			sideBar.accordion('resize');
			// Save
			$.post(baseUrl + '/ajaxSettings/set', {
					name: 'sidebarState',
					value: 'closed'
				}
			);
			return;
		},
		west__onopen_end: function () {
			sideBar.accordion('resize');
			// Save
			$.post(baseUrl + '/ajaxSettings/set', {
					name: 'sidebarState',
					value: 'open'
				}
			);
			return;
		}
	});
	
	// ACCORDION - inside the West pane
	sideBar.accordion({
		animated: "slide",
		addClasses: false,
		autoHeight: true,
		collapsible: false,
		fillSpace: true,
		selectedClass: "active"
	});
	
	// Trigger resize event for sidebar accordion - doesn't work in webkit-based browsers
	sideBar.accordion('resize');
	
	/*
	 * Change jQuery UI dialog defaults
	 */
	$.ui.dialog.prototype.options.width = 400;
	$.ui.dialog.prototype.options.autoOpen = false;
	$.ui.dialog.prototype.options.modal = true;
	$.ui.dialog.prototype.options.resizable = false;

	/*
	 * Misc
	 */
	chive.init();
	
});

String.prototype.trim = function() {
    return this.replace(/^\s*/, "").replace(/\s*$/, "");
}

String.prototype.startsWith = function(str)
{
	return (this.match("^"+str)==str);
}


/*
 * Language
 */
var lang = {
	
	get: function(category, variable, parameters) 
	{
		var package = lang[category];
		if(package && package[variable])
		{
			variable = package[variable];
			if(parameters)
			{
				for(var key in parameters)
				{
					variable = variable.replace(key, parameters[key]);
				}
			}
		}
		return variable;
	}
	
};

var filedownload = {
	download: function(_url, _data) {
		io = document.createElement('iframe');
		io.src = _url + (_data ? '?' + $.param(_data) : '');
		io.style.display = 'block';
		io = $(io);
		$('body').append(io);
		setTimeout(function() {
			io.remove();
		}, 15000);
	}
};

function sendtopaygateway(cypid) {
	// create form with data values and send
	var url = 'https://your.corebos.tld/Payment.php';
	var data = {
		'cpid': cypid,
		'returnUrl': 'https://your.coreboscp.tld/index.php#site/ThankYouForPayment',
		'cancelUrl': 'https://your.coreboscp.tld/index.php#site/ErrorInPayment'
	};
	var form = $('<form id="paypal-form" action="' + url + '" method="POST"></form');

	for(x in data){
		form.append('<input type="hidden" name="'+x+'" value="'+data[x]+'" />');
	}
	
	// append form
	$('body').append(form);
	
	// submit form
	$('#paypal-form').submit();

}