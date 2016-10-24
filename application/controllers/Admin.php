<?php

/**
 * Created by PhpStorm.
 * User: Usuari
 * Date: 09/05/2016
 * Time: 11:18
 */
class Admin extends CI_Controller {
    function __construct()
    {
        parent::__construct();
        $this->load->library('ion_auth');
        $this->load->library('session');
        $this->load->library('form_validation');
        $this->load->database();
        $this->load->dbutil();
        $this->load->helper('url');
        $this->load->model("Admin_model", "admin");

        if ( !$this->ion_auth->logged_in() ) {
            //redirect them to the login page
            redirect('auth/login', 'refresh');

        } elseif (!$this->ion_auth->is_admin()) {
            //redirect them to the home page because they must be an administrator to view this
            redirect($this->config->item('base_url'), 'refresh');

        }

    }
    
    function _view( $template = null, $data = null ) {

        $data['theContent'] = ( $template == null ) ? "" : $this->load->view($template, $data, TRUE);

        $this->load->view('header_s');
        $this->load->view('admin/shared/menu', $data);
        $this->load->view('footer_s');
    }



    function index()
    {
        redirect('admin/user_list', 'refresh');
    }


    function user_list()
    {

        //set the flash data error message if there is one
        $this->data['message'] = (validation_errors()) ? validation_errors() : $this->session->flashdata('message');

        //list the users
        $this->data['users'] = $this->ion_auth->users()->result();
        foreach ($this->data['users'] as $k => $user) {
            $this->data['users'][$k]->groups = $this->ion_auth->get_users_groups($user->id)->result();
        }



        $this->_view('admin/user_list', $this->data);
    }


    function create_user()
    {
        $this->data['title'] = "Create User";

        //validate form input
        $this->form_validation->set_rules('first_name', 'First Name', 'required');
        $this->form_validation->set_rules('last_name', 'Last Name', 'required');
        $this->form_validation->set_rules('email', 'Email Address', 'required|valid_email');
        $this->form_validation->set_rules('phone1', 'First Part of Phone', 'required|min_length[3]|max_length[3]');
        $this->form_validation->set_rules('phone2', 'Second Part of Phone', 'required|min_length[3]|max_length[3]');
        $this->form_validation->set_rules('phone3', 'Third Part of Phone', 'required|min_length[4]|max_length[4]');
        $this->form_validation->set_rules('company', 'Company Name', 'required');
        $this->form_validation->set_rules('password', 'Password', 'required|min_length[' . $this->config->item('min_password_length', 'ion_auth') . ']|max_length[' . $this->config->item('max_password_length', 'ion_auth') . ']|matches[password_confirm]');
        $this->form_validation->set_rules('password_confirm', 'Password Confirmation', 'required');

        if ($this->form_validation->run() == true)
        {
            $username = strtolower($this->input->post('first_name')) . ' ' . strtolower($this->input->post('last_name'));
            $email = $this->input->post('email');
            $password = $this->input->post('password');

            $additional_data = array('first_name' => $this->input->post('first_name'),
                'last_name' => $this->input->post('last_name'),
                'company' => $this->input->post('company'),
                'phone' => $this->input->post('phone1') . '-' . $this->input->post('phone2') . '-' . $this->input->post('phone3'),
            );
        }
        if ($this->form_validation->run() == true && $this->ion_auth->register($username, $password, $email, $additional_data))
        { //check to see if we are creating the user
            //redirect them back to the admin page
            $this->session->set_flashdata('message', "User Created");
            redirect("admin", 'refresh');
        }
        else
        { //display the create user form
            //set the flash data error message if there is one
            $this->data['message'] = (validation_errors() ? validation_errors() : ($this->ion_auth->errors() ? $this->ion_auth->errors() : $this->session->flashdata('message')));

            $this->data['first_name'] = array('name' => 'first_name',
                'id' => 'first_name',
                'type' => 'text',
                'value' => $this->form_validation->set_value('first_name'),
            );
            $this->data['last_name'] = array('name' => 'last_name',
                'id' => 'last_name',
                'type' => 'text',
                'value' => $this->form_validation->set_value('last_name'),
            );
            $this->data['email'] = array('name' => 'email',
                'id' => 'email',
                'type' => 'text',
                'value' => $this->form_validation->set_value('email'),
            );
            $this->data['company'] = array('name' => 'company',
                'id' => 'company',
                'type' => 'text',
                'value' => $this->form_validation->set_value('company'),
            );
            $this->data['phone1'] = array('name' => 'phone1',
                'id' => 'phone1',
                'type' => 'text',
                'value' => $this->form_validation->set_value('phone1'),
            );
            $this->data['phone2'] = array('name' => 'phone2',
                'id' => 'phone2',
                'type' => 'text',
                'value' => $this->form_validation->set_value('phone2'),
            );
            $this->data['phone3'] = array('name' => 'phone3',
                'id' => 'phone3',
                'type' => 'text',
                'value' => $this->form_validation->set_value('phone3'),
            );
            $this->data['password'] = array('name' => 'password',
                'id' => 'password',
                'type' => 'password',
                'value' => $this->form_validation->set_value('password'),
            );
            $this->data['password_confirm'] = array('name' => 'password_confirm',
                'id' => 'password_confirm',
                'type' => 'password',
                'value' => $this->form_validation->set_value('password_confirm'),
            );

            $this->_view('admin/user_create', $this->data);
        }
    }

    function disable_user($id = NULL)
    {
        // no funny business, force to integer
        $id = (int) $id;

        $this->load->library('form_validation');
        $this->form_validation->set_rules('confirm', 'confirmation', 'required');
        $this->form_validation->set_rules('id', 'user ID', 'required|is_natural');

        if ($this->form_validation->run() == FALSE)
        {
            // insert csrf check
            $this->data['csrf'] = $this->_get_csrf_nonce();
            $this->data['user'] = $this->ion_auth->user($id)->row();

            $this->_view('admin/user_disable', $this->data);
        }
        else
        {
            // do we really want to deactivate?
            if ($this->input->post('confirm') == 'yes')
            {
                // do we have a valid request?
                if ($this->_valid_csrf_nonce() === FALSE || $id != $this->input->post('id'))
                {
                    show_404();
                }

                // do we have the right userlevel?
                if ($this->ion_auth->logged_in() && $this->ion_auth->is_admin())
                {
                    $this->ion_auth->deactivate($id);
                }
            }

            //redirect them back to the auth page
            redirect('admin/user_list', 'refresh');
        }
    }

    function enable_user($id, $code=false)
    {
        if ($code !== false)
            $activation = $this->ion_auth->activate($id, $code);
        else if ($this->ion_auth->is_admin())
            $activation = $this->ion_auth->activate($id);

        if ($activation)
        {
            //redirect them to the auth page
            $this->session->set_flashdata('message', $this->ion_auth->messages());
            redirect("admin", 'refresh');
        }
        else
        {
            //redirect them to the forgot password page
            $this->session->set_flashdata('message', $this->ion_auth->errors());
            redirect("auth/forgot_password", 'refresh');
        }
    }

    function edit_user( $user_id ) {

        if ( isset($_POST['set_teams']) ) $this->admin->setUserTeams( $user_id, isset($_POST['team_list']) ? $_POST['team_list'] : [] );
        if ( isset($_POST['set_groups']) ) $this->admin->setUserGroups( $user_id, isset($_POST['group_list']) ? $_POST['group_list'] : [] );

        $data['user'] = $this->ion_auth->user( $user_id )->result()[0];

        $data['groups'] = $this->ion_auth->groups()->result();

        $user_groups = $this->ion_auth->get_users_groups( $user_id )->result();
        foreach( $user_groups as $ug ) $data['user_groups'][] = $ug->id;

        $data['teams'] = $this->admin->getTeams();

        $user_teams = $this->admin->getTeams( $user_id );
        $data['user_teams'] = [];
        foreach( $user_teams as $ut ) $data['user_teams'][] = $ut->id;

        $this->_view('admin/user_edit', $data);
    }

    
    
    function team_list() {

        $data['teams'] = $this->admin->getTeams();

        $this->_view('admin/team_list', $data);
    }
    
    function save_team( $edit_team_id = null ) {

        $this->form_validation->set_rules('team_name', 'Team Name', 'required');
        $this->form_validation->set_rules('team_db', 'Team DB', 'required');
        $this->form_validation->set_rules('team_dir', 'Team Dir', 'required');

        if ($this->form_validation->run() == true) {

            if ( $edit_team_id == null ) {
                $this->admin->addTeam(
                    $this->input->post('team_name'),
                    $this->input->post('team_db'),
                    $this->input->post('team_dir')
                );

                if ( $this->input->post('create_database') != null ) {
                    $this->admin->createDatabase($this->input->post('team_db'), './application/config/mapon_team_db.sql');
                    
                    @mkdir('./upload/'.$this->input->post('team_dir'));
                    @mkdir('./download/'.$this->input->post('team_dir'));
                }


            } else {
                $this->admin->editTeam(
                    $edit_team_id,
                    $this->input->post('team_name'),
                    $this->input->post('team_db'),
                    $this->input->post('team_dir')
                );
            }
            
            redirect('admin/team_list', 'refresh');

        }

        redirect($_SERVER['HTTP_REFERER'], 'refresh');
    }

    function import_team() {

        $data['directories'] = array_diff(scandir('./upload'), array('..', '.'));
        $data['databases'] = $this->dbutil->list_databases();
        
        $this->_view('admin/team_edit_import', $data );
    }

    function create_team () {
        $this->_view('admin/team_create');
    }
    
    function edit_team ( $team_id ) {

        $data['directories'] = array_diff(scandir('./upload'), array('..', '.'));
        $data['databases'] = $this->dbutil->list_databases();

        $data['team'] = $this->admin->getTeam( $team_id );

        if ( $data['team'] == null ) {
            redirect('admin/team_list', 'refresh');
        }

        $this->_view( 'admin/team_edit_import', $data );

    }

    function delete_team( $team_id ) {
        $this->admin->deleteTeam( $team_id );
        redirect('admin/team_list', 'refresh');
    }



    function _valid_csrf_nonce()
    {
        if ($this->input->post($this->session->flashdata('csrfkey')) !== FALSE &&
            $this->input->post($this->session->flashdata('csrfkey')) == $this->session->flashdata('csrfvalue'))
        {
            return TRUE;
        }
        return FALSE;

    }

    function _get_csrf_nonce()
    {
        $this->load->helper('string');
        $key = random_string('alnum', 8);
        $value = random_string('alnum', 20);
        $this->session->set_flashdata('csrfkey', $key);
        $this->session->set_flashdata('csrfvalue', $value);

        return array($key => $value);
    }

}