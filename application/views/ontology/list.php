<!-- WebVOWL CSS's -->
<link REL=StyleSheet HREF="<?php echo base_url(); ?>/public/css/common/edition_area.css" TYPE="text/css" MEDIA=screen>
<link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>/public/css/external/webvowl/webvowl.css" />
<link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>/public/css/external/webvowl/webvowl.app.css" />


<div class="ui green segment">
	Ontologies:<br/><br/>
		
	<table class="ui selectable basic small table">
	<thead>
		<tr>
			<th scope="col" width="60%">Name</th>
			<th scope="col" width="20%">Date</th>
			<th scope="col" width="20%"></th>
		</tr>
	</thead>
	<tbody>
	<?php foreach($ontologies as $row): ?>
		<tr>
			<td><a href="<?php echo base_url();?>index.php/ontology/view/<?php echo $row->id; ?>"><?php echo $row->name; ?></a>	</td>
			<td><?php echo $row->date; ?> </td>
			<td>
				<a href="<?php echo base_url();?>index.php/ontology/view/<?php echo $row->id; ?>">
					<i class="edit link icon" style="color: black;" title="Edit ontology"></i>
				</a>
				<!--a onclick="loadVowl(<?php echo $row->id; ?>);">
					<i class="unhide link icon" style="color: black;" title="View ontology"></i>
				</a-->
				<a href="<?php echo base_url();?>index.php/ontology/delete/<?php echo $row->id; ?>" onclick="return confirm('Are you sure?');">
					<i class="remove link icon" style="color: red;" title="Delete ontology"></i>
					<!--img src="<?php echo base_url();?>/public/img/delete.png" title="delete ontology"-->
				</a>
			</td>
		</tr>
	<?php endforeach; ?>
	</tbody>

	<tfoot>
		<tr>
			<th colspan="3"><br/>
				<?php
					if ( $this->team->connected() ) {
						echo '<div class="ui tiny button" onMouseUp="$(\'.ui.modal\').modal(\'show\');">add new ontology</div>';
					} else {
						echo 'Team not selected.';
					}
				?>

			</th>
		</tr>
	</tfoot> 
	</table> 
</div>

<!-- MODAL BOX -->
<?php echo $createnew; ?>


<!-- Ontology Graph - WebVOWL
<div id="ea_loader" class="hidden"></div>
<section id="canvasArea"></section>
-->


<!-- WebVowl JS's
<script src="<?php echo base_url(); ?>/public/js/external/webvowl/d3.min.js"></script>
<script src="<?php echo base_url(); ?>/public/js/external/webvowl/webvowl.js"></script>
<script src="<?php echo base_url(); ?>/public/js/external/webvowl/webvowl.app.js"></script>

<script type="text/javascript">
	var webvowl_app;

	function loadVowl( ontology_id ) {
		$('#ea_loader').removeClass('hidden');
		$('#ea_loader').html("Loading ontology...<br>&#8635;");
		$('#canvasArea').html('<div id="graph"></div>');

		// Initialize WebVOWL
		this.webvowl_app = webvowl.app();
		this.webvowl_app.initialize();
		var self = this;

		$.ajax({
			type: "POST",
			url:  "<?php echo base_url(); ?>index.php/ontology/getvowlontology/" + ontology_id,
			success: function( data ) {
				try {
					self.webvowl_app.loadVowlFile(data);

					$('#ea_loader').addClass('hidden');
					$('#canvasArea').height('500px');

				} catch( err ) {
					$('#ea_loader').html('Error loading<br><i class="warning icon"></i>');
					console.error(err);
				}

			}
		});
	}
</script>-->