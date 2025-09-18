<?php

class Pages extends CI_Controller
{
    public function index()
    {
        _cleanup_temp_files($this);
        $this->session->unset_userdata('uploaded_files');
        $this->session->unset_userdata('temp_files');

        $search = $this->input->get('search') ?: $this->input->post('search');
        $datetimes = $this->input->get('datetimes') ?: $this->input->post('datetimes');

        $date_from = null;
        $date_to = null;

        if (!empty($datetimes)) {
            if (strpos($datetimes, ' - ') !== false) {
                $date_parts = explode(' - ', $datetimes);
                if (count($date_parts) == 2) {
                    // Parse dates from MM/DD/YYYY HH:mm format
                    $start_date = DateTime::createFromFormat('m/d/Y H:i', trim($date_parts[0]));
                    $end_date = DateTime::createFromFormat('m/d/Y H:i', trim($date_parts[1]));
                    
                    if ($start_date !== false && $end_date !== false) {
                        $date_from = $start_date->format('Y-m-d H:i:s');
                        $date_to = $end_date->format('Y-m-d H:i:s');
                    } else {
                        // Fallback to strtotime if DateTime::createFromFormat fails
                        $date_from = date('Y-m-d H:i:s', strtotime(trim($date_parts[0])));
                        $date_to = date('Y-m-d H:i:s', strtotime(trim($date_parts[1])));
                    }
                }
            } else {
                // Parse single date from MM/DD/YYYY HH:mm format
                $single_date = DateTime::createFromFormat('m/d/Y H:i', $datetimes);
                if ($single_date !== false) {
                    $date_from = $single_date->format('Y-m-d H:i:s');
                    $date_to = $date_from;
                } else {
                    // Fallback to strtotime
                    $date_from = date('Y-m-d H:i:s', strtotime($datetimes));
                    $date_to = $date_from;
                }
            }
        }

        $config['total_rows'] = $this->Training_model->count_all_trainings($search, $date_from, $date_to);
        $config['per_page'] = 10;
        $config['use_page_numbers'] = TRUE;

        $query_params = [];
        if (!empty($search)) {
            $query_params['search'] = $search;
        }
        if (!empty($datetimes)) {
            $query_params['datetimes'] = $datetimes;
        }

        // Always use query string pagination for consistency with AJAX
        $query_string = !empty($query_params) ? http_build_query($query_params) . '&' : '';
        $config['base_url'] = base_url() . '?' . $query_string . 'page=';
        $config['page_query_string'] = TRUE;
        $config['query_string_segment'] = 'page';

        $this->pagination->initialize($config);

        // Get page number from query string
        $page_num = $this->input->get('page') ? (int)$this->input->get('page') : 1;
        // convert page number (1,2,3...) into offset (0,10,20...)
        $offset = ($page_num - 1) * $config['per_page'];

        // Use the same AJAX pagination function for consistency
        $total_pages = ceil($config['total_rows'] / $config['per_page']);
        $pagination = generateAjaxPagination($page_num, $total_pages);
        
        $data = [
            'title' => "TRAINING MANUAL",
            'trainings' => $this->Training_model->get_all_trainings_paginated($config['per_page'], $offset, $search, $date_from, $date_to),
            'pagination' => $pagination,
            'search' => $search,
            'datetimes' => $datetimes,
            'date_from' => $date_from,
            'date_to' => $date_to,
            'uploaded_files' => []
        ];

        $this->load->view('templates/header', $data);
        $this->load->view('pages/index', $data);
        $this->load->view('templates/footer', $data);
    }

}
