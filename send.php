<?php
// Prostsza i bezpieczniejsza obsługa formularza kontaktowego
$odbiorca = "kontakt@vocaviolin.pl"; // Odbiorca wiadomości
$temat = "NOWA REZERWACJA / ZAPYTANIE ze strony Vocaviolin";

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo 'Metoda niedozwolona.';
    exit;
}

// Pobierz i zabezpiecz dane
$name = isset($_POST['name']) ? trim($_POST['name']) : '';
$email = isset($_POST['email']) ? trim($_POST['email']) : '';
$datetime = isset($_POST['datetime']) ? trim($_POST['datetime']) : '';
$location = isset($_POST['location']) ? trim($_POST['location']) : '';
$notes = isset($_POST['notes']) ? trim($_POST['notes']) : '';

// Proste czyszczenie wejścia
$name = filter_var($name, FILTER_SANITIZE_STRING);
$location = filter_var($location, FILTER_SANITIZE_STRING);
$notes = filter_var($notes, FILTER_SANITIZE_STRING);

// Walidacja podstawowa
if (empty($name) || empty($datetime) || empty($location)) {
    header('Location: index.html?success=false&error=missing_fields#kontakt');
    exit;
}

// Walidacja e-mail (jeśli podano)
$replyToHeader = '';
if (!empty($email)) {
    $email = filter_var($email, FILTER_SANITIZE_EMAIL);
    if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
        // unikamy wstrzyknięć nagłówków
        $safeEmail = str_replace(array("\r", "\n"), '', $email);
        $replyToHeader = "Reply-To: " . $safeEmail . "\r\n";
    }
}

// Przygotuj nagłówki
$headers = "From: Vocaviolin <no-reply@vocaviolin.pl>\r\n";
$headers .= $replyToHeader;
$headers .= "Content-Type: text/plain; charset=UTF-8\r\n";

// Przygotuj treść
$message = "Otrzymano nowe zapytanie z formularza kontaktowego:\n\n";
$message .= "Imię i nazwisko: " . $name . "\n";
$message .= "E-mail: " . ($email ?: 'Brak') . "\n";
$message .= "Data i godzina wydarzenia: " . $datetime . "\n";
$message .= "Lokalizacja: " . $location . "\n\n";
$message .= "Dodatkowe uwagi:\n" . $notes . "\n";

// Wyślij e-mail
if (mail($odbiorca, $temat, $message, $headers)) {
    header('Location: index.html?success=true#kontakt');
    exit;
} else {
    header('Location: index.html?success=false&error=mail_failed#kontakt');
    exit;
}

?>