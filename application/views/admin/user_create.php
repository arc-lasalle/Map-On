<div id='content'>

    <h2 class="ui header">
        <i class="user icon"></i>
        <div class="content">
            Create user
            <div class="sub header">Here you can create a new user.</div>
        </div>
    </h2>


    <?php
        if ( isset($message) ) {
            echo '
                <div class="ui error message">
                    <div class="header"> There was some errors with your submission</div>
                    ' . $message . '
                </div>
                
            ';
        }
    ?>
    <br>

    <?php echo form_open("admin/create_user", array('class' => 'ui form') )?>

    <div class="field">
        <label>First Name</label>
        <?php echo form_input($first_name);?>
    </div>

    <div class="field">
        <label>Last Name</label>
        <?php echo form_input($last_name);?>
    </div>

    <div class="field">
        <label>Company Name</label>
        <?php echo form_input($company);?>
    </div>

    <div class="field">
        <label>Email</label>
        <?php echo form_input($email);?>
    </div>

    <div class="field">
        <label>Phone</label>
        <?php
        $a = array('style' => 'width: 250px;');
        echo form_input($phone1,null,$a);?> - <?php echo form_input($phone2,null,$a);?> - <?php echo form_input($phone3,null,$a);
        ?>
    </div>

    <div class="field">
        <label>Password</label>
        <?php echo form_input($password);?>
    </div>

    <div class="field">
        <label>Confirm Password</label>
        <?php echo form_input($password_confirm);?>
    </div>

    <p><?php echo form_submit('submit', 'Create User', array('class' => 'ui button'));?></p>


    <?php echo form_close();?>

</div>