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

var chive = {
	
	currentLocation: 	window.location.href,
	
	// Turn loading indicator on by default
	loadingIndicator: 	true,
	
	
	/*
	 * Initialize chive
	 */
	init: function()
	{
		// Initialize location checker
		setInterval(chive.checkLocation, 100);
		
		// Load first page if anchor is set
		if(chive.currentLocation.indexOf('#') > -1)
		{
			chive.refresh();
		}
		
		// Set keyboard shortcuts for Yii pager
		$(document)
		.bind('keydown', {disableInInput: true }, function(e) {
			if (e.which==35 || e.which==36 || e.which==37 || e.which==39) {
				switch (e.which) {
					case 35: // end 
						var li = $('ul.yiiPager li').last('li');
						break;
					case 36: // start 
						var li = $('ul.yiiPager li').first('li');
						break;
					case 37: // left 
						var li = $('ul.yiiPager li.selected').prev('li.page');
						break;
					case 39: // right 
						var li = $('ul.yiiPager li.selected').next('li.page');
						break;
				}
				if(li.length > 0)
				{
					//location.href = li.children('a').attr('href');
					li.children('a').click();
				}
			}
		});
	
		// Send keep-alive to server every 5 minutes
		if(!location.href.indexOf('login'))
		{
			setInterval(function() {
				$.post(baseUrl + '/site/keepAlive', function(response) {
					if(response != 'OK') 
					{
						reload();
					}
				});
			}, 300000);
		}
                
		if($('#globalSearch').length)
		{
			jQuery('#globalSearch').legacyautocomplete(baseUrl + '/site/search', {
				width:		400,
				formatItem: function(item, position, total, item2) {
					item = JSON.parse(item2);
					return item.text;
				},
				formatResult: function(item, position, total) {
					item = JSON.parse(item.pop());
					return item.plain;
				}
				}).result(function(event, position, item) {
					item = JSON.parse(item);
					window.location = item.target;
				});
		}
		
		// Initialize loading indicator
		$(document)
			.ajaxStart(function() {
				if(this.loadingIndicator)
				{
					$('#loading').css({'background-image': 'url(images/loading4.gif)'}).fadeIn();
				}
			})
			.ajaxStop(function() {
				$('#loading').css({'background-image': 'url(images/loading5.gif)'}).fadeOut();
			})
			.ajaxError(function(error, xhr) {
				if (xhr.statusText!='abort') {  // si hemos abortado la llamada ajax no mostrar error
				Notification.add('ajaxerror', lang.get('core', 'ajaxRequestFailed'), lang.get('core', 'ajaxRequestFailedText'), xhr.responseText);
				$('#loading').css({'background-image': 'url(images/loading5.gif)'}).fadeOut();
				}
			});

	},
	
	/*
	 * Loads the specified page.
	 */
	goto: function(location)
	{
		globalPost = {};
		window.location.hash = location;
		chive.currentLocation = window.location.href;
		chive.refresh();
	},
	
	/*
	 * Refreshes the current page using the anchor name.
	 */
	refresh: function()
	{	
		// Build url
		
		var url = chive.currentLocation
			.replace(/\?(.+)#/, '')
			.replace('#', '/')					// Replace # with /
			.replace(/([^:])\/+/g, '$1/');		// Remove multiple slashes
		chive.ajaxloading(0,0);
		// Load page into content area
		$.post(url, globalPost, function(response) {
			if(!AjaxResponse.handle(response))
			{
				var content = document.getElementById('content');
				response = '<div style="display: none">IE8 requires this dirty hack</div>' + response;
				content.innerHTML = response;
				var scripts = content.getElementsByTagName('script');
				for(var i = 0; i < scripts.length; i++)
				{
					$.globalEval(scripts[i].innerHTML);
				}
				init();
			}
			var globalPost = {};
			chive.ajaxloaded(0,0);
		});
	},
	
	ajaxloading: function(id, data)
	{	
		$('#loading').css({'background-image': 'url(images/loading4.gif)'}).fadeIn();
	},
	ajaxloaded: function(id, data)
	{	
		$('#loading').css({'background-image': 'url(images/loading5.gif)'}).fadeOut();
	},

	/*
	 * Reloads the whole page.
	 */
	reload: function()
	{
		window.location.reload();
	},
	
	/*
	 * Checks if current location has changed.
	 */
	checkLocation: function()
	{
		if(chive.currentLocation.indexOf('index.php') == -1) {
			window.location.href=window.location.href+'index.php';
		}
		if(window.location.href != chive.currentLocation) 
		{
			chive.currentLocation = window.location.href;
			chive.refresh();
		}
	}
	
};