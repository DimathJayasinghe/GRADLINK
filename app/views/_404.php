<!DOCTYPE html>
<html lang="en"><head>
<meta charset="utf-8"/>
<meta content="width=device-width, initial-scale=1.0" name="viewport"/>
<title>404 Page Not Found</title>
<script src="https://cdn.tailwindcss.com"></script>
<link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;700&amp;display=swap" rel="stylesheet"/>
<link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet"/>
<style>
        body {
            font-family: 'Roboto', sans-serif;
        }
    </style>
</head>
<body class="bg-white text-gray-800">
<div class="flex h-screen">
<main class="flex-grow flex flex-col justify-center items-center text-center px-4">
<h1 class="text-9xl font-bold text-gray-800">404</h1>
<h2 class="text-4xl font-light text-gray-800 mt-4">This Page Not Found!</h2>
<p class="text-gray-500 mt-4">The link might be corrupted,</p>
<p class="text-gray-500">or the page may have been removed</p>
<a class="mt-8 bg-gray-800 text-white py-2 px-6 text-sm font-semibold rounded-full hover:bg-gray-700 transition duration-300" href="<?php echo URLROOT; ?>">
                GO BACK HOME
            </a>
</main>
</div>

</body></html>