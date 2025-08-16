<?php
class Profile extends Controller{
    Protected $undergradModel;
    public function __construct() {
        $this->undergradModel = $this->model('M_Undergrad');
    }
    public function index() {
        $data = [];
        $this->view('_404', $data);
    }

    public function undergraduateProfile() {
        $data = [];

        $data['userDetails'] = [
            'name' => 'John Doe',
            'age' => 21,
            'major' => 'Computer Science',
            'bio' => 'Aspiring software developer with a passion for learning new technologies.'
        ];

        $data['certificates'] = [
            ['title' => 'Certificate in Web Development', 'image_url' => null],
            ['title' => 'Certificate in Data Science', 'image_url' => null],
            ['title' => 'Certificate in Machine Learning', 'image_url' => null]
        ];

        $data['events'] = [
            ['image_url' => 'event-1-image'],
            ['image_url' => 'event-2-image'],
            ['image_url' => 'event-3-image'],
            ['image_url' => 'event-4-image'],
        ];

        $this->view('Undergraduate/v_profile', $data);
    }
}
?>