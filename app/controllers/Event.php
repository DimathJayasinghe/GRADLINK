<?php
    class Event extends Controller {
        private $eventModel;

        public function __construct(){
            $this->eventModel = $this->model('M_event');
        }

        // List events. Optional query params: start, end, limit, page
        public function index(){
            $params = $this->getQueryParam();
            $filters = [];
            if(!empty($params['start'])) $filters['start'] = $params['start'];
            if(!empty($params['end'])) $filters['end'] = $params['end'];
            if(!empty($params['limit'])) $filters['limit'] = (int)$params['limit'];
            if(!empty($params['page'])) $filters['page'] = (int)$params['page'];
            if(!empty($params['visibility'])) $filters['visibility'] = $params['visibility'];

            $events = $this->eventModel->findList($filters);
            $data = ['events' => $events, 'filters' => $filters];
            $this->view('event/index', $data);
        }

        // Show a single event by id (numeric) or slug (string)
        public function show($identifier = null){
            if($identifier === null){
                // No identifier provided
                http_response_code(404);
                echo 'Event not found.';
                return;
            }

            // If numeric, treat as ID, else try slug
            if(ctype_digit((string)$identifier)){
                $event = $this->eventModel->findById((int)$identifier);
            } else {
                // fallback: attempt to find by slug
                $this->eventModel->db->query('SELECT e.*, u.name AS organizer_name FROM events e LEFT JOIN users u ON u.id = e.organizer_id WHERE e.slug = :slug LIMIT 1');
                $this->eventModel->db->bind(':slug', $identifier);
                $event = $this->eventModel->db->single();
            }

            if(!$event){
                http_response_code(404);
                echo 'Event not found.';
                return;
            }

            $data = ['event' => $event];
            $this->view('event/show', $data);
        }
    }

?>