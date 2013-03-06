<!--
All Emoncms code is released under the GNU Affero General Public License.
See COPYRIGHT.txt and LICENSE.txt.

---------------------------------------------------------------------
Emoncms - open source energy visualisation
Part of the OpenEnergyMonitor project:
http://openenergymonitor.org
-->

<?php

// no direct access
defined('EMONCMS_EXEC') or die('Restricted access');

global $path, $session;
?>
<script type="text/javascript" src="<?php print $path; ?>Lib/flot/jquery.min.js"></script>
<script type="text/javascript" src="<?php print $path; ?>Lib/listjs/list.js"></script>
<script type="text/javascript" src="<?php print $path; ?>Modules/map/widget/map_render.js"></script>
<script type="text/javascript" src="http://d3js.org/d3.v3.js"></script>



<h2><?php echo _("Nodes"); ?></h2>

<?php if ($nodes) { ?>
    
<div id="nodelist"></div>

<script type="text/javascript">

  // The list is created using list.js - a javascript dynamic user interface list creator created as part of this project
  // list.js is still in early development.

  var list =
  {
    'element': "nodelist",
 
    'items': <?php echo json_encode($nodes); ?>,

    'groupby': 'typeid',

    'fields': 
    {
      'id':{}, 
      'hostname':
      {
        'input':"text"
      },
      'comments':
      {
        'input':"text"
      },
      'x':
      {
        'input':"text"
      }, 
      'y':
      {
        'input':"text"
      }, 
      'typeid':
      {
        'format':"select",
        'input':"select", 
        'options':
        {
			1:"emonTx",
			2:"emonBase",
			3:"emonGLCD",
			4:"emonPlug",
			5:"emonMeter",
			6:"Arduino",
			7:"<?php echo _("No type"); ?>"
		}
      },
    },
    
    'actions':{},
    
    'group_prefix': "Type ",

    'path': "<?php echo $path; ?>",
    'controller': "map",
    'listaction': "list",

    'editable': true,
    'deletable': true,
    'restoreable': false,

    'group_properties': {},

    'updaterate': 5000
  };

  listjs(list);
  
  path = "<?php echo $path; ?>";
  apikey = "<?php if ($session['read']) echo get_apikey_read($session['userid']); ?>";
</script>

<?php } else { ?>

<div class="alert alert-block">
<h4 class="alert-heading">No feeds created</h4>
<p>Feeds are where your monitoring data is stored. The recommended route for creating feeds is to start by creating inputs (see the inputs tab). Once you have inputs you can either log them straight to feeds or if you want you can add various levels of input processing to your inputs to create things like daily average data or to calibrate inputs before storage.</p>
</div>

<?php } ?> 

<h2><?php echo _("Node creation and map"); ?></h2>
<div style="background-color:#efefef; margin-bottom:10px; border: 1px solid #ddd; width: 445px; height : 505px;	float :left;">
	<div style="padding:10px; border-top: 1px solid #fff;">
		<form id="addnode" class="form-horizontal" method="get" action="add">
			<legend><?php echo _("Create Node"); ?></legend>
			<div class="control-group">
				<input type="hidden" name="userid" value="<?php echo $_SESSION['userid']; ?>" required autofocus>
				<label class="control-label" for="nodeid">Node id</label>
				<?php if (isset($inputNodes) && count($inputNodes)) { ?>
				<div class="controls">
					<select id="nodeid" name="nodeid" style="width:233px;">
				<?php foreach ($inputNodes as $input) { ?>
						<option><?php echo $input[0]; ?></option>
				<?php } // end foreach ?>
					</select>
				</div>
				<?php  } else {  ?>
				<div class="controls">
					<select id="type" name="type" class="uneditable-input">
						<option></option>
					</select>
				</div>
				<?php  } ?>
			</div>
			<div class="control-group">	
				<label class="control-label" for="hostname">Hostname</label>
				<div class="controls">
					<input id="hostname" name="hostname" type="text" placeholder="host name of the device" style="width:219px;" required>
				</div>
			</div>
			<div class="control-group">
				<label class="control-label" for="comments"><?php echo _("Comments"); ?></label>
				<div class="controls">
					    <textarea id="comments" name="comments" rows="4" placeholder="Comments about the node" style="width:219px;"></textarea>
				</div>
			</div>
			<div class="control-group">
				<label class="control-label" for="y"><?php echo _("Margin top (pixels)"); ?></label>
				<div class="controls">
					<input id="y" name="y" type="text" placeholder="e.g. 10" style="width:219px;" required>
				</div>
			</div>
			<div class="control-group">
				<label class="control-label" for="x"><?php echo _("Margin left (pixels)"); ?></label>
				<div class="controls">
					<input id="x" name="x" type="text" placeholder="e.g. 38" style="width:219px;" required>
				</div>
			</div>
			<div class="control-group">
				<label class="control-label" for="type"><?php echo _("Type"); ?></label>
				<div class="controls">
					    <select id="type" name="type" style="width:233px;">
							<option>emonTx</option>
							<option>emonBase</option>
							<option>emonGLCD</option>
							<option>emonPlug</option>
							<option>emonMeter</option>
							<option>Arduino</option>
							<option><?php echo _("No type"); ?></option>
						</select>
				</div>
			</div>
			 <div class="control-group">
				 <div class="controls">
					 <button type="submit" class="btn"><?php echo _("Add"); ?></button>
				</div>
			</div>
		</form>
	</div>
</div>

<div style="margin-bottom:10px; border: 1px solid #ddd; float :left;  width: 470px; height : 505px; margin-left : 20px;">
	<div style="padding:10px; border-top: 1px solid #fff;">
		<legend><?php echo _("Map"); ?></legend>
		<div class="map"></map>
	</div>
</div>

<script type="text/javascript">
	draw_map();
	
</script>
