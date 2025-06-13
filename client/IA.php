<?php
define('GEMINI_API_KEY', 'AIzaSyAYxzeqGBdcyquD7OFDTGfEdvZ_3VIR7d4'); // Remplace par ta clé API réelle

$ai_response = '';
$user_question = '';

function is_related_to_real_estate($question) {
    $keywords = [
        'immobilier', 'vente', 'achat', 'location', 'terrain', 'bien', 'propriété',
        'maison', 'appartement', 'agent', 'agence', 'notaire', 'bail', 'loyer',
        'propriétaire', 'investissement immobilier', 'hypothèque', 'logement'
    ];

    $lower_question = mb_strtolower($question);
    foreach ($keywords as $keyword) {
        if (strpos($lower_question, $keyword) !== false) {
            return true;
        }
    }
    return false;
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['user_question'])) {
    $user_question = htmlspecialchars(trim($_POST['user_question']));

    if (!empty($user_question)) {
        if (!is_related_to_real_estate($user_question)) {
            $ai_response = '<div class="ai-response-error">Cette expression ne correspond pas à mon domaine d\'application.</div>';
        } else {
            $api_endpoint = 'https://generativelanguage.googleapis.com/v1beta/models/gemini-1.5-flash:generateContent?key=' . GEMINI_API_KEY;

            $data = [
                'contents' => [
                    [
                        'parts' => [
                            ['text' => "Tu es un assistant expert en vente immobilière. Réponds uniquement aux questions liées à ce domaine. " . $user_question]
                        ]
                    ]
                ]
            ];

            $ch = curl_init($api_endpoint);
            curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);

            $response = curl_exec($ch);

            if (curl_errno($ch)) {
                $ai_response = '<div class="ai-response-error">Erreur cURL : ' . curl_error($ch) . '</div>';
            } else {
                $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
                if ($http_code == 200) {
                    $decoded_response = json_decode($response, true);
                    if (isset($decoded_response['candidates'][0]['content']['parts'][0]['text'])) {
                        $ai_response = '<div class="ai-response-success">' . nl2br(htmlspecialchars($decoded_response['candidates'][0]['content']['parts'][0]['text'])) . '</div>';
                    } else {
                        $ai_response = '<div class="ai-response-error">Réponse invalide de l\'IA.</div>';
                    }
                } else {
                    $ai_response = '<div class="ai-response-error">Erreur API: Code HTTP ' . $http_code . '</div>';
                }
            }
            curl_close($ch);
        }
    } else {
        $ai_response = '<div class="ai-response-info">Veuillez poser une question.</div>';
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Assistant Immobilier IA</title>
    <style>
        body {
            font-family: 'Segoe UI', sans-serif;
            background: #f7f7fc;
            margin: 0;
            padding: 0;
        }

        header {
            background-color: #4a148c;
            color: white;
            padding: 20px 0;
            text-align: center;
        }

        nav a {
            color: #fff;
            margin: 0 15px;
            text-decoration: none;
            font-weight: bold;
        }

        .ai-container {
            max-width: 900px;
            margin: 50px auto;
            padding: 40px;
            background-color: #ffffff;
            border-radius: 15px;
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1);
        }

        .ai-container h2 {
            text-align: center;
            color: #6a1b9a;
            margin-bottom: 30px;
            font-size: 28px;
        }

        .ai-form textarea {
            width: 100%;
            padding: 20px;
            margin-bottom: 25px;
            border: 1px solid #ccc;
            border-radius: 8px;
            font-size: 16px;
            resize: vertical;
            min-height: 120px;
        }

        .ai-form button {
            background-color: #7b1fa2;
            color: white;
            padding: 15px 30px;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-size: 18px;
            width: 100%;
            transition: 0.3s ease;
        }

        .ai-form button:hover {
            background-color: #6a1b9a;
        }

        .ai-response-container {
            margin-top: 30px;
        }

        .ai-response-success,
        .ai-response-error,
        .ai-response-info {
            padding: 20px;
            border-radius: 8px;
            font-size: 16px;
            margin-top: 20px;
        }

        .ai-response-success {
            background-color: #e8f5e9;
            color: #2e7d32;
            border: 1px solid #c8e6c9;
        }

        .ai-response-error {
            background-color: #fdecea;
            color: #c62828;
            border: 1px solid #f5c6cb;
        }

        .ai-response-info {
            background-color: #e3f2fd;
            color: #1565c0;
            border: 1px solid #bbdefb;
        }

        footer {
            text-align: center;
            padding: 20px;
            background-color: #4a148c;
            color: white;
            position: fixed;
            bottom: 0;
            width: 100%;
        }

    </style>
</head>
<body>
    <header>
        <h1>Assistant IA Immobilier</h1>
        <nav> 

            <a href="index.php"> Regardez nos biens à vendre</a>
    
        </nav>
    </header>

    <main>
        <div class="ai-container">
            <h2>Posez votre question immobilière à l'IA</h2>
            <form action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" method="POST" class="ai-form">
                <label for="user_question">Exemple : Comment acheter une local rapidement ?</label>
                <textarea id="user_question" name="user_question" required><?php echo htmlspecialchars($user_question); ?></textarea>
                <button type="submit">Demander à l'IA</button>
            </form>

            <?php if (!empty($ai_response)): ?>
                <div class="ai-response-container">
                    <h3>Réponse :</h3>
                    <?php echo $ai_response; ?>
                </div>
            <?php endif; ?>
        </div>
    </main>

    <footer>
        <p>&copy; <?php echo date("Y"); ?> Assistant IA Immobilier</p>
    </footer>
</body>
</html>
