<?php

require './vendor/autoload.php';

use Sushilk\Supabase\Client;

$c = new Client(
    'https://grpskktdnkpwfceqcdbx.supabase.co',
    'eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJpc3MiOiJzdXBhYmFzZSIsInJlZiI6ImdycHNra3Rkbmtwd2ZjZXFjZGJ4Iiwicm9sZSI6InNlcnZpY2Vfcm9sZSIsImlhdCI6MTc3ODg1MTMwOCwiZXhwIjoyMDk0NDI3MzA4fQ.LszzV2s9tHBSd4bg99XIBhfXvI4dGCm_vKAK67gAjOg'
);

$co = $c->table('users')->select()->where('id', 4)->get();
var_dump($co);

/*
$c->post('users', [
    'name' => 'John',
    'email' => 'john@example.com'
]);
 */
