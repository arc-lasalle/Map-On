
<div id="viewbox_<?php echo str_replace(":", "--",$class);?>" class="ui secondary segment" onmouseover="hoverLabelClass('<?php echo $class;?>', '<?php echo $table;?>');" onMouseOut="outLabelClass('<?php echo $class;?>', '<?php echo $table;?>');">
	
	
	
	<div class="ui top left pointing dropdown lmcDropdown">
		<div class="ui tiny orange circular image label" onclick="toogleLabelClass(<?php echo $mappedclass_id;?>, '<?php echo $table;?>');" style="cursor:pointer;"  title = "<?php echo $description;?>">
		  <?php echo $class;?>
		</div>
		<div class="ui tiny purple circular image label" >
		  <?php echo $table;?>
		</div>
	
		<div class="menu">
			<a class="item" href="<?php echo base_url();?>index.php/mappedclass/createnew/<?php echo $datasource_id."/".$mappedspace_id."/".$mappedclass_id; ?>"><i class="edit purple icon"></i>Edit mapping</a>
		
			<a class="item" style=" text-align=left;" href="<?php echo base_url();?>index.php/mappeddataproperty/addnew/<?php echo $datasource_id."/".$mappedspace_id."/".$mappedclass_id; ?>"><i class="add circle purple  icon" ></i>Create data property</a>

			<a class="item" style=" text-align=left;" href="<?php echo base_url();?>index.php/mappedobjectproperty/addnew/<?php echo $datasource_id."/".$mappedspace_id."/".$mappedclass_id; ?>"><i class="resize horizontal purple  icon" ></i>Create object property</a> 
			
			<a class="item" style=" text-align=left;" href="<?php echo base_url();?>index.php/mappedclass/expand/<?php echo $datasource_id."/".$mappedspace_id."/".$mappedclass_id; ?>" title="Expand class"><i class="asterisk purple icon" ></i>Expand mapping</a>

			<a class="item url7"><i class="external share purple icon" ></i>Move to
				<div class="menu suburl7">

					<?php
					
						foreach( $mappingspaces as $ms ) {

							if ( $ms->id == $mappedspace_id ) {
								echo "<div class=\"item disabled\">" . $ms->name . "</div>";
							} else {

								$btnUrl = base_url() . "index.php/mappedclass/move/" . $mappedclass_id . "/" . $ms->id;

								echo '<div class="item" onclick="window.location = \' ' . $btnUrl . ' \'">';
								echo $ms->name;
								echo '</div>';
							}
						}
					?>


				</div>
			</a>

			<a class="item" href="<?php echo base_url();?>index.php/mappedclass/delete/<?php echo $datasource_id."/".$mappedspace_id."/".$mappedclass_id; ?>" onclick="return confirm('Are you sure?');"><i class="delete red icon" ></i>Delete mapping</a>
		</div>	
	</div>
	<div id="details_<?php echo $mappedclass_id;?>"  style="display: none; margin-top:15px; margin-bottom:0px">
		<div class="ui horizontal segment">
			<p><strong>SQL:</strong> <?php echo $sql;?></p>
		
			<p><strong>URI:</strong> <?php echo $uri;?></p>
		</div>
	</div>
	<br /><br />
	<!--div class="ui horizontal segment"-->
	<div>
	 
		<div class="ui  list">
			<?php foreach($dataproperties as $datarow): ?>
				<div class="basic item">
					<div class="ui top left pointing dropdown lmcDropdown">
						<div class="ui tiny green circular image label" >
							<?php echo $datarow->dataproperty;?>
						</div>
						<div class="ui tiny purple circular image label" >
							<?php echo $datarow->value;?>
						</div>
						<div class="menu">
							<a class="item" href="<?php echo base_url();?>index.php/mappeddataproperty/addnew/<?php echo $datasource_id."/".$mappedspace_id."/".$mappedclass_id."/".$datarow->id; ?>"><i class="edit purple icon" ></i>Edit data property</a> 
							<a class="item" href="<?php echo base_url();?>index.php/mappeddataproperty/delete/<?php echo $datasource_id."/".$mappedspace_id."/".$datarow->id; ?>" onclick="return confirm('Are you sure?');"><i class="delete red icon" ></i>Delete data property</a>
						</div>
					</div>
				</div>
			<?php endforeach; ?>
			
			
			<?php foreach($objectproperties as $datarow): ?>
				<div class="basic item">
					<div class="ui top left pointing dropdown lmcDropdown">
						<div class="ui tiny red  circular image label"  title="Target: <?php echo $targetDescription[$datarow->id];?>"><?php echo $datarow->target;?></div>
						<div class="menu">
						  <a class="item" href="<?php echo base_url();?>index.php/mappedclass/delete/<?php echo $datasource_id."/".$mappedspace_id."/".$mappedclass_id; ?>" onclick="return confirm('Are you sure?');"><i class="delete red icon" ></i>Delete object property</a>
						</div>
					</div>
				</div>
				
			
			<?php endforeach; ?>
		</div>	
	</div>
</div>

<script>
	$('.lmcDropdown').dropdown({
		on: 'hover'
	});
</script>