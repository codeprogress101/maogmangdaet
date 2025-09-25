<?php
$plainPassword = "Admin#2025!";
$hash = password_hash($plainPassword, PASSWORD_ARGON2ID);
echo "Plain password: $plainPassword\n";
echo "Argon2id hash: $hash\n";