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

var breadCrumb = {
	
	breadcrumbTrail: new Array(),
	breadcrumbTrailSize: 6,
	textMaxLength: 15,

	set: function(data)
	{
		var ul = $('#headerLeft');
		
		// Unset current breadcrumb
		ul.children('a.dynamicCrumb').remove();
		
		// Check if data is array
		if(!$.isArray(data) || data.length==0)
		{
			return;
		}

		var windowTitle = [];
		
		// Create new breadCrumbs
		for(var i = 0; i < data.length; i++)
		{			
			var html = '<a href="' + data[i].href + '"' + (data[i].icon ? ' class="icon button dynamicCrumb"' : '') + '>';
			
			// Add icon
			if(data[i].icon)
			{
				html += '<img src="' + iconPath + '/16/' + data[i].icon + '.png" class="icon icon16 icon_' + data[i].icon + '" width="16" height="16" />';
			}
			
			// Text
			html += '<span>' + data[i].text.substring(0,11) + '</span>';
			
			html += '</a>';
			
			ul.append(html);
			
			windowTitle.push(data[i].text);
		}

		// Set window title
		// change Chive behaviour, we put just module and App Name
		document.title = data[data.length-1].text + ' » ' + ' coreBOSCP';
	},

	add: function(element)
	{
		// if it is already in the array don't add it
		found=false;
		idx=0;
		while (!found && idx<this.breadcrumbTrail.length) {
			found=this.breadcrumbTrail[idx].href==element.href;
			idx++;
		}
		if (!found) {
			if (element.text.length>this.textMaxLength)
				element.text=element.text.substring(0, this.textMaxLength);
			bcsize = this.breadcrumbTrail.push(element);
			if (bcsize > this.breadcrumbTrailSize)
				delete this.breadcrumbTrail.splice(0,1);
		}
	},

	get: function()
	{
		return this.breadcrumbTrail;
	},

	show: function()
	{
		this.set(this.breadcrumbTrail);
	}

};