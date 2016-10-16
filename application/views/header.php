<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<meta name="title" content="OME: Ontology Mapping Environment">
	<meta name="date" content="2014">
	<meta name="description" content='Semantic Tools for Carbon Reduction in Urban Planning' />
    <meta name="keywords" content="ontology mapping, linked open data, linked data, fp7 project" />
 
    <title>OME: Ontology Mapping Environment</title>
	<base href="<?php echo base_url(); ?>" />
	<link REL=StyleSheet HREF="<?php echo base_url(); ?>/public/css/style.css" TYPE="text/css" MEDIA=screen>
	<link REL=StyleSheet HREF="<?php echo base_url(); ?>/public/css/RGraph.css" TYPE="text/css" MEDIA=screen>
	    
	<script src="<?php echo base_url(); ?>/public/js/jit.js" language="javascript" type="text/javascript" ></script>
	<script src="<?php echo base_url(); ?>/public/js/jquery-1.7.1.min.js" language="javascript" type="text/javascript" ></script>
	<script src="<?php echo base_url(); ?>/public/js/jquery.masonry.min.js" language="javascript" type="text/javascript" ></script>
	<script src="<?php echo base_url(); ?>/public/js/vivagraph.js" language="javascript" type="text/javascript" ></script>
	<script src="<?php echo base_url(); ?>/public/js/common/global.js" language="javascript" type="text/javascript" ></script>
</head>
<!-- <body onload="init();"> -->

<body>
<div id="outer">

	<div id="header">
		<div id="logo">
			<table><tr><td>OME: Ontology Mapping Environment</td><td style="text-align:right;font-size:12px;">
			<?php 
/*				if ($this->ion_auth->logged_in()) {
					$user = $this->ion_auth->user()->row();
					echo $user->first_name." ".$user->last_name." <a href='index.php/auth/logout'>logout</a>";
				}
*/
			?>
			</td></tr></table>
		</div><!-- /logo -->
	</div><!-- /header -->
	<div id="menu">
		<ul id="nav">
			<?php //if ($this->ion_auth->logged_in()) { ?>
			<li><a href="index.php/home" <?php if($this->uri->segment(1) =="home"): ?> class="activo"<?php endif; ?> id="nav_home">Home</a></li>
			<li><a href="index.php/datasource" <?php if($this->uri->segment(1) =="datasource" || $this->uri->segment(1) =="mappings"): ?> class="activo"<?php endif; ?>>Data sources</a></li>
			<li><a href="index.php/ontology" <?php if($this->uri->segment(1) =="ontologies"): ?> class="activo"<?php endif; ?>>Ontologies</a></li>
			<!--
			<li><a href="index.php/prefixes" <?php if($this->uri->segment(1) =="prefixes"): ?> class="activo"<?php endif; ?>>Prefixes</a></li>
			<li><a href="index.php/extractor" <?php if($this->uri->segment(1) =="extractor"): ?> class="activo"<?php endif; ?>>Extractor</a></li>
			-->
			<?php //if ($this->ion_auth->is_admin()) { ?>
			<li><a href="index.php/admin" <?php if($this->uri->segment(1) =="admin"): ?> class="activo"<?php endif; ?>>Admin</a></li>
			<?php //} ?>
			<?php //} ?>
		</ul>
	</div><!-- /menu -->

		

	
