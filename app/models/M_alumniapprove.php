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
                (object)[
                    'req_id' => 104,
                    'Name' => 'Sameer Khan',
                    'Batch' => 'Batch 2017',
                    'profile' => 'default.jpg',
                    'status' => 'Pending',
                    'email' => 'sameer.khan@example.com',
                    'display_name' => 'SameerK',
                    'bio' => 'Backend developer, loves APIs.',
                    'nic' => '871234567V',
                    'student_no' => '2017/CSC/021'
                ],
                (object)[
                    'req_id' => 105,
                    'Name' => 'Nadeesha Silva',
                    'Batch' => 'Batch 2016',
                    'profile' => 'default.jpg',
                    'status' => 'Pending',
                    'email' => 'nadeesha.s@example.com',
                    'display_name' => 'Nadee',
                    'bio' => 'DevOps engineer and cloud tinkerer.',
                    'nic' => '861112223V',
                    'student_no' => '2016/CSC/063'
                ],
                (object)[
                    'req_id' => 106,
                    'Name' => 'Kasun Jayawardena',
                    'Batch' => 'Batch 2015',
                    'profile' => 'default.jpg',
                    'status' => 'Pending',
                    'email' => 'kasun.j@example.com',
                    'display_name' => 'KasunJ',
                    'bio' => 'Full-stack dev working on fintech.',
                    'nic' => '851234568V',
                    'student_no' => '2015/CSC/099'
                ],
                (object)[
                    'req_id' => 107,
                    'Name' => 'Tharushi Fernando',
                    'Batch' => 'Batch 2021',
                    'profile' => 'default.jpg',
                    'status' => 'Pending',
                    'email' => 'tharushi.f@example.com',
                    'display_name' => 'TharuF',
                    'bio' => 'Mobile app developer (Flutter).',
                    'nic' => '012345678V',
                    'student_no' => '2021/CSC/034'
                ],
                (object)[
                    'req_id' => 108,
                    'Name' => 'Ishan De Alwis',
                    'Batch' => 'Batch 2018',
                    'profile' => 'default.jpg',
                    'status' => 'Pending',
                    'email' => 'ishan.d@example.com',
                    'display_name' => 'IshanDA',
                    'bio' => 'SRE with a passion for reliability.',
                    'nic' => '882567890V',
                    'student_no' => '2018/CSC/072'
                ],
                (object)[
                    'req_id' => 109,
                    'Name' => 'Dinithi Wijesinghe',
                    'Batch' => 'Batch 2022',
                    'profile' => 'default.jpg',
                    'status' => 'Pending',
                    'email' => 'dinithi.w@example.com',
                    'display_name' => 'DinithiW',
                    'bio' => 'UX researcher and designer.',
                    'nic' => '022334455V',
                    'student_no' => '2022/CSC/056'
                ],
                (object)[
                    'req_id' => 110,
                    'Name' => 'Akhil Raj',
                    'Batch' => 'Batch 2017',
                    'profile' => 'default.jpg',
                    'status' => 'Pending',
                    'email' => 'akhil.raj@example.com',
                    'display_name' => 'AkhilR',
                    'bio' => 'Data engineer; pipelines and ETL.',
                    'nic' => '871998877V',
                    'student_no' => '2017/CSC/110'
                ],
                (object)[
                    'req_id' => 111,
                    'Name' => 'Mihiri Gunasekara',
                    'Batch' => 'Batch 2019',
                    'profile' => 'default.jpg',
                    'status' => 'Pending',
                    'email' => 'mihiri.g@example.com',
                    'display_name' => 'MihiriG',
                    'bio' => 'Product manager with tech background.',
                    'nic' => '902222111V',
                    'student_no' => '2019/CSC/099'
                ],
                (object)[
                    'req_id' => 112,
                    'Name' => 'Ruwan Pathirana',
                    'Batch' => 'Batch 2014',
                    'profile' => 'default.jpg',
                    'status' => 'Pending',
                    'email' => 'ruwan.p@example.com',
                    'display_name' => 'RuwanP',
                    'bio' => 'Architecting scalable platforms.',
                    'nic' => '841122333V',
                    'student_no' => '2014/CSC/041'
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