<div id='content'>

    <h2 class="ui header">
        <i class="user icon"></i>
        <div class="content">
            Edit user
            <div class="sub header">Here you can edit the user information.</div>
        </div>
    </h2>

    <div class="ui piled segment">
        <h4 class="ui large header">Profile</h4>

        <b>ID: </b> <?php echo $user->id ?> <br/>
        <b>User: </b> <?php echo $user->username ?> <br/>
        <b>Email: </b> <?php echo $user->email ?> <br/>
        <b>Name: </b> <?php echo $user->first_name . ' ' . $user->last_name; ?> <br/>
        <b>Company: </b> <?php echo $user->company ?> <br/>
        <b>Phone: </b> <?php echo $user->phone ?> <br/>

        <?php //var_dump($user); ?>

        <br/>
        <button class="ui button" disabled>Save</button>

    </div>

    <div class="ui piled segment">
        <h4 class="ui large header">Groups</h4>


        <b>Groups:</b>
        <form id="form" action="" method="post">
            <select id="groups_dropdown" name="group_list[]" multiple="" class="ui multiple fluid dropdown">
                <?php
                    foreach( $groups as $group ) {
                        $s = in_array( $group->id, $user_groups ) ? "selected" : "";
                        echo '<option value="'.$group->id.'" '.$s.'>'.$group->description.'</option>';
                    }
                ?>
            </select>
            <input type="hidden" name="set_groups" value="set_groups">
            <input id="group_btn" type="submit" value="Save groups" class="ui tiny button" style="margin-top: 5px;">

        </form>
        <?php //var_dump($groups); ?>

        <br>
        <b>Teams:</b>
        <form id="form" action="" method="post">
            <select id="teams_dropdown" name="team_list[]" multiple="" class="ui multiple fluid dropdown">
                <?php
                foreach( $teams as $team ) {
                    $s = in_array( $team->id, $user_teams ) ? "selected" : "";
                    echo '<option value="'.$team->id.'" '.$s.'>'.$team->name.'</option>';
                }
                ?>
            </select>
            <input type="hidden" name="set_teams" value="set_teams">
            <input id="team_btn" type="submit" value="Save teams" class="ui tiny button" style="margin-top: 5px;">

        </form>

        <br><br>
    </div>

    <script>
        $('#group_btn').prop( "disabled", true );
        $('#team_btn').prop( "disabled", true );
        $('#groups_dropdown').dropdown({
            onChange: function () {
                $('#group_btn').prop( "disabled", false );
            },
        });
        $('#teams_dropdown').dropdown({
            onChange: function () {
                $('#team_btn').prop( "disabled", false );
            },
        });
    </script>

</div>