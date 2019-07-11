<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class DataTablesMod extends CI_Model {

    var $table = 'courses';
    var $select = array('c_id', 'c_name', 'c_picture', 'c_description');

    public function make_query() {
        $this->db->select($this->select);
        $this->db->from($this->table);

        if (isset($_POST['search']['value'])) {
            $st = $_POST['search']['value'];
            $st = trim($st);
            $space = strrpos($st, " ");
            $columns = array('c_name');
            $where = '(';
            if ($space) {
                $st = explode(" ", $st);
                $col_len = count($columns);
                $st_len = count($st);
                for ($k = 0; $k < $st_len; $k++) {
                    for ($i = 0; $i < $col_len; $i++) {
                        $where .= "`" . $columns[$i] . "` LIKE '" . $st[$k] . "%'";
                        if ($i < $col_len - 1) {
                            $where .= ' OR ';
                        }
                    }
                    if ($k < $st_len - 1) {
                        $where .= ') AND (';
                    }
                }
                $where .= ')';
                $this->db->where($where);
            } else {
                for ($i = 0; $i < count($columns); $i++) {
                    $this->db->or_like($columns[$i], $st);
                }
            }
        }

        if (isset($_POST['order'])) {
            $this->db->order_by($this->select[$_POST['order'][0]['column']], $_POST['order'][0]['dir']);
        } else {
            $this->db->order_by('c_id', 'ASC');
        }
    }

    public function make_datatables() {
        $this->make_query();
        if (isset($_POST['length'])) {
            if ($_POST['length'] != -1) {
                $this->db->limit($_POST['length'], $_POST['start']);
            }
        }
        $query = $this->db->get();
        return $query->result();
    }

    public function get_filtered_data() {
        $this->make_query();
        $query = $this->db->get();
        return $query->num_rows();
    }

    public function get_all_data() {
        $this->db->select('*');
        $this->db->from($this->table);
        return $this->db->count_all_results();
    }

}
