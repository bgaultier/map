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

global $user, $path, $session;
?>
<script type="text/javascript" src="<?php print $path; ?>Modules/map/map.js"></script>
<script type="text/javascript" src="<?php print $path; ?>Lib/tablejs/table.js"></script>
<script type="text/javascript" src="<?php print $path; ?>Modules/map/widget/map_render.js"></script>
<script type="text/javascript" src="http://d3js.org/d3.v3.js"></script>



<h2><?php echo _("Nodes"); ?></h2>

<?php if ($nodes) { ?>
    
<div id="nodelist"></div>

<script type="text/javascript">

  path = "<?php echo $path; ?>";
  apikey = "<?php if ($session['read']) echo $user->get_apikey_read($session['userid']); ?>";

  // The list is created using list.js - a javascript dynamic user interface list creator created as part of this project
  // list.js is still in early development.

  table.element = "#nodelist";

  table.fields = {
    'id':{'title':"<?php echo _('Id'); ?>", 'type':"fixed"},
    'hostname':{'title':"<?php echo _('hostname'); ?>", 'type':"text"},
    'comments':{'title':"<?php echo _('comments'); ?>", 'type':"text"},
    'x':{'title':"<?php echo _('x'); ?>", 'type':"text"},
    'y':{'title':"<?php echo _('y'); ?>", 'type':"text"},
    'typeid':{'title':"<?php echo _('typeid'); ?>", 'type':"select", 'options':['','emonTx','emonBase','emonGLCD','emonPlug','emonMeter','Arduino','No type']},

    // Actions
    'edit-action':{'title':'', 'type':"edit"},
    'delete-action':{'title':'', 'type':"delete"}
  }

  table.groupby = 'typeid';

  table.data = map.list();
  table.draw();

  $("#nodelist").bind("onSave", function(e,id,fields_to_update){
    map.set(id,fields_to_update); 
  });

  $("#nodelist").bind("onDelete", function(e,id,row){
    map.delete(id);
  });

</script>

<?php } else { ?>

<script>
  path = "<?php echo $path; ?>";
  apikey = "<?php if ($session['read']) echo $user->get_apikey_read($session['userid']); ?>";
</script>

<div class="alert alert-block">
<h4 class="alert-heading">No nodes created</h4>
<p><?php echo _("Nodes are used to represent the real sensors on a map. The recommended route for creating nodes is to start by creating inputs and feeds. Then, you will have to create a node using the form below."); ?>
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
				<?php foreach ($inputNodes as $item) { ?>
						<option><?php echo $item; ?></option>
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
							<option >emonTx</option>
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
