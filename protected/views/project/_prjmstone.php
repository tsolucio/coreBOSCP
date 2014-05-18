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
?>
<table class="list">
<colgroup>
<col style="width: 30%;"></col>
<col style="width: 55%;"></col>
<col style="width: 15%;"></col>
</colgroup>
<thead>
<tr>
<th colspan="3"><?php echo $modname; ?></th>
</tr>
</thead>
<tbody>
<?php
$i=0;
if (is_array($relPrj))
foreach ($relPrj as $prrow) {
	echo '<tr class="'.($i % 2 == 0 ? 'even' : 'odd').'">
	<td>'.CHtml::link($prrow['projectmilestonename'],"index.php#vtentity/ProjectMilestone/view/".$prrow['id']).'</td>
	<td>'.$prrow['projectmilestonedate'].'</td>
	<td>'.$prrow['projectmilestonetype'].'</td>
	</tr>';
	$i++;
} ?>
</tbody>
</table>