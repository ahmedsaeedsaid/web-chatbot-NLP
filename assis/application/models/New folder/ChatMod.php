<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class chatMod extends CI_Model {

    public function getChatLog($firstuserid, $seconduserid) {
        $this->db->select('*, messages.id');
        $this->db->from('messages');
        $this->db->where('(sender_id = ' . $firstuserid . ' OR sender_id = ' . $seconduserid . ') AND (reciever_id = ' . $firstuserid . ' OR reciever_id = ' . $seconduserid . ')');
        $this->db->join('chat', 'messages.chatID = chat.id');
        $this->db->order_by('messages.id', 'ASC');
        $query = $this->db->get();
        $logs = $query->result();
        $this->db->select(array('name', 'img', 'id'));
        $this->db->from('users');
        $this->db->where('id = ' . $firstuserid);
        $this->db->or_where('id = ' . $seconduserid);
        $query = $this->db->get();
        $users = $query->result();
        return array_merge($users, $logs);
    }

    public function refreshChatLog($firstuserid, $seconduserid, $id) {
        $this->db->select_max('id');
        $query = $this->db->get('messages');
        $currmaxid = $query->result()[0]->id;
        if ($id < $currmaxid) {
            $this->db->select('*');
            $this->db->from('messages');
            $this->db->where('(sender_id = ' . $firstuserid . ' OR sender_id = ' . $seconduserid . ') AND (reciever_id = ' . $firstuserid . ' OR reciever_id = ' . $seconduserid . ') AND (messages.id > ' . $id . ')');
            $this->db->join('chat', 'messages.chatID = chat.id');
            $query = $this->db->get();
            $logs = $query->result();
            return array_merge(array(array('id' => $currmaxid)), $logs);
        } else {
            return array(array('id' => $id));
        }
    }

    public function getMsg($chatID) {
        $this->db->select_max('id');
        $this->db->from('messages');
        $this->db->where('chatID = ' . $chatID);
        $query = $this->db->get();
        $id = $query->result()[0]->id;
        $this->db->select('message_body');
        $this->db->from('messages');
        $this->db->where('id = ' . $id);
        $query = $this->db->get();
        return $query->result()[0]->message_body;
    }

    public function insertMsg($chatID, $sender, $reciever, $msg) {
        $data = array(
            'message_body' => $msg,
            'sender_id' => $sender,
            'reciever_id' => $reciever,
            'chatID' => $chatID
        );
        $this->db->insert('messages', $data);
    }

    public function createChat($firstuserid, $seconduserid) {
        $data = array(
            'firstuserid' => $firstuserid,
            'seconduserid' => $seconduserid
        );
        $this->db->insert('chat', $data);
        return $this->db->insert_id();
    }

}
