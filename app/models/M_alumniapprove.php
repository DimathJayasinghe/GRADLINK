<?php
    class M_alumniapprove {
        private $db;

        public function __construct() {
            $this->db = new Database();
        }

        /**
         * Get pending alumni approval requests.
         * Return as an indexed array of stdClass with keys:
         * - req_id (int|string)
         * - Name, Batch, profile, status, email, display_name, bio, nic, student_no
         */
        public function getPendingRequests(): array {
            $this->db->query("SELECT id, name, email, display_name, profile_image, bio, nic, batch_no, status FROM unregisted_alumni WHERE status = 'pending' ORDER BY created_at DESC");
            $rows = [];
            try {
                $rows = $this->db->resultSet();
            } catch (Exception $e) {
                return [];
            }

            $list = [];
            foreach ($rows as $r) {
                $obj = new stdClass();
                $obj->req_id = $r->id;
                $obj->Name = $r->name;
                $obj->Batch = isset($r->batch_no) && $r->batch_no !== null ? ('Batch ' . $r->batch_no) : 'Batch -';
                $obj->profile = $r->profile_image ?? 'default.jpg';
                $obj->status = ucfirst(strtolower($r->status ?? 'pending'));
                $obj->email = $r->email;
                $obj->display_name = $r->display_name ?? $r->name;
                $obj->bio = $r->bio ?? '';
                $obj->nic = $r->nic ?? '';
                $obj->student_no = '';
                $list[] = $obj;
            }
            return $list;
        }

        /**
         * Fetch one request by ID from the list
         */
        public function getRequestById($req_id) {
            $this->db->query("SELECT id, name, email, display_name, profile_image, bio, nic, batch_no, status FROM unregisted_alumni WHERE id = :id LIMIT 1");
            $this->db->bind(':id', $req_id);
            try {
                $r = $this->db->single();
                if (!$r) return null;
            } catch (Exception $e) {
                return null;
            }
            $obj = new stdClass();
            $obj->req_id = $r->id;
            $obj->Name = $r->name;
            $obj->Batch = isset($r->batch_no) && $r->batch_no !== null ? ('Batch ' . $r->batch_no) : 'Batch -';
            $obj->profile = $r->profile_image ?? 'default.jpg';
            $obj->status = ucfirst(strtolower($r->status ?? 'pending'));
            $obj->email = $r->email;
            $obj->display_name = $r->display_name ?? $r->name;
            $obj->bio = $r->bio ?? '';
            $obj->nic = $r->nic ?? '';
            $obj->student_no = '';
            return $obj;
        }
    }
?>