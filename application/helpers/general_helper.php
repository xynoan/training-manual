<?php

function dd($data)
{
    echo '<pre>';
    var_dump($data);
    echo '</pre>';
    die();
}

function get_file_icon($extension)
{
    $extension = strtolower($extension);
    
    switch ($extension) {
        case 'pdf':
            return '<i class="fas fa-file-pdf text-danger" title="PDF File"></i>';
            
        case 'ppt':
        case 'pptx':
            return '<i class="fas fa-file-powerpoint text-warning" title="PowerPoint Presentation"></i>';
            
        case 'doc':
        case 'docx':
            return '<i class="fas fa-file-word text-primary" title="Word Document"></i>';
            
        case 'xls':
        case 'xlsx':
            return '<i class="fas fa-file-excel text-success" title="Excel Spreadsheet"></i>';
            
        case 'txt':
            return '<i class="fas fa-file-alt text-secondary" title="Text File"></i>';
            
        case 'jpg':
        case 'jpeg':
        case 'png':
        case 'gif':
        case 'bmp':
        case 'webp':
        case 'svg':
            return '<i class="fas fa-file-image text-info" title="Image File"></i>';
            
        case 'mp4':
        case 'avi':
        case 'mov':
        case 'wmv':
        case 'flv':
        case 'webm':
            return '<i class="fas fa-file-video text-purple" title="Video File"></i>';
            
        case 'mp3':
        case 'wav':
        case 'flac':
        case 'aac':
        case 'ogg':
            return '<i class="fas fa-file-audio text-success" title="Audio File"></i>';
            
        case 'zip':
        case 'rar':
        case '7z':
        case 'tar':
        case 'gz':
            return '<i class="fas fa-file-archive text-dark" title="Archive File"></i>';
            
        case 'html':
        case 'htm':
        case 'css':
        case 'js':
        case 'php':
        case 'py':
        case 'java':
        case 'cpp':
        case 'c':
        case 'cs':
        case 'rb':
        case 'go':
        case 'rs':
            return '<i class="fas fa-file-code text-primary" title="Code File"></i>';
            
        default:
            return '<i class="fas fa-file text-muted" title="Unknown File Type"></i>';
    }
}