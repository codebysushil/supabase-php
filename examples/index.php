<?php

declare(strict_types=1);

require './vendor/autoload.php';

use Sushilk\Supabase\Client;

$client = new Client(
    'https://grpskktdnkpwfceqcdbx.supabase.co',
    'eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJpc3MiOiJzdXBhYmFzZSIsInJlZiI6ImdycHNra3Rkbmtwd2ZjZXFjZGJ4Iiwicm9sZSI6InNlcnZpY2Vfcm9sZSIsImlhdCI6MTc3ODg1MTMwOCwiZXhwIjoyMDk0NDI3MzA4fQ.LszzV2s9tHBSd4bg99XIBhfXvI4dGCm_vKAK67gAjOg'
);

$data = [
    'name' => 'John',
    'email' => 'john@example.com',
];

// Fetching all data
$result = $client->from('users')->select()->get();
var_dump($result);

// Fetching all data by columns name
$result = $client->from('users')->select('id,name, email')->get();
var_dump($result);

// Fetching single data by columns name
$result = $client->from('users')->select('name')->where('id', 2)->get();
var_dump($result);

// Post or insert data to Supabase tables.
$client->from('users')->insert($data);

// Update data
$client->from('users')->where('id', 2)->update([
    'name' => 'Sushil',
    'email' => 'sushil@gmail.com',
]);

// Delete data
$client->from('users')->where('id', 3)->delete();
