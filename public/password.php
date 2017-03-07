<?php

if (isset($argv[1])) {
    $password = (string) $argv[1]; // use the command line argument for ID
} else {
    do {
        print("Enter a Password: ");
        $password = fgets(STDIN); // prompt the user for an ID
    } while (empty($password) || $password == PHP_EOL);
}

echo "Password: " . $password . " Hashed: " . password_hash($password, PASSWORD_BCRYPT) . "\n";
