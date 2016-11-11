<div id='content'>
<br />
<div class="ui stackable grid">
	<div class="ten wide column">
		<div class="ui green segment">
			<strong>Map-On: A web-based editor for visual ontology mapping</strong><br />
			<p>Map-On is a graphical environment for ontology mapping which helps different kinds of users to carry out ontology mapping processes by establishing relations between the elements of a database and a domain ontology.</p>
			<p>Map-On has four distinctive features: 
			<ul><li>Point-and-click interface for reducing the map-ping activities effort</li>
			<li>Ontology-driven mapping approach, where the mapping process starts from the ontology in-stead of working with the database</li>
			<li>Top-down visualization, for representing the whole database schema, ontology structure, and mappings using an interactive graph layout</li>
			<li>Mapping spaces, where the mappings are freely group by the users in order to keep mappings tidy</li>
			<li>R2RML mappings automatically generated from the actions carried out by the users in the graphic interface</li>
			</ul>
			</p>
		</div>
		<div class="ui green segment">
			<strong>Recent development changes</strong><br /><br />
	
			<span> - 10/10/2016 <i>Fixing errors and refactoring.</i> </span><br />
			<span> - 11/06/2016 <i>Added MapSchema to analyse data sources (databases, csv files).</i> </span><br />			
			<span> - 25/01/2016 <i>Added visual representation of the database based on tables. </i> </span><br />
			<span> - 23/12/2015 <i>Added WebOWL for visualizing the ontology.</i> </span><br />			
			<span> - 17/12/2015 <i>Fixing errors with the forms.</i> </span><br />
			<span> - 11/06/2014 <i>Added activity log.</i> </span><br />
			<span> - 02/03/2014 <i>Added new graphic representation of the mappings.</i> </span><br />
			<span> - 21/05/2013 <i>R2RML recommendation support.</i> </span><br />		
			<span> - 13/03/2012 <i>First version.</i> </span><br />
		</div>

	</div>
	<div class="four wide column">
		<div class="ui green segment">
			<div class="ui small header">
				<strong>Please login with your email/username and password below</strong> 
			</div>	
		
			<div class="ui form secondary accordion fluid segment" >

				<div id="infoMessage" style="color: #f00;"><?php echo $message;?></div>
		
				<br /><br />
				<?php echo form_open("auth/login");?>
			
				<div class="field">
					<label>Email/Username</label>
					<div class="ui input">
						<?php echo form_input($identity);?>
					</div>
				</div>
				<div class="field">
					<label>Password</label>
					<div class="ui input">
						<?php echo form_input($password);?>
					</div>
				</div>
				<div class="field">
					<label>Remember Me</label>
					<div class="ui input">
						<?php echo form_checkbox('remember', '1', FALSE);?>
					</div>
				</div>
				
				<div class="actions">
					<input type="submit" value="Login" class="ui tiny button" />
				</div>
				<?php echo form_close(); ?>
			</div>
		</div>
	</div>
</div>

</div>