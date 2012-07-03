/*
 * vtigerCRM vtyiiCPng - web based vtiger CRM Customer Portal
 * Copyright (C) 2011-2012 Opencubed shpk: JPL TSolucio, S.L./StudioSynthesis, S.R.L.
 *
 * This file is part of vtyiiCPng.
 *
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0 ("License")
 * You may not use this file except in compliance with the License
 * The Original Code is:  Opencubed Open Source
 * The Initial Developer of the Original Code is Opencubed.
 * Portions created by Opencubed are Copyright (C) Opencubed.
 * All Rights Reserved.
 *
 * vtyiiCPng is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY;
 * without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
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