<?php

function _cleanup_session_data($obj)
{
    _cleanup_temp_files($obj);
    $obj->session->unset_userdata('uploaded_files');
    $obj->session->unset_userdata('temp_files');
}
