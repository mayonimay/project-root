<?php
session_start();

if(isset($_POST['submit'])) {
    $allowed_ext = ['zip'];
    $file = $_FILES['zipfile'];
    $file_ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));

    if(!in_array($file_ext, $allowed_ext)) {
        $_SESSION['message'] = 'Hanya file ZIP yang diperbolehkan!';
        $_SESSION['message_type'] = 'error';
        header('Location: index.php');
        exit();
    }

    $temp_dir = 'temp/' . time();
    mkdir($temp_dir, 0777, true);

    $zip = new ZipArchive();
    if($zip->open($file['tmp_name']) === TRUE) {
        $zip->extractTo($temp_dir);
        $zip->close();
        
        $structure = "<pre>" . createStructure($temp_dir) . "</pre>";
        $_SESSION['structure'] = $structure;
        $_SESSION['message'] = 'File berhasil dianalisis!';
        $_SESSION['message_type'] = 'success';
        
        // Membersihkan direktori temporary
        deleteDirectory($temp_dir);
    } else {
        $_SESSION['message'] = 'Gagal membuka file ZIP!';
        $_SESSION['message_type'] = 'error';
    }
    
    header('Location: index.php');
    exit();
}

function createStructure($dir, $level = 0, $isLast = true, $prefix = '') {
    $result = '';
    $files = scandir($dir);
    $files = array_diff($files, ['.', '..']);
    $totalItems = count($files);
    $counter = 0;

    foreach($files as $file) {
        $counter++;
        $isLastItem = ($counter == $totalItems);
        $path = $dir . '/' . $file;
        
        // Menentukan simbol yang akan digunakan
        $currentPrefix = $isLastItem ? '└── ' : '├── ';
        $childPrefix = $isLastItem ? '    ' : '│   ';
        
        // Membuat baris untuk item saat ini
        $result .= $prefix . $currentPrefix;
        
        if(is_dir($path)) {
            $result .= $file . "/<br>";
            // Rekursif untuk konten folder dengan prefix yang sesuai
            $result .= createStructure(
                $path, 
                $level + 1, 
                $isLastItem,
                $prefix . $childPrefix
            );
        } else {
            $result .= $file . "<br>";
        }
    }
    
    return $result;
}

function deleteDirectory($dir) {
    if(!is_dir($dir)) return;
    
    $files = array_diff(scandir($dir), ['.', '..']);
    foreach($files as $file) {
        $path = $dir . '/' . $file;
        is_dir($path) ? deleteDirectory($path) : unlink($path);
    }
    
    return rmdir($dir);
} 