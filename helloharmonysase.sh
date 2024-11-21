#!/bin/bash

PORT=8080
LOGO_URL="https://www.checkpoint.com/wp-content/themes/corporate/images/logos/check-point-logo-2019.svg"

HTML_RESPONSE="HTTP/1.1 200 OK
Content-Type: text/html

<!DOCTYPE html>
<html lang=\"en\">
<head>
    <meta charset=\"UTF-8\">
    <meta name=\"viewport\" content=\"width=device-width, initial-scale=1.0\">
    <title>Harmony SASE</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #3c1053; /* Dark pink Check Point color */
            color: #f2f2f2;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }
        .container {
            text-align: center;
            margin-bottom: auto;
        }
        footer {
            position: absolute;
            bottom: 10px;
            font-size: 0.9em;
            color: #e0e0e0;
        }
        h1 {
            font-size: 3em;
            color: #ff4081; /* Bright pink for headings */
        }
        p {
            font-size: 1.2em;
            color: #e0e0e0;
        }
        img {
            max-width: 150px;
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
    <div class=\"container\">
        <img src=\"$LOGO_URL\" alt=\"Check Point Logo\">
        <h1>Hello World!</h1>
        <p>Harmony SASE</p>
    </div>
    <footer>
        v0.1 AM test
    </footer>
</body>
</html>"

echo "Server running on port $PORT. Press Ctrl+C to stop."
while true; do
    echo -e "$HTML_RESPONSE" | nc -lN $PORT
done
