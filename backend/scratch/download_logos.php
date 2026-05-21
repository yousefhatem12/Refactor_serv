<?php

$logos = [
    'ahli_egypt.png' => 'https://companieslogo.com/img/orig/NBE.EG-c29eb77b.png',
    'misr_egypt.jpg' => 'https://companieslogo.com/img/orig/BMISR.EG-c4ecb816.png',
    'credit_agricole.jpg' => 'https://upload.wikimedia.org/wikipedia/commons/thumb/c/ca/Cr%C3%A9dit_Agricole.svg/512px-Cr%C3%A9dit_Agricole.svg.png',
    'united_bank.jpg' => 'https://upload.wikimedia.org/wikipedia/commons/thumb/1/1a/The_United_Bank_of_Egypt_Logo.svg/512px-The_United_Bank_of_Egypt_Logo.svg.png',
    'egypt_post.jpg' => 'https://companieslogo.com/img/orig/EGYPTPOST.EG-c38d41de.png',
    'orange_cash.png' => 'https://upload.wikimedia.org/wikipedia/commons/thumb/c/c8/Orange_logo.svg/512px-Orange_logo.svg.png',
    'etisalat_cash.jpg' => 'https://upload.wikimedia.org/wikipedia/commons/thumb/c/cd/Etisalat_by_e%26_logo.svg/512px-Etisalat_by_e%26_logo.svg.png',
    'vodafone_cash.png' => 'https://upload.wikimedia.org/wikipedia/commons/thumb/a/a6/Vodafone_icon.svg/512px-Vodafone_icon.svg.png',
    'rajhi_ksa.png' => 'https://upload.wikimedia.org/wikipedia/commons/thumb/c/cc/Al_Rajhi_Bank_Logo.svg/512px-Al_Rajhi_Bank_Logo.svg.png',
    'ahli_ksa.jpg' => 'https://upload.wikimedia.org/wikipedia/commons/thumb/8/87/The_Saudi_National_Bank_Logo.svg/512px-The_Saudi_National_Bank_Logo.svg.png',
    'bilad_ksa.jpg' => 'https://upload.wikimedia.org/wikipedia/commons/thumb/2/23/Bank_Albilad_Logo.svg/512px-Bank_Albilad_Logo.svg.png',
    'western_union.jpg' => 'https://upload.wikimedia.org/wikipedia/commons/thumb/a/af/Western_Union_logo.svg/512px-Western_Union_logo.svg.png',
    'moneygram.jpg' => 'https://upload.wikimedia.org/wikipedia/commons/thumb/8/82/MoneyGram_Logo.svg/512px-MoneyGram_Logo.svg.png'
];

$dir = __DIR__ . '/../../public/images/banks/';
if (!file_exists($dir)) {
    mkdir($dir, 0777, true);
}

foreach ($logos as $filename => $url) {
    echo "Downloading {$filename}... ";
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64)');
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    $data = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if ($httpCode === 200 && $data) {
        file_put_contents($dir . $filename, $data);
        echo "Success!\n";
    } else {
        echo "Failed with HTTP code {$httpCode}\n";
    }
}
