<div id='content'>

    <h2 class="ui header">
        <i class="user icon"></i>
        <div class="content">
            Disable user
            <div class="sub header">Here you can disable the user account.</div>
        </div>
    </h2>
    <br>


    <p>Are you sure you want to deactivate the user '<?php echo $user->username; ?>'</p>

    <?php echo form_open("admin/disable_user/".$user->id);?>

    <p>
        <label for="confirm">Yes:</label>
        <input type="radio" name="confirm" value="yes" checked="checked" />
        <label for="confirm">No:</label>
        <input type="radio" name="confirm" value="no" />
    </p>

    <?php echo form_hidden($csrf); ?>
    <?php echo form_hidden(array('id'=>$user->id)); ?>

    <p><?php echo form_submit('submit', 'Submit');?></p>

    <?php echo form_close();?>

</div>