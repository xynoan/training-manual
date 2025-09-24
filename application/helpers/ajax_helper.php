<?php

function generateAjaxPagination($current_page, $total_pages)
{
    if ($total_pages <= 1) return '';

    $pagination = '<nav aria-label="Page navigation"><ul class="pagination">';

    // Previous button - disabled if on first page or no pages
    if ($current_page > 1 && $total_pages > 0) {
        $pagination .= '<li class="page-item"><a class="page-link ajax-page" href="#" data-page="' . ($current_page - 1) . '">&laquo; Previous</a></li>';
    } else {
        $pagination .= '<li class="page-item disabled"><span class="page-link">&laquo; Previous</span></li>';
    }

    $start = max(1, $current_page - 2);
    $end = min($total_pages, $current_page + 2);

    if ($start > 1) {
        $pagination .= '<li class="page-item"><a class="page-link ajax-page" href="#" data-page="1">1</a></li>';
        if ($start > 2) {
            $pagination .= '<li class="page-item disabled"><span class="page-link">...</span></li>';
        }
    }

    for ($i = $start; $i <= $end; $i++) {
        $active = ($i == $current_page) ? ' active' : '';
        $pagination .= '<li class="page-item' . $active . '"><a class="page-link ajax-page" href="#" data-page="' . $i . '">' . $i . '</a></li>';
    }

    if ($end < $total_pages) {
        if ($end < $total_pages - 1) {
            $pagination .= '<li class="page-item disabled"><span class="page-link">...</span></li>';
        }
        $pagination .= '<li class="page-item"><a class="page-link ajax-page" href="#" data-page="' . $total_pages . '">' . $total_pages . '</a></li>';
    }

    // Next button - disabled if on last page or no pages
    if ($current_page < $total_pages && $total_pages > 0) {
        $pagination .= '<li class="page-item"><a class="page-link ajax-page" href="#" data-page="' . ($current_page + 1) . '">Next &raquo;</a></li>';
    } else {
        $pagination .= '<li class="page-item disabled"><span class="page-link">Next &raquo;</span></li>';
    }

    $pagination .= '</ul></nav>';

    return $pagination;
}
