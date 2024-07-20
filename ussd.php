<?php

// Sample data
$languages = [
    "1" => "English",
    "2" => "French",
    "3" => "Kinyarwanda",
    "4" => "Swahili"
];

$phrases = [
    "1" => [
        "1" => ["Hello", "Həˈloʊ"],
        "2" => ["Good Morning", "ˌɡʊd ˈmɔːrnɪŋ"],
        "3" => ["How are you?", "haʊ ɑːr juː"]
    ],
    "2" => [
        "1" => ["Bonjour", "bɔ̃ʒuʁ"],
        "2" => ["Bonsoir", "bɔ̃swaʁ"],
        "3" => ["Comment ça va?", "kɔmɑ̃ sa va"]
    ]
    // Add more phrases for Kinyarwanda and Swahili
];

// Fetch parameters from the POST request
$sessionId = $_POST["sessionId"];
$serviceCode = $_POST["serviceCode"];
$phoneNumber = $_POST["phoneNumber"];
$text = $_POST["text"];

// Process the text to determine the USSD menu level
if ($text == "") {
    // First menu level
    $response = "CON Welcome to the Language Assistance Tool. Select a language:\n";
    foreach ($languages as $key => $value) {
        $response .= "$key. $value\n";
    }
} else {
    // Subsequent menu levels
    $text_parts = explode("*", $text);
    if (count($text_parts) == 1) {
        $language_id = $text_parts[0];
        $response = "CON Select a category:\n1. Greetings\n2. Directions\n3. Dining\n4. Emergencies";
    } elseif (count($text_parts) == 2) {
        $language_id = $text_parts[0];
        $category_id = $text_parts[1];
        $response = "CON Select a phrase:\n";
        foreach ($phrases[$category_id] as $key => $value) {
            $response .= "$key. " . $value[0] . "\n";
        }
    } elseif (count($text_parts) == 3) {
        $language_id = $text_parts[0];
        $category_id = $text_parts[1];
        $phrase_id = $text_parts[2];
        $phrase = $phrases[$category_id][$phrase_id][0];
        $pronunciation = $phrases[$category_id][$phrase_id][1];
        $response = "CON $phrase ($pronunciation)\n1. Send via SMS\n2. Back to main menu";
    } elseif (count($text_parts) == 4) {
        $option = $text_parts[3];
        if ($option == "1") {
            // Integrate with an SMS service to send the phrase via SMS
            $response = "END The phrase has been sent to your phone.";
        } else {
            $response = "CON Welcome to the Language Assistance Tool. Select a language:\n";
            foreach ($languages as $key => $value) {
                $response .= "$key. $value\n";
            }
        }
    }
}

header('Content-type: text/plain');
echo $response;

?>
