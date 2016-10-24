<script src="<?php echo base_url(); ?>/public/libs/datatables/code/jquery.dataTables.min.js" language="javascript" type="text/javascript" ></script>
<link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>/public/libs/datatables/code/jquery.dataTables.min.css">


<div id="content">

    <h2 class="ui header">
        <i class="users icon"></i>
        <div class="content">
            Users
            <div class="sub header">Below is a list of the users.</div>
        </div>
    </h2>
    <br>

    <div id="infoMessage"><?php echo $message;?></div>


    <table id="user_list_table" class="display" cellspacing="0" width="100%">
        <thead>
        <tr>
            <th>First Name</th>
            <th>Last Name</th>
            <th>Email</th>
            <th>Groups</th>
            <th>Teams</th>
            <th>Status</th>
        </tr>
        </thead>
        <tbody>
        <?php foreach ($users as $user):?>
            <tr>
                <td><?php echo $user->first_name;?></td>
                <td><?php echo $user->last_name;?></td>
                <td><?php echo $user->email;?></td>
                <td>
                    <?php foreach ($user->groups as $group):?>
                        <?php echo $group->name;?><br />
                    <?php endforeach?>
                </td>
                <td>-</td>
                <td>
                    <?php echo ($user->active) ? anchor("admin/disable_user/".$user->id, 'Active') : anchor("admin/enable_user/". $user->id, 'Inactive');?>
                    <br/>
                    <a href="<?php echo site_url('admin/edit_user/'.$user->id);?>">Edit user</a>
                </td>
            </tr>
        <?php endforeach;?>
        </tbody>
    </table>



    <p><a href="<?php echo site_url('admin/create_user');?>">Create a new user</a></p>

</div>

<script>
    $(document).ready(function() {
        $('#user_list_table').DataTable( {

        } );
    } );
</script>