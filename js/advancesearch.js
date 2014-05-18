/*************************************************************************************************
 * coreBOSCP - web based coreBOS Customer Portal
 * Copyright 2011-2014 JPL TSolucio, S.L.   --   This file is a part of coreBOSCP.
 * Licensed under the GNU General Public License (the "License") either
 * version 3 of the License, or (at your option) any later version; you may not use this
 * file except in compliance with the License. You can redistribute it and/or modify it
 * under the terms of the License. JPL TSolucio, S.L. reserves all rights not expressly
 * granted by the License. coreBOSCP distributed by JPL TSolucio S.L. is distributed in
 * the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied
 * warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. Unless required by
 * applicable law or agreed to in writing, software distributed under the License is
 * distributed on an "AS IS" BASIS, WITHOUT ANY WARRANTIES OR CONDITIONS OF ANY KIND,
 * either express or implied. See the License for the specific language governing
 * permissions and limitations under the License. You may obtain a copy of the License
 * at <http://www.gnu.org/licenses/>
 *************************************************************************************************
 *  Author       : JPL TSolucio, S. L.
 *************************************************************************************************/

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