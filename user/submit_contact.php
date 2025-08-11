<?php
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $name = htmlspecialchars($_POST['name']);
    $phone = htmlspecialchars($_POST['phone']);
    $email = htmlspecialchars($_POST['email']);
    $message_text = htmlspecialchars($_POST['message']);

    // Compose the WhatsApp message
    $message = urlencode("Name: $name\nPhone: $phone\nEmail: $email\nMessage: $message_text");

    // Replace with your actual WhatsApp number
    $whatsapp_number = "+919133171380";

    // Clear form data and redirect to WhatsApp
    echo "<!DOCTYPE html>
    <html>
    <head>
        <meta charset='UTF-8'>
        <title>Redirecting...</title>
        <style>
            body { 
                font-family: 'Outfit', sans-serif; 
                background-color: #000; 
                color: #B0B0B0; 
                display: flex; 
                justify-content: center; 
                align-items: center; 
                height: 100vh; 
                margin: 0;
            }
            .redirect-container {
                text-align: center;
                padding: 20px;
                border: 1px solid #3EB489;
                border-radius: 10px;
                background-color: #1a1a1a;
            }
        </style>
    </head>
    <body>
        <div class='redirect-container'>
            <p>Opening WhatsApp...</p>
            <p>Please wait while we redirect you.</p>
        </div>
        <script>
            // Clear the form data from browser history
            if (window.history.replaceState) {
                window.history.replaceState(null, null, window.location.href);
            }
            
            // Clear any stored form data
            localStorage.removeItem('contactFormData');
            sessionStorage.removeItem('contactFormData');
            
            // Redirect to WhatsApp
            window.location.href = 'https://wa.me/$whatsapp_number?text=$message';
        </script>
    </body>
    </html>";
    exit;
} else {
    echo "Invalid request method.";
}
?>
