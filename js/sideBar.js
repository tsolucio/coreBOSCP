/*
 * coreBOSCP - web based coreBOS Customer Portal
 * Copyright 2012 JPL TSolucio, S.L.   --   This file is a part of coreBOSCP.
 * Licensed under the GNU General Public License (the "License");
 * This file is a modified version of it's equivalent in the Chive Project
 *
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

var sideBar = {

	activate: function(index)
	{
		$('#sideBar').accordion('activate', index);
	},

	loadMenu: function(callback)
	{
		var loadingIcon = $('div.sidebarHeader.schemaList img.loading');
		var contentUl = $('#sideBar #schemaList');

		// Setup loading icon
		loadingIcon.show();

		// Do AJAX request
		$.post(baseUrl + '/site/list', {}, function(data) {

			var template = $('#menutemplate');
			var templateHtml = template.html();
			var html = '';

			// Remove all existing nodes
			contentUl.empty();

			if(data.length > 0)
			{
				// Append all nodes
				for(var i = 0; i < data.length; i++)
				{
					html += '<li class="nowrap">' + templateHtml
						.replace(/{linkName}/g, data[i]['link'])
						.replace(/{iconName}/g, data[i]['icon'])
						.replace(/{menuName}/g, data[i]['name']) + '</li>';
				}

				contentUl.append(html);
				$('#sideBar #schemaList').parent().children('div.noEntries').hide();
			}
			else
			{
				$('#sideBar #schemaList').parent().children('div.noEntries').show();
			}

			// Callback
			if($.isFunction(callback))
			{
				callback();
			}

			// Hide loading icon
			loadingIcon.hide();
		});
	}

};