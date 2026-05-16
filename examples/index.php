<?php

require './vendor/autoload.php';

use Sushilk\Supabase\Client;

$client = new Client($_ENV['URL'], $_ENV['API_KEY']);

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
$client->table('users')->insert($data);

// Update data
$client->from('users')->where('id', 2)->update([
    'name' => 'Sushil',
    'email' => 'sushil@gmail.com',
]);

// Delete data
$client->from('users')->where('id', 3)->delete();
