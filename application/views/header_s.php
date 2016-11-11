<!DOCTYPE html>
<html>
<head>
    <!-- Standard Meta -->
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0">

    <!-- Site Properities -->
    <title>Map-On: Ontology Mapping environment</title>
    <link rel="shortcut icon" href="<?php echo base_url(); ?>public/icons/favicon-semanco-32.png">
    <link href='http://fonts.googleapis.com/css?family=Source+Sans+Pro:400,700|Open+Sans:300italic,400,300,700' rel='stylesheet' type='text/css'>

    <link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>/public/libs/Semantic-UI-2.1.8/semantic.css">
	<link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>/public/libs/Semantic-UI-2.1.8/semanticmods.css">
	<link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>/public/css/homepage.css">
    <link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>/public/css/codemirror.css">
    <link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>/public/css/neat.css">

    <script src="http://cdnjs.cloudflare.com/ajax/libs/jquery/2.0.3/jquery.js"></script>
    <!--script src="<?php echo base_url(); ?>/public/semanticui/semantic.js"></script-->
	<script src="<?php echo base_url(); ?>/public/libs/Semantic-UI-2.1.8/semantic.min.js"></script>
    <script src="<?php echo base_url(); ?>/public/js/vivagraph.js" language="javascript" type="text/javascript" ></script>
    <script src="<?php echo base_url(); ?>/public/js/codemirror/codemirror.js" language="javascript" type="text/javascript" ></script>
    <script src="<?php echo base_url(); ?>/public/js/codemirror/turtle.js" language="javascript" type="text/javascript" ></script>
    <script src="<?php echo base_url(); ?>/public/js/common/global.js" language="javascript" type="text/javascript" ></script>

    <script>
	  (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
	  (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
	  m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
	  })(window,document,'script','//www.google-analytics.com/analytics.js','ga');

	  ga('create', 'UA-31019112-10', 'auto');
	  ga('send', 'pageview');

	</script>

</head>
<body id="home">
	<div class="ui right wide vertical icon sidebar menu">
	<a class="item">
	<div class='ui small feed'>	  
		<h3>History log</h3><br />
							
		<?php if(isset($logs)) foreach($logs as $log ) {  ?>
		<div class='event'>
		<div class='label'>
			<?php 
				if($log->action == "new") echo "<i class='green add square icon'></i>";
				else if($log->action == "edit") echo "<i class='blue edit icon'></i>";
				else if($log->action == "delete") echo "<i class='red erase icon'></i>";
			?>
		
		</div>
		<div class='content'>
			<div class="summary">
				<?php echo $log->user_name; ?>
				<div class='date'>
					<?php echo $log->date; ?>
				</div>
			</div>
			<div class="extra text">
				<?php echo $log->log_message; ?>
			</div>
		</div>
		</div>
		<?php } ?>
		</div> 
	</a>
	</div>
	<div class="pusher">
	<h3 class="ui block header" style="background-color: #A1CF64; color: white;">
	<table width="100%" ><tr><td width="25" valign="middle"><img width="20" src="<?php echo base_url(); ?>public/icons/icon-semanco-32.png"> </td><td valign="middle"><strong>Map-On: Ontology Mapping environment</strong> </td><td valign="middle" style="text-align:right;font-size:12px;">
	<?php 
				if ($this->ion_auth->logged_in()) {
					$user = $this->ion_auth->user()->row();
					echo $user->first_name.' '.$user->last_name.' <a href="'.base_url().'index.php/auth/logout"><i class="sign out icon"></i></a>';
				}
	?>
	</td></tr></table>
	</h3>
		<div class="ui small pointing menu">
		<?php
			$tab = array_fill_keys( array('home', 'auth', 'datasource', 'ontology', 'help', 'admin'), '' );

			$segment = $this->uri->segment(1);

			if ( isset($tab[$segment]) ) $tab[$segment] .= ' active';

			echo '<a class="item ' . $tab['home'] . $tab['auth'] . '" href="'.base_url().'index.php/home">Home</a>';
			if ($this->ion_auth->logged_in()) {
				echo '<a class="item ' . $tab['datasource'] . '" href="' . base_url() . 'index.php/datasource">Data sources</a>';
				echo '<a class="item ' . $tab['ontology'] . '" href="' . base_url() . 'index.php/ontology">Ontologies</a>';
			}
			echo '<a class="item ' . $tab['help'] . '" href="'.base_url().'index.php/help">Help</a>';
			if($this->ion_auth->is_admin()) {
				echo '<a class="item ' . $tab['admin'] . '" href="'.base_url().'index.php/admin">Admin</a>';
			}
		?>
			<div class="right menu">

				<?php if ($this->ion_auth->logged_in() ) { ?>

					<div id="team_dropdown" class="ui top right pointing dropdown" style="margin: 6px 25px 5px 5px;">
						<div class="text">Team:&nbsp;&nbsp;<?php echo (isset($_SESSION['team_database'])) ? ($_SESSION['team_database']->name) : "None"; ?></div>
						<i class="dropdown icon"></i>
						<div class="menu">
							<?php
							$teams = $this->team->getTeams();
							foreach( $teams as $t ) {
								echo '<option value="' . $t->id . '" class="item active selected">' . $t->name . '</option>';
							}
							?>
						</div>
					</div>

					<script>
						$('#team_dropdown').dropdown({
							onChange: function(value, text, $selectedItem) {
								console.log("Entra");
								$.post( "#",
									{ set_team: $selectedItem[0].value },
									function (data) {
										<?php
											$valid_urls = array('home', 'datasource', 'ontology', 'help', 'admin');
											$url = base_url() . "index.php/" . $this->uri->segment(1);
											if ( !in_array($this->uri->segment(1), $valid_urls) ) $url = base_url() . "index.php/datasource";
										?>

										window.location.href = "<?php echo $url; ?>";
									}
								);
							}

						});
					</script>

				<?php } ?>



			</div>

		</div>

	<div class="ui horizontal segment">
	
	<?php if(isset($breadcrumb)) { ?>
	<div class="ui breadcrumb" style="padding: 5px; margin-top: -10px;">
		<?php foreach($breadcrumb as $b): ?>
			<a class="section" href="<?php echo base_url()."index.php/".$b["link"]; ?>"><?php echo $b["name"]; ?></a>
			<div class="divider"> / </div>
		<?php endforeach; ?>
	</div>
	<?php } ?>

	<?php
	$box_message = $this->session->flashdata('error_message');

	if ( $box_message ) {
		if ( $box_message[0]  ) echo '<div class="ui success message">';
		if ( !$box_message[0] ) echo '<div class="ui negative message">';
		echo '<i class="close icon"></i>';
		if ( isset($box_message[2]) ) {
			echo '<div class="header">' . $box_message[1] . '</div>';
			echo $box_message[2];
		} else {
			echo $box_message[1];
		}
		echo '</div>';
	}
	?>
	<script>
		$('.message .close').on('click', function() {
			$(this).closest('.message').transition('fade');
		});

        /*
        if ( sessionStorage.getItem('tab_id') === null ) {
            sessionStorage.setItem('tab_id', (new Date).getTime() );
        }
        document.cookie = 'tab_id=' + sessionStorage.getItem('tab_id') + ";path=/;";
        */
        /*
         php_vars = [];
         php_vars.base_url = '<?php echo base_url(); ?>';
         php_vars.logged_in = <?php echo ($this->ion_auth->logged_in()==1) ? "true" : "false"  ?>;

		 var isNewTab = (sessionStorage.getItem('newTab') === null);
         sessionStorage.setItem('newTab', '0');
		 if ( php_vars.logged_in && isNewTab ) {
		 	window.location.href = php_vars.base_url + "/index.php/auth/login";
		 }
		*/


	</script>
