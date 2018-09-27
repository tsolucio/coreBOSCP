<?php
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

//This is the coreBOS server path i.e., the url to access the coreBOS server in browser
//Ex. if you access your coreBOS with http://joeshome:90/corebos/index.php you will use http://joeshome:90/corebos
$evocp_Server_Path = "http://localhost/coreBOSwork";

// The next two variables define the portal webservice user to be used to connect to the coreBOS server
// This user must be created and configured in your coreBOS application
$evocp_Login_User = 'joe';
$evocp_Access_Key = 'aocJtA9oF8kl1p1g';

// name of the folder within which you want any trouble ticket attachments to be uploaded
$evocp_AttachmentFolderName = 'Default';

// If true, portal shows all the tickets related to all contacts on the Company
$evocp_CompanyTickets = false;
?>
