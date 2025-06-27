<?php
// ats-upload.php

// List of required skills/keywords
$required_keywords = ['php', 'javascript', 'mysql', 'html', 'css', 'python', 'java'];

// File upload handling
if (isset($_FILES['resume']) && $_FILES['resume']['error'] == 0) {
    $file = $_FILES['resume']['tmp_name'];
    $filename = $_FILES['resume']['name'];
    $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
    $text = '';

    if ($ext == 'pdf') {
        require 'vendor/autoload.php';
        $parser = new \Smalot\PdfParser\Parser();
        $pdf = $parser->parseFile($file);
        $text = $pdf->getText();
    } elseif ($ext == 'docx') {
        require 'vendor/autoload.php';
        $phpWord = \PhpOffice\PhpWord\IOFactory::load($file);
        $text = '';
        foreach ($phpWord->getSections() as $section) {
            $elements = $section->getElements();
            foreach ($elements as $element) {
                if (method_exists($element, 'getText')) {
                    $text .= $element->getText() . ' ';
                }
            }
        }
    } else {
        header("Location: resume.html?result=Invalid file type.");
        exit();
    }

    // Keyword matching
    $matches = 0;
    foreach ($required_keywords as $keyword) {
        if (stripos($text, $keyword) !== false) {
            $matches++;
        }
    }

    $result = ($matches >= 3) ? "You are shortlisted! ($matches skills matched)" : "Not shortlisted. ($matches skills matched)";
    header("Location: resume.html?result=" . urlencode($result));
    exit();
} else {
    header("Location: resume.html?result=File upload failed.");
    exit();
}
?> 