<!-- 
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
-->

<table class="list" style="width: 50%; float: left;">
	<colgroup>
		<col style="width: 300px;"></col>
		<col></col>
	</colgroup>
	<thead>
		<tr>
			<th colspan="2"><?php echo Yii::t('core', 'softwareInformation'); ?></th>
		</tr>
	</thead>
	<tbody>
		<tr>
			<td style="padding-left: 15px;">
				<a href="http://www.evolutivo.it">
					<img src="images/logo-big.png" alt="evolutivo logo" title="<?php echo Yii::app()->name; ?>" />
				</a><br/>
				<p class="login-text"><?php echo Yii::t('core', 'customerPortal'); ?></p>
			</td>
			<td>
				<b><?php echo Yii::app()->name . ' ' . Yii::app()->params->version; ?></b><br/>
				<i>Webservice Customer Portal for coreBOS</i><br/><br/><br/>
				Released under the <a href="http://www.gnu.org/licenses/gpl-3.0-standalone.html">GNU GPL v3</a><br/>
				<a href="http://www.tsolucio.com">&copy; JPL TSolucio, S.L.</a><br/><br/>
			</td>
		</tr>
	</tbody>
</table>
<table class="list" style="width: 49%; float: right;">
	<thead>
		<tr>
			<th colspan="2"><?php echo Yii::t('core', 'maintainer'); ?></th>
		</tr>
	</thead>
	<tbody>
		<tr>
			<td style="padding-left: 15px;padding-right: 15px;">
				<a href="http://www.evolutivo.it">
					<img src="images/evolutivo.png" alt="Evolutivo" title="Evolutivo" />
				</a>
			</td>
			<td>
				<?php echo Yii::app()->name; ?> is an <b><a href="http://www.evolutivo.it">Evolutivo Initiative</a></b><br/>
			</td>
		</tr>
		<tr><td colspan=2>Which means it is a joint venture project of the companies: OpenCubed. JPL TSolucio, S.L. and StudioSynthesis, S.R.L.</td></tr>
	</tbody>
</table>

<div class="clear"></div>
<br/>

<table class="list">
	<colgroup>
		<col style="width: 200px;"></col>
		<col></col>
		<col style="width: 150px;"></col>
	</colgroup>
	<thead>
		<tr>
			<th colspan="3"><?php echo Yii::t('core', 'usedLibraries'); ?></th>
		</tr>
	</thead>
	<tbody>
		<tr class="even">
			<td>
				<a href="http://www.chive-project.com/" class="icon">
					<?php echo Html::icon('chive'); ?>
					<span>Chive Project 0.5</span>
				</a>
			</td>
			<td>Chive is a next generation MySQL database management tool. We have based the Customer Portal design and some of the under works on the great work <a href="http://www.fusonic.net">Fusonic</a> is doing on this project.<br/><b>Thanks for sharing!!!</b><br/>
			<b>Fusonic GmbH</b>	<a href="http://www.fusonic.net">http://www.fusonic.net</a>
			</td>
			<td>GPL</td>
		</tr>
		<tr class="odd">
			<td>
				<a href="http://corebos.org/" class="icon">
					<?php echo Html::icon('corebos'); ?>
					<span>coreBOS</span>
				</a>
			</td>
			<td>coreBOS is a community-driven, fully open source, CRM software with a worldwide community of developers and user. This customer portal uses the coreBOS Web Services API to connect to a running install and permits your clients to communicate and collaborate with your company.</td>
			<td>VPL</td>
		</tr>
	
		<tr class="even">
			<td>
				<a href="http://www.yiiframework.com" class="icon">
					<?php echo Html::icon('yii'); ?>
					<span>Yii 1.1.12</span>
				</a>
			</td>
			<td>Yii is a high-performance component-based PHP framework best for developing large-scale Web applications.</td>
			<td>BSD</td>
		</tr>
		<tr class="odd">
			<td>
				<a href="http://jquery.com" class="icon">
					<?php echo Html::icon('jquery'); ?>
					<span>jQuery 1.7.2</span>
				</a>
			</td>
			<td>jQuery is a fast and concise JavaScript Library that simplifies HTML document traversing, event handling, animating, and Ajax interactions for rapid web development.</td>
			<td>MIT, GPL</td>
		</tr>
		<tr class="even">
			<td>
				<a href="http://jqueryui.com" class="icon">
					<?php echo Html::icon('jqueryui'); ?>
					<span>jQuery UI 1.8.22</span>
				</a>
			</td>
			<td>jQuery UI provides abstractions for low-level interaction and animation, advanced effects and high-level, themeable widgets, built on top of the jQuery JavaScript Library, that you can use to build highly interactive web applications.</td>
			<td>MIT, GPL</td>
		</tr>
		<tr class="odd">
			<td>
				<a href="http://layout.jquery-dev.net/" class="icon">
					<?php echo Html::icon('globe'); ?>
					<span>jQuery UI.Layout 1.2.0</span>
				</a>
			</td>
			<td>The UI.Layout plug-in can create any UI look you want - from simple headers or sidebars, to a complex application with toolbars, menus, help-panels, status bars, sub-forms, etc.</td>
			<td>MIT, GPL</td>
		</tr>
		<tr class="even">
			<td>
				<a href="http://malsup.com/jquery/form/" class="icon">
					<?php echo Html::icon('globe'); ?>
					<span>jQuery Form 2.84</span>
				</a>
			</td>
			<td>The jQuery Form Plugin allows you to easily and unobtrusively upgrade HTML forms to use AJAX. The main methods, ajaxForm and ajaxSubmit, gather information from the form element to determine how to manage the submit process. Both of these methods support numerous options which allows you to have full control over how the data is submitted. Submitting a form with AJAX doesn't get any easier than this!</td>
			<td>MIT, GPL</td>
		</tr>
		<tr class="odd">
			<td>
				<a href="http://onehackoranother.com" class="icon">
					<?php echo Html::icon('tipsy'); ?>
					<span>Facebook-style tooltip plugin for jQuery 0.1.4</span>
				</a>
			</td>
			<td>Tipsy is a jQuery plugin for creating a Facebook-like tooltips effect based on an anchor tag's title attribute.</td>
			<td>MIT</td>
		</tr>
		<tr class="even">
			<td>
				<a href="http://malsup.com/jquery/block/" class="icon">
					<?php echo Html::icon('globe'); ?>
					<span>jQuery BlockUI 2.31</span>
				</a>
			</td>
			<td>The jQuery BlockUI Plugin lets you simulate synchronous behavior when using AJAX, without locking the browser. When activated, it will prevent user activity with the page (or part of the page) until it is deactivated. BlockUI adds elements to the DOM  to give it both the appearance and behavior of blocking user interaction.</td>
			<td>MIT, GPL</td>
		</tr>
		<tr class="odd">
			<td>
				<a href="https://github.com/ricardovaleriano/jquery.hotkeys" class="icon">
					<?php echo Html::icon('globe'); ?>
					<span>jQuery Hotkeys 0.8.1 Ricardo Valeriano</span>
				</a>
			</td>
			<td>jQuery Hotkeys plugin lets you easily add and remove handlers for keyboard events anywhere in your code supporting almost any key combination. It takes one line of code to bind/unbind a hot key combination. </td>
			<td>MIT, GPL</td>
		</tr>
		<tr class="even">
			<td>
				<a href="http://code.google.com/p/jquery-purr/" class="icon">
					<?php echo Html::icon('globe'); ?>
					<span>jQuery Purr 0.1.0</span>
				</a>
			</td>
			<td>Purr is a jQuery plugin for dynamically displaying unobtrusive messages in the browser. It is designed to behave much as the Mac OS X program "Growl".</td>
			<td>MIT</td>
		</tr>
		<tr class="odd">
			<td>
				<a href="http://bassistance.de/jquery-plugins/jquery-plugin-autocomplete/" class="icon">
					<?php echo Html::icon('bassistance'); ?>
					<span>jQuery Autocomplete 1.1</span>
				</a>
			</td>
			<td>Autocomplete an input field to enable users quickly finding and selecting some value, leveraging searching and filtering.</td>
			<td>MIT, GPL</td>
		</tr>
		<tr class="even">
			<td>
				<a href="http://www.appelsiini.net/projects/jeditable" class="icon">
					<?php echo Html::icon('globe'); ?>
					<span>Jeditable 1.7.1</span>
				</a>
			</td>
			<td>Edit in place plugin for jQuery.</td>
			<td>MIT</td>
		</tr>
		<tr class="odd">
			<td>
				<a href="http://www.texotela.co.uk/code/jquery/select/" class="icon">
					<?php echo Html::icon('globe'); ?>
					<span>jQuery Select 2.2.4</span>
				</a>
			</td>
			<td>Select box manipulation plugin for jQuery.</td>
			<td>MIT, GPL</td>
		</tr>
	</tbody>
</table>

<script type="text/javascript">
	breadCrumb.add({ icon: 'info', href: 'javascript:chive.goto(\'information/about\')', text: '<?php echo Yii::t('core', 'about'); ?>'});
	breadCrumb.show();
</script>