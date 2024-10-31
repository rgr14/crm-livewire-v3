<?php

function obfuscate_email(?string $email = null): string
{
    if (!$email) {
        return '';
    }

    $splitted = explode('@', $email);

    if (sizeof($splitted) != 2) {
        return '';
    }

    $firstPart = $splitted[0];
    $qty = (int) floor(strlen($firstPart) * 0.75);
    $remaining = strlen($firstPart) - $qty;
    $maskedFirstPart = substr($firstPart, 0, $remaining) . str_repeat('*', $qty);


    $secondPart = $splitted[1];
    $qty = (int) floor(strlen($secondPart) * 0.75);
    $remaining = strlen($secondPart) - $qty;
    $maskedSecondtPart = str_repeat('*', $qty) . substr($secondPart, $remaining * -1, $remaining);

    return $maskedFirstPart . '@' . $maskedSecondtPart;
}
