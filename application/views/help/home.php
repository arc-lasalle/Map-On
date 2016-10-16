<div id="content">	


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
	</div>	
</div>

<div class="ui green segment">

<p><strong>Help</strong></p>
<div class="ui styled accordion">
	<div class=" ui header title" width ="100%">
      <i class="dropdown icon"></i>1. Interface concepts </div>
	<div class=" content">
	<p>
	The mapping space is divided in two panels. In the right panel <strong>(1)</strong>, there is the visualization of the mappings based on a graph representation. The user can modify the layout positioning the elements (i.e. tables, columns, classes, data properties) by dragging them. In the left panel <strong>(2)</strong>, a list of mappings is displayed and the button for creating, editing and removing mappings. <br/><br/>
	The legend describes the different element and color codes <strong>(3)</strong>.

	<br /><br />
	<img src="<?php echo base_url()?>public/img/help/Imagen1.png" width="100%">
	<br /><br />
	When the cursor hover a mapping in the left screen <strong>(1)</strong> the corresponding elements of the graph layout are highlighted <strong>(2)</strong>. When the cursor hovers a node of the graph the corresponding mapping is highlighted. Thus a description of the concept is shown <strong>(3)</strong>.

	<br /><br />
	<img src="<?php echo base_url()?>public/img/help/Imagen2.png" width="100%">
	<br /><br />
	To show the menu option <strong>(1)</strong> for creating a data property, object property, expand a mapping, and delete a mapping click on the concept box of a mapping <strong>(2)</strong>.

	<br /><br />
	<img src="<?php echo base_url()?>public/img/help/Imagen3.png" width="100%">
	<br /><br />
	To search a particular mapping, write a text in the input box <strong>(1)</strong> and a list of mappings that match the text will be displayed <strong>(2)</strong>. When an element of the list is selected the mapping is highlighted.

	<br /><br />
	<img src="<?php echo base_url()?>public/img/help/Imagen4.png" width="100%">
	<br /><br />
	To show the history log <strong>(1)</strong> click on the button <strong>(2)</strong>. Each mapping space and mapping have their own history log.
	<br /><br />
	<img src="<?php echo base_url()?>public/img/help/Imagen5.png" width="100%">
	</p>
	</div>



<div class=" ui header title" width ="100%"> <i class="dropdown icon"></i> 2. Creating a data source</div>
<div class=" content">
	<p>
To create a new data source click on <strong>Create new data source (1)</strong> button and the input form will be displayed. Fill the inputs: <br/>
 - <strong>Name</strong>: The name of the data source. <strong>(2)</strong><br/>
 - <strong>SQL file</strong>: The SQL schema of the data source, it will be uploaded from your file system. <strong>(3)</strong><br/>
 - <strong>Base URI</strong>: The base URI for generating the IRI patterns of the R2RML statements (e.g. http://www.example.com/resource/). <strong>(4)</strong><br/>
 - <strong>Target ontology</strong>: To select an already existing ontology as a domain (target) ontology for the mappings. <strong>(5)</strong><br/>
<br/>
Click <strong>Create (6)</strong> at the bottom of the form to create the data source. 


	<br /><br />
	<img src="<?php echo base_url()?>public/img/help/Imagen6.png" width="100%">
	</p>
</div>
		
<div class=" ui header title" width ="100%"> <i class="dropdown icon"></i> 3. Loading an ontology</div>
<div class=" content">

	<p>
To load an ontology click on <strong>Add new ontology (1)</strong> button and the input form will be displayed. Fill the inputs: <br />
 - <strong>Name</strong>: The name of the ontology. <strong>(2)</strong><br /><br />

Click <strong>Add new ontology (3)</strong> at the bottom of the form to create the ontology. 

	<br /><br />
	<img src="<?php echo base_url()?>public/img/help/Imagen7.png" width="100%">
		<br /><br />
	To add a new ontology module click on <strong> Add new module (1)</strong> button and the input form will be displayed. Fill the inputs: <br />
 - <strong>Name</strong>: The name of the ontology module. <strong>(2)</strong><br />
 - <strong>Prefix</strong>: The prefix of the ontology module. <strong>(3)</strong><br />
 - <strong>File</strong>: The OWL file of the ontology module. The preferred format is RDF/XML. <strong>(4)</strong><br /><br />

Click <strong>Add new ontology module (5)</strong> at the bottom of the form to upload the module. <br /><br />

<strong>Note</strong>: all modules are stored in the same repository. In the case your ontology have multiple imports, upload the ontology as a module and the imports as a modules as well.

	<br /><br />
	<img src="<?php echo base_url()?>public/img/help/Imagen8.png" width="100%">
	
		<br /><br />
	To display the namespaces of the ontology (automatically retrieved from the ontology modules) click on the link <strong>(1)</strong> on the right side of the Namespaces label. <br /><br />

To edit a namespace click on the edit button <strong>(2)</strong> and the edit form will be displayed. Edit the input boxes (prefix  and IRI of the ontology).<br /><br />

Click Edit namespace at the bottom of the form to edit the namespace. <br /><br />

Click on Add new namespace button <strong>(6)</strong> to create new namespaces.<br /><br />

<strong>Note</strong>: The namespaces are used for generating the qnames needed for visualizing the mappings. If the namespaces are not properly set it might cause visualization issues.<br />


	<br /><br />
	<img src="<?php echo base_url()?>public/img/help/Imagen9.png" width="100%">
	</p>
</div>
			
		
		

		
<div class=" ui header title" width ="100%"> <i class="dropdown icon"></i> 4. Creating a mapping space</div>
<div class=" content">
	<p>
To create a mapping space click on <strong>Create new mapping space (1)</strong> button and the input form will be displayed. Fill the inputs: <br />
 - <strong>Name</strong>: The name of the mapping space.<strong>(2)</strong><br /><br />

Click <strong>Create (3)</strong> at the bottom of the form to create the mapping space. 



	<br /><br />
	<img src="<?php echo base_url()?>public/img/help/Imagen10.png" width="100%">
	</p>
</div>
		

<div class=" ui header title" width ="100%"> <i class="dropdown icon"></i>5. Creating a mapping</div>
<div class=" content">
	<p>
To create a mapping between an ontology concept and a database table select a mapping space <strong>(1)</strong>. 

	<br /><br />
	<img src="<?php echo base_url()?>public/img/help/Imagen11.png" width="100%">
	<br /><br />
	To create a mapping click on <strong>plus (1)</strong> button and the new mapping page will be loaded.

	<br /><br />
	<img src="<?php echo base_url()?>public/img/help/Imagen12.png" width="100%">
		<br /><br />
	To select a target concept write in the <strong>class</strong> input box <strong>(1)</strong> a text and a list of possible concepts that match with the text will be displayed <strong>(2)</strong>. When an element of the list is selected the name of the node is accordingly changed <strong>(3)</strong>.


	<br /><br />
	<img src="<?php echo base_url()?>public/img/help/Imagen13.png" width="90%">
		<br /><br />
	To select a target column of the datable type in the <strong>Table/Column source</strong> input box <strong>(1)</strong>  and a list of possible columns will be displayed <strong>(2)</strong>. When an element of the list is selected the concept and column nodes are automatically linked with a dotted blue edge <strong>(3)</strong>. 
<br /><br/>
<strong>Note</strong>: Clicking another column node in the graph representation will modify the mapping and update the <strong>Table/Column source</strong> input box.


	<br /><br />
	<img src="<?php echo base_url()?>public/img/help/Imagen14.png" width="80%">
		<br /><br />
	To show the SQL query and URI patterns click on the <strong>SQL and URI details</strong> label <strong>(1)</strong>. The SQL query <strong>(2)</strong> and the URI pattern <strong>(2)</strong> are automatically generated when the mapping is modified.

	<br /><br />
	<img src="<?php echo base_url()?>public/img/help/Imagen15.png" width="42%">
		<br /><br />
	The visualization of the graph representation (i.e. the tables and columns) can be filtered. Click on the check boxes <strong>(1)</strong> to show or hidden the tables and their columns.

	<br /><br />
	<img src="<?php echo base_url()?>public/img/help/Imagen16.png" width="90%">
	</p>
</div>
		


<div class=" ui header title" width ="100%"> <i class="dropdown icon"></i>6. Creating an object property mapping</div>
<div class=" content">
	<h4 class="ui header">6. Creating an object property mapping </h4>
	<p>
	<br /><br />
	
	</p>
</div>
		
		
<div class=" ui header title" width ="100%"> <i class="dropdown icon"></i>7. Creating a data property mapping</div>
<div class=" content">
	<p>
	To create a data property for a particular mapping click on the <strong>concept box</strong> of the mapping <strong>(1)</strong> and the menu options will be shown. Click on <strong>Create data property (2)</strong> option. 

	<br /><br />
	<img src="<?php echo base_url()?>public/img/help/Imagen17.png" width="100%">
	<br /><br />
	To select a data property write in the <strong>Data property</strong> input box <strong>(1)</strong> a text and a list of possible data properties that match the text will be displayed <strong>(2)</strong>. When an element of the list is selected the name of the node is accordingly changed <strong>(3)</strong>.

	<br /><br />
	<img src="<?php echo base_url()?>public/img/help/Imagen18.png" width="100%">
	<br /><br />
	To select a target column of the datable type in the <strong>Table/Column source</strong> input box <strong>(1)</strong>  and a list of possible columns will be displayed. When an element of the list is selected the data property and column nodes are automatically linked with a dotted blue edge <strong>(2)</strong>. By the default, the <strong>Table/Column source</strong> input box will have the same value as the domain concept.<br /><br />

To show the Value statement and Type click on <strong>the Value and Type details</strong> label <strong>(3)</strong>. The Value <strong>(4)</strong> and the Type <strong>(5)</strong> are automatically generated when the mapping is modified. The Value is taken from the <strong>Table/Column source</strong> and the Type from the type of the column retrieved from the database schema.<br /><br />

<strong>Note</strong>: Clicking another column node in the graph representation will modify the mapping and update the <strong>Table/Column source</strong> input box.


	<br /><br />
	<img src="<?php echo base_url()?>public/img/help/Imagen19.png" width="100%">
	</p>
</div>
	
		
		
<div class=" ui header title" width ="100%"> <i class="dropdown icon"></i>8. Expanding a mapping</div>
<div class=" content">
	<p>
	To expand a mapping click on a <strong>concept box</strong> of the mapping <strong>(1)</strong> and the menu options will be shown. Click on <strong>Expand mapping</strong> option <strong>(2)</strong>. 

	<br /><br />
	<img src="<?php echo base_url()?>public/img/help/Imagen20.png" width="100%">
	<br /><br />
	Users can create multiple mappings from a database element to different concepts following a path <strong>(1)</strong> whose origin is an existing mapped concept. The user can expand the concepts by clicking their nodes <strong>(2)</strong>. 

	<br /><br />
	<img src="<?php echo base_url()?>public/img/help/Imagen21.png" width="100%">
	<br /><br />
	The nodes can be located in any position. Push a node and drag it to the desired position <strong>(1)</strong>. Once a node is clicked it will be expanded following the ontology structure.

	<br /><br />
	<img src="<?php echo base_url()?>public/img/help/Imagen22.png" width="100%">
		<br /><br />
	When the cursor hovers a node the path <strong>(1)</strong> from the origin concept to the hovered node is highlighted. When a node is clicked the path is stored in <strong>(2)</strong>. Click <strong>Save path</strong> to automatically generate the mappings between the origin concept and the concepts listed in the path.

	<br /><br />
	<img src="<?php echo base_url()?>public/img/help/Imagen23.png" width="100%">
	</p>
</div>
		
		
	
		
<div class=" ui header title" width ="100%"> <i class="dropdown icon"></i>9. Generating R2RML document</div>
<div class=" content">
	<p>
	To generate the R2RML document for a data source click on <strong>R2RML</strong> link <strong>(1)</strong>. 
	<br /><br />
	<img src="<?php echo base_url()?>public/img/help/Imagen24.png" width="100%">
	<br /><br />
	The R2RML document is automatically generated and visualized <strong>(1)</strong> . To download the R2RML document click on <strong> R2RML (2)</strong>. To edit the part of the document click on <strong>R2RML part link (3)</strong>. 

	<br /><br />
	<img src="<?php echo base_url()?>public/img/help/Imagen25.png" width="100%">
	<br /><br />
	The R2RML part contains mappings created by the user manually <strong>(1)</strong>, it can contain any kind of mapping without restrictions. The R2RML part will be appended to the final R2RML document. To save the R2RML part click on <strong>Edit button (2)</strong>

	<br /><br />
	<img src="<?php echo base_url()?>public/img/help/Imagen26.png" width="100%">

	</p>
</div>
		
		










	</div>


</div>
		
</div>

</div>

<script>

$('.ui.accordion').accordion();

</script>