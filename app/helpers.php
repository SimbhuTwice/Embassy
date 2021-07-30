<?php

// Generate Random Password
function randomPassword() 
{
    // $alphabet = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890';
    $alphabet = 'abcdefghijklmnopqrstuvwxyz';
    $alphcaps = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $numbers = '1234567890';
    $splchars = '@_';
    $pass = array(); //remember to declare $pass as an array
    $alphaLength = strlen($alphabet) - 1; //put the length -1 in cache
    for ($i = 0; $i < 3; $i++) {
        $n = rand(0, $alphaLength);
        $pass[] = $alphabet[$n];
    }
    $alphaLength = strlen($alphcaps) - 1;
    for ($i = 0; $i < 2; $i++) {
        $n = rand(0, $alphaLength);
        $pass[] = $alphcaps[$n];
    }
    $alphaLength = strlen($numbers) - 1;
    for ($i = 0; $i < 2; $i++) {
        $n = rand(0, $alphaLength);
        $pass[] = $numbers[$n];
    }
    $alphaLength = strlen($splchars) - 1;
    for ($i = 0; $i < 1; $i++) {
        $n = rand(0, $alphaLength);
        $pass[] = $splchars[$n];
    }
    // return implode($pass); //turn the array into a string
    $pswd = implode($pass);
    $pswd = str_shuffle($pswd);
    return $pswd;
}
