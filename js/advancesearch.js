/*
 *************************************************************************************************
 * vtigerCRM vtyiiCPng - web based vtiger CRM Customer Portal
 * Copyright 2012 JPL TSolucio, S.L.  --  This file is a part of vtyiiCPNG.
 * You can copy, adapt and distribute the work under the "Attribution-NonCommercial-ShareAlike"
 * Vizsage Public License (the "License"). You may not use this file except in compliance with the
 * License. Roughly speaking, non-commercial users may share and modify this code, but must give credit
 * and share improvements. However, for proper details please read the full License, available at
 * http://vizsage.com/license/Vizsage-License-BY-NC-SA.html and the handy reference for understanding
 * the full license at http://vizsage.com/license/Vizsage-Deed-BY-NC-SA.html. Unless required by
 * applicable law or agreed to in writing, any software distributed under the License is distributed
 * on an  "AS IS" BASIS, WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and limitations under the
 * License terms of Creative Commons Attribution-NonCommercial-ShareAlike 3.0 (the License).
 *************************************************************************************************
 *  Author       : JPL TSolucio, S. L.
 */

var typeofdata = new Array();
typeofdata['V'] = ['e','n','s','z','c','k'];
typeofdata['N'] = ['e','n','l','g','m','h'];
//typeofdata['NN'] = ['e','n','l','g','m','h'];
typeofdata['T'] = ['e','n','l','g','m','h','b','a'];
typeofdata['I'] = ['e','n','l','g','m','h'];
typeofdata['C'] = ['e','n'];
typeofdata['D'] = ['e','n','l','g','m','h','b','a'];
//typeofdata['DT'] = ['e','n','l','g','m','h','b','a'];
typeofdata['E'] = ['e','n','s','z','c','k'];

var fLabels = new Array();
fLabels['e'] = lang.get('core','equals');
fLabels['n'] = lang.get('core','not equal to');
fLabels['s'] = lang.get('core','starts with');
fLabels['z'] = lang.get('core','ends with');
fLabels['c'] = lang.get('core','contains');
fLabels['k'] = lang.get('core','does not contain');
fLabels['l'] = lang.get('core','less than');
fLabels['g'] = lang.get('core','greater than');
fLabels['m'] = lang.get('core','less or equal');
fLabels['h'] = lang.get('core','greater or equal');
fLabels['b'] = lang.get('core','before');
fLabels['a'] = lang.get('core','after');

var noneLabel = lang.get('core','none');

function updatefOptions(sel) {
	var sops = $(sel).closest('td').next('td').find('select');
	// first we empty the select
	sops.find('option').remove();
	// then add default none
	sops.append($('<option></option>').val('').html(noneLabel));
	// finally add all the rest of valid values
	$.each(typeofdata[$(sel).find('option:selected').attr('fieldtype')], function(index, val) {
		sops.append($('<option></option>').val(val).html(fLabels[val]));
	});
}