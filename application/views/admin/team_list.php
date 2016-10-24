<script src="<?php echo base_url(); ?>/public/libs/datatables/code/jquery.dataTables.min.js" language="javascript" type="text/javascript" ></script>
<link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>/public/libs/datatables/code/jquery.dataTables.min.css">

<div id='content'>
    
    <h2 class="ui header">
        <i class="users icon"></i>
        <div class="content">
            Teams
            <div class="sub header">Below is a list of teams.</div>
        </div>
    </h2>
    <br>


    <table id="team_list_table" class="display" cellspacing="0" width="100%">
        <thead>
            <tr>
                <th>Team name</th>
                <th>Database name</th>
                <th>Directory name</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php
                foreach ( $teams as $team ) {
                    echo '<tr>';
                    echo '<td>' . $team->name . '</td>';
                    echo '<td>' . $team->database_name . '</td>';
                    echo '<td>' . $team->directory_name . '</td>';
                    echo '<td>';
                    echo '<a href="'. site_url('admin/edit_team/' . $team->id) .'"> Edit</a>&nbsp;';
                    echo '<a href="'. site_url('admin/delete_team/' . $team->id) .'"> Delete</a>';
                    echo '</td>';
                    echo '</tr>';
                }
            ?>
        </tbody>
    </table>


    <p>
        <a href="<?php echo site_url('admin/import_team');?>">Import existing team</a>
        &nbsp;&nbsp;|&nbsp;&nbsp;
        <a href="<?php echo site_url('admin/create_team');?>">Create a new team</a>
    </p>


</div>


<script>
    $(document).ready(function() {
        $('#team_list_table').DataTable( {

        } );
    } );
</script>