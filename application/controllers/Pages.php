<?php

class Pages extends CI_Controller
{
    public function index()
    {
        _cleanup_temp_files($this);
        $this->session->unset_userdata('uploaded_files');
        $this->session->unset_userdata('temp_files');

        $search = $this->input->get('search') ?: $this->input->post('search');
        $dates = $this->input->get('dates') ?: $this->input->post('dates');

        $date_from = null;
        $date_to = null;

        if (!empty($dates)) {
            if (strpos($dates, ' - ') !== false) {
                $date_parts = explode(' - ', $dates);
                if (count($date_parts) == 2) {
                    $date_from = date('Y-m-d H:i:s', strtotime(trim($date_parts[0])));
                    $date_to = date('Y-m-d H:i:s', strtotime(trim($date_parts[1])));
                }
            } else {
                $date_from = date('Y-m-d H:i:s', strtotime($dates));
                $date_to = $date_from;
            }
        }

        $uri_segment = 1;
        $config['total_rows'] = $this->Training_model->count_all_trainings($search, $date_from, $date_to);
        $config['per_page'] = 10;
        $config['uri_segment'] = $uri_segment;

        $query_params = [];
        if (!empty($search)) {
            $query_params['search'] = $search;
        }
        if (!empty($dates)) {
            $query_params['dates'] = $dates;
        }

        if (!empty($query_params)) {
            $query_string = http_build_query($query_params);
            $config['base_url'] = base_url() . '?' . $query_string . '&page=';
            $config['page_query_string'] = TRUE;
            $config['query_string_segment'] = 'page';
        }

        $this->pagination->initialize($config);

        $page_num = ($this->uri->segment($uri_segment)) ? $this->uri->segment($uri_segment) : 1;
        // convert page number (1,2,3...) into offset (0,10,20...)
        $offset = ($page_num - 1) * $config['per_page'];

        $data = [
            'title' => "TRAINING MANUAL",
            'trainings' => $this->Training_model->get_all_trainings_paginated($config['per_page'], $offset, $search, $date_from, $date_to),
            'pagination' => $this->pagination->create_links(),
            'search' => $search,
            'dates' => $dates,
            'date_from' => $date_from,
            'date_to' => $date_to,
            'uploaded_files' => []
        ];

        $this->load->view('templates/header', $data);
        $this->load->view('pages/index', $data);
        $this->load->view('templates/footer', $data);
    }

}
