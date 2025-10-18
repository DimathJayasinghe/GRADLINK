<?php
    class M_alumniapprove {
        private $db;

        public function __construct() {
            // Use Database if needed later; for now we'll return mock data until schema is ready
            $this->db = new Database();
        }

        /**
         * Get pending alumni approval requests.
         * Return as an indexed array of stdClass with keys:
         * - req_id (int|string)
         * - Name, Batch, profile, status, email, display_name, bio, nic, student_no
         */
        public function getPendingRequests(): array {
            // TODO: replace with real DB once available
            $list = [
                (object)[
                    'req_id' => 101,
                    'Name' => 'Jane Doe',
                    'Batch' => 'Batch 2019',
                    'profile' => 'default.jpg',
                    'status' => 'Pending',
                    'email' => 'jane.doe@example.com',
                    'display_name' => 'Jane D.',
                    'bio' => 'Software engineer passionate about web tech.',
                    'nic' => '902345678V',
                    'student_no' => '2019/CSC/012'
                ],
                (object)[
                    'req_id' => 102,
                    'Name' => 'John Smith',
                    'Batch' => 'Batch 2018',
                    'profile' => 'default.jpg',
                    'status' => 'Pending',
                    'email' => 'john.smith@example.com',
                    'display_name' => 'JSmith',
                    'bio' => 'Data scientist and ML enthusiast.',
                    'nic' => '882223334V',
                    'student_no' => '2018/CSC/045'
                ],
                (object)[
                    'req_id' => 103,
                    'Name' => 'Amaya Perera',
                    'Batch' => 'Batch 2020',
                    'profile' => 'default.jpg',
                    'status' => 'Pending',
                    'email' => 'amaya.p@example.com',
                    'display_name' => 'AmayaP',
                    'bio' => 'Frontend developer and UI designer.',
                    'nic' => '002112345V',
                    'student_no' => '2020/CSC/077'
                ],
            ];
            return $list;
        }

        /**
         * Fetch one request by ID from the list
         */
        public function getRequestById($req_id) {
            foreach ($this->getPendingRequests() as $r) {
                if ((string)$r->req_id === (string)$req_id) return $r;
            }
            return null;
        }
    }
?>