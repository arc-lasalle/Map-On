
<div class="ui grid">
    <div class="three wide column">
        
        <div class="ui vertical menu" style="">

            <div class="item">
                <div class="header">Users</div>
                <div class="menu">
                    <a class="item" href="<?php echo base_url(); ?>index.php/admin/user_list">List users</a>
                    <a class="item" href="<?php echo base_url(); ?>index.php/admin/create_user">Create new user</a>
                </div>
            </div>

            <div class="item">
                <div class="header">Teams</div>
                <div class="menu">
                    <a class="item" href="<?php echo base_url(); ?>index.php/admin/team_list">List teams</a>
                    <a class="item" href="<?php echo base_url(); ?>index.php/admin/import_team">Import team</a>
                    <a class="item" href="<?php echo base_url(); ?>index.php/admin/create_team">Create new team</a>
                </div>
            </div>

        </div>

    </div>
    <div class="twelve wide column">
        
        <div class="ui segment" style="width: 100%;">

            <?php echo $theContent ?>

        </div>

    </div>
</div>


    


