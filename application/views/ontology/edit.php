<div id="content">
		
	<div id="title">Edit mapping space</div>
	
	<?php 
		echo form_open_multipart(base_url().'index.php/mappingspace/edit_post');
		echo form_hidden('mappingspace_id', $mappingspace_id);
		echo form_hidden('datasource_id', $datasource_id);
	?>
	
        <table>
            <tr>
                <td>
                    <?php echo form_label('Name'); ?>
                </td>
                <td>
                    <?php 
                    echo form_input(array(
                        'name'  => 'input_name',
                        'id'    => 'input_name',
						'value'    => "$name",
						)); 
                    ?>
                </td>
            </tr>
			<tr>
                <td>
					<br/>	
					<div class="buttons"><button type="submit" class="positive" name="edit">Edit mapping space</button></div>
                </td>
				<td><br/>
					<div class="buttons"><a href="<?php echo base_url();?>index.php/mappingspace/show/<?php echo $datasource_id;?>" class="negative">Cancel</a></div>
				</td>
            </tr>
        </table>
        <?php echo form_close(); ?>

</div>
