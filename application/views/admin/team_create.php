
<div id='content'>

    <h2 class="ui header">
        <i class="users icon"></i>
        <div class="content">
            Create team
            <div class="sub header">Here you can create a team.</div>
        </div>
    </h2>
    <br>

    <?php
        $form_url = site_url('admin/save_team');
        if ( isset($team) ) {
            $form_url = site_url('admin/save_team/' . $team->id );
        }
    ?>

    <form class="ui form" action="<?php echo $form_url ?>" method="post">


        <div class="field">
            <label>Team name</label>
            <input id="team_name" name="team_name" placeholder="Team name" type="text" value="<?php if(isset($team)) echo $team->name; ?>">
        </div>

        <div class="field">
            <label>Database name</label>
            <input id="team_db" name="team_db" placeholder="database name" type="text" value="<?php if(isset($team)) echo $team->database_name; ?>">
        </div>

        <div class="field">
            <label>Directory name</label>
            <input id="team_dir" name="team_dir" placeholder="directory name" type="text" value="<?php if(isset($team)) echo $team->directory_name; ?>">
        </div>

        <input type="hidden" name="create_database" value="true">

        <button class="ui button" type="submit">Submit</button>
    </form>

</div>


<script>
    $('#team_name').on('keyup', function(){
        console.log("Changing");
        var teamName = $('#team_name').val().toLowerCase();

        $('#team_db').val( 'mapon_' + teamName );
        $('#team_dir').val( 'team_' + teamName );

    });
</script>
