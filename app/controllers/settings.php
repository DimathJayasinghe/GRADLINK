<?php
class settings extends Controller{
    private $model;
    public function __construct(){
        SessionManager:: redirectToAuthIfNotLoggedIn();
        $this->model = $this->model('M_settings');
    }

    public function index(){
        $data = [

        ];
        $this->redirect('/settings/account');
    }
    public function account(){
        $data = [
            'section' => 'account'
        ];
        $this->view('settings/v_settings', $data);
    }

    public function privacyandsafety(){
        $data = [
            'section' => 'privacyandsafety'
        ];
        $this->view('settings/v_settings', $data);
    }
    public function notifications(){
        $data = [
            'section' => 'notifications'
        ];
        $this->view('settings/v_settings', $data);

    }
    public function appearance(){
        $data = [
            'section' => 'appearance'
        ];
        $this->view('settings/v_settings', $data);
    }
    public function helpandsupport(){
        $data = [
            'section' => 'helpandsupport'
        ];
        $this->view('settings/v_settings', $data);
    }
}
?>