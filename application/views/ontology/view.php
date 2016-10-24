
<div class="ui green segment">
	Ontology name: <strong><?php echo $ontology->name; ?></strong><br />
	Date: <?php echo $ontology->date; ?><br />
	Namespaces: <a href="<?php echo base_url();?>index.php/prefix/view/<?php echo $ontology->id; ?>"><?php echo $nprefixes; ?></a>	<br />
	<br />
	Number of classes: <strong><?php echo $statistics["nclasses"]; ?></strong><br />
	Number of data properties: <strong><?php echo $statistics["ndataprop"]; ?></strong><br />
	Number of object properties: <strong><?php echo $statistics["nobjprop"]; ?></strong><br />
	<br /><br />
	<strong>Ontology modules</strong>
	<table class="ui basic small table">
	<thead>
		<tr>
			<th scope="col" width="40%">Name</th>
			<th scope="col" width="40%">file</th>
			<th scope="col" width="20%"></th>
		</tr>
	</thead>
	<tbody>
	<?php foreach($modules as $row): ?>
		<tr>
			<td><?php echo $row->name; ?></td>
			<td><?php echo $row->file; ?> </td>
			<td>
				<a href="<?php echo base_url();?>upload/<?php echo $this->team->dir()?>/ontologies/<?php echo $ontology->id."_".$ontology->name; ?>/source/<?php echo $row->file;	 ?>">
					<i class="file outline icon" style="color: black;" title="View file"></i>
				</a>


			</td>
		</tr>
	<?php endforeach; ?>
	</tbody>
	<tfoot>
		<tr><th colspan="3"><br/>
			<div class="ui tiny button" onMouseUp="$('.ui.modal').modal('show');">Add new module</div></th>
		</tr>
	</tfoot> 
	</table> 
</div>

<!-- MODAL BOX -->
<?php echo $createnew; ?>