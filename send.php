<?php
// Ustawienia e-mail
$odbiorca = "kontakt@vocaviolin.pl"; // TWÓJ ADRES E-MAIL
$temat = "NOWA REZERWACJA / ZAPYTANIE ze strony Vocaviolin";
$headers = "From: Twoja Strona <no-reply@twojadomena.pl>\r\n"; // ZMIEŃ NA SWOJĄ DOMENĘ
$headers .= "Reply-To: " . $_POST['email'] . "\r\n"; // Umożliwienie odpowiedzi na e-mail klienta
$headers .= "Content-Type: text/plain; charset=UTF-8\r\n";

// Sprawdzenie, czy dane zostały wysłane metodą POST
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    // Zbieranie danych z formularza
    // Dane z inputów są dostępne w tablicy $_POST
    $imie_nazwisko = filter_var(trim($_POST['name']), FILTER_SANITIZE_STRING);
    $data_wydarzenia = filter_var(trim($_POST['datetime']), FILTER_SANITIZE_STRING);
    $lokalizacja = filter_var(trim($_POST['location']), FILTER_SANITIZE_STRING);
    $uwagi = filter_var(trim($_POST['notes']), FILTER_SANITIZE_STRING);
    
    // Walidacja - sprawdzenie, czy wymagane pola nie są puste
    if (empty($imie_nazwisko) || empty($data_wydarzenia) || empty($lokalizacja)) {
        // Przekierowanie z powrotem do strony głównej z komunikatem o błędzie
        header("Location: index.html?success=false&error=missing_fields");
        exit;
    }
    
    // Budowanie treści wiadomości
    $wiadomosc = "Otrzymano nową rezerwację/zapytanie:\n\n";
    $wiadomosc .= "Imię i nazwisko: " . $imie_nazwisko . "\n";
    $wiadomosc .= "Data i godzina wydarzenia: " . $data_wydarzenia . "\n";
    $wiadomosc .= "Lokalizacja: " . $lokalizacja . "\n";
    $wiadomosc .= "Dodatkowe uwagi: " . $uwagi . "\n";

    // UWAGA: Twój formularz nie zawiera pola email, 
    // więc nie można go użyć do Reply-To, ani go podać w treści.
    // Dodaj to pole do HTML jeśli jest potrzebne!
    
    // Wysłanie e-maila
    if (mail($odbiorca, $temat, $wiadomosc, $headers)) {
        // Sukces: Przekierowanie z powrotem do strony głównej z komunikatem o sukcesie
        header("Location: index.html?success=true");
    } else {
        // Błąd: Przekierowanie z komunikatem o błędzie wysyłki
        header("Location: index.html?success=false&error=mail_failed");
    }

} else {
    // Próba bezpośredniego dostępu do send.php
    http_response_code(403);
    echo "Dostęp zabroniony.";
}
?>