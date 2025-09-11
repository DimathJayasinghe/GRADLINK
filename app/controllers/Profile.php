<?php
class Profile extends Controller{
    protected $undergradModel;
    protected $alumniModel;
   
    public function __construct() {
        $this->undergradModel = $this->model('M_Undergrad');
        // $this->alumniModel = $this->model('M_Alumni');
    }
    
    public function index() {
        $data = [];
        $this->view('_404', $data);
    }

    public function alumniProfile(){
        $data['certificates'] = [
            ['title' => 'Certificate in Web Development', 'image_url' => null],
            ['title' => 'Certificate in Data Science', 'image_url' => null],
            ['title' => 'Certificate in Machine Learning', 'image_url' => null]
        ];

        $data['projects'] = [
            ['title' => 'Project A', 'description' => 'Description for Project A'],
            ['title' => 'Project B', 'description' => 'Description for Project B'],
            ['title' => 'Project C', 'description' => 'Description for Project C']
        ];

       $this->view('Alumni/v_profile', $data); 
    }

    public function undergraduateProfile() {
        // $data = [];

    //     $loggedInUserId = $_SESSION['user_id'] ?? null;

    //     if($loggedInUserId) {
    //         if($loggedInUserId == $profileUserId) {
    //             $data['isOwnProfile'] = true;
    //         } else {
    //             $data['isOwnProfile'] = false;
    //         }
    //     }


    //     $data['user'] = $this->undergradModel->getUserDetails($_SESSION['user_id']);

        //! Kalin code eka 
        // $data['userDetails'] = [
        //     'name' => 'John Doe',
        //     'age' => 21,
        //     'major' => 'Computer Science',
        //     'bio' => 'Aspiring software developer with a passion for learning new technologies.'
        // ];

        //* Uses the currently logged in user's id
        $data['userDetails'] = $this->undergradModel->getUserDetails(2);

        $data['certificates'] = [
            ['title' => 'Certificate in Web Development', 'image_url' => null],
            ['title' => 'Certificate in Data Science', 'image_url' => null],
            ['title' => 'Certificate in Machine Learning', 'image_url' => null]
        ];

        $data['posts'] = [
            ['title' => 'My First Post', 'content' => 'This is the content of my first post.'],
            ['title' => 'Learning PHP', 'content' => 'PHP is a great language for web development.'],
            ['title' => 'Exploring MVC', 'content' => 'MVC architecture helps in organizing code better.']
        ];

        $data['projects'] = [
            ['title' => 'Project A', 'description' => 'Description for Project A'],
            ['title' => 'Project B', 'description' => 'Description for Project B'],
            ['title' => 'Project C', 'description' => 'Description for Project C']
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
   
