<html>

<head>
    <title>Training Manual</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-LN+7fdVzj6u52u30Kp6M/trliBMCMKTyK833zpbD+pXdCLuTusPj697FH4R/5mcr" crossorigin="anonymous">
</head>

<style>
    .dropdown-menu a:active {
        background-color: white;
    }

    /* Common styles for forms */
    textarea::placeholder {
        opacity: 0.7 !important;
    }

    /* Floating Success Alert */
    .success-alert-floating {
        position: fixed;
        top: 20px;
        right: 20px;
        background: linear-gradient(135deg, #00b09b, #96c93d);
        color: white;
        padding: 20px 25px;
        border-radius: 15px;
        box-shadow: 0 15px 35px rgba(0, 176, 155, 0.3);
        transform: translateX(400px);
        transition: all 0.5s cubic-bezier(0.68, -0.55, 0.265, 1.55);
        z-index: 1000;
        max-width: 350px;
    }

    .success-alert-floating.show {
        transform: translateX(0);
    }

    .success-alert-floating .success-icon {
        width: 50px;
        height: 50px;
        background: rgba(255, 255, 255, 0.2);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-bottom: 15px;
        animation: pulse 2s infinite;
    }

    /* Upload box styles */
    .upload-box {
        border: 2px dashed #d3d3d3;
        border-radius: 20px;
        padding: 40px;
        text-align: center;
        background: #fafafa;
        cursor: pointer;
        transition: border-color 0.3s ease;
    }

    .upload-box.error {
        border: 2px dashed #e74c3c;
        border-radius: 20px;
        padding: 40px;
        text-align: center;
        background: #fff5f5;
        cursor: pointer;
        transition: border-color 0.3s ease, background 0.3s ease;
    }

    .upload-box.error::after {
        content: "⚠ No file uploaded!";
        display: block;
        color: #e74c3c;
        font-size: 14px;
        margin-top: 10px;
        font-weight: bold;
    }

    .upload-box.dragover {
        border-color: #0d6efd;
        background: #f0f8ff;
    }

    .upload-box svg {
        width: 50px;
        height: 50px;
        fill: #adb5bd;
    }

    .upload-box p {
        margin-top: 10px;
        color: #adb5bd;
        font-weight: 500;
    }

    #fileInput {
        display: none;
    }

    #fileList .file-box {
        width: 150px;
        word-break: break-word;
        background: #f8f9fa;
    }
</style>

<body class="container mt-5">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h1 class="fw-bold"><?php echo $title; ?></h1>