
<html>
<head>
    <title>Security Headers Analyzer</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            padding: 50px;
        }

        h1 {
            color: #333;
        }

        form {
            background-color: #fff;
            padding: 20px;
            border-radius: 5px;
            box-shadow: 0px 0px 10px 0px rgba(0,0,0,0.1);
        }

        label {
            font-weight: bold;
            display: block;
            margin-bottom: 5px;
        }

        textarea {
            width: 100%;
            padding: 12px 20px;
            margin: 8px 0;
            display: inline-block;
            border: 1px solid #ccc;
            border-radius: 4px;
            box-sizing: border-box;
            resize: vertical;
        }

        input[type=submit] {
            background-color: #4CAF50;
            color: white;
            padding: 14px 20px;
            margin: 8px 0;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }

        input[type=submit]:hover {
            background-color: #45a049;
        }
    </style>
</head>
<body>
    <h1>Security Headers Analyzer</h1>
    <form method="post">
        <label for="url">Enter Website URL:</label><br>
        <input type="text" id="url" name="url" placeholder="https://example.com" required><br><br>
        <button type="submit" name="submit">Analyze Headers</button>
    </form>

    <?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $url = $_POST["url"];
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HEADER, true);
    $response = curl_exec($ch);

    if ($response === false) {
        echo "<p>Error fetching headers.</p>";
    } else {
        list($headers, $body) = explode("\r\n\r\n", $response, 2);
        $header_lines = explode("\r\n", $headers);

        $csp_headers = [];
        $hsts_headers = [];
        $cors_headers = [];
        $xcto_headers = [];
        $xfo_headers = [];
        $rp_headers = [];
        $fp_headers = [];
        $xss_headers = [];

        foreach ($header_lines as $header_line) {
            if (stripos($header_line, 'Content-Security-Policy:') !== false) {
                $csp_headers[] = $header_line;
            } elseif (stripos($header_line, 'Strict-Transport-Security:') !== false) {
                $hsts_headers[] = $header_line;
            } elseif (stripos($header_line, 'Access-Control-Allow-Origin:') !== false) {
                $cors_headers[] = $header_line;
            } elseif (stripos($header_line, 'X-Content-Type-Options:') !== false) {
                $xcto_headers[] = $header_line;
            } elseif (stripos($header_line, 'X-Frame-Options:') !== false) {
                $xfo_headers[] = $header_line;
            } elseif (stripos($header_line, 'Referrer-Policy:') !== false) {
                $rp_headers[] = $header_line;
            } elseif (stripos($header_line, 'Feature-Policy:') !== false) {
                $fp_headers[] = $header_line;
            } elseif (stripos($header_line, 'X-XSS-Protection:') !== false) {
                $xss_headers[] = $header_line;
            }
        }
        echo "<h2>Security Headers:</h2>";

        echo "<p><strong>Content Security Policy (CSP):</strong><br>";
        foreach ($csp_headers as $csp_header) {
            echo htmlentities($csp_header) . "<br>";
        }
        echo "</p>";

        echo "<p><strong>HTTP Strict Transport Security (HSTS):</strong><br>";
        foreach ($hsts_headers as $hsts_header) {
            echo htmlentities($hsts_header) . "<br>";
        }
        echo "</p>";

        echo "<p><strong>Cross-Origin Resource Sharing (CORS):</strong><br>";
        foreach ($cors_headers as $cors_header) {
            echo htmlentities($cors_header) . "<br>";
        }
        echo "</p>";

        echo "<p><strong>X-Content-Type-Options:</strong><br>";
        foreach ($xcto_headers as $xcto_header) {
            echo htmlentities($xcto_header) . "<br>";
        }
        echo "</p>";

        echo "<p><strong>X-Frame-Options:</strong><br>";
        foreach ($xfo_headers as $xfo_header) {
            echo htmlentities($xfo_header) . "<br>";
        }
        echo "</p>";

        echo "<p><strong>Referrer-Policy:</strong><br>";
        foreach ($rp_headers as $rp_header) {
            echo htmlentities($rp_header) . "<br>";
        }
        echo "</p>";

        echo "<p><strong>Feature-Policy:</strong><br>";
        foreach ($fp_headers as $fp_header) {
            echo htmlentities($fp_header) . "<br>";
        }
        echo "</p>";

        echo "<p><strong>X-XSS-Protection:</strong><br>";
        foreach ($xss_headers as $xss_header) {
            echo htmlentities($xss_header) . "<br>";
        }
        echo "</p>";

        $servername = "localhost";
        $username = "root";
        $password = "";
        $dbname = "security";

        $conn = new mysqli($servername, $username, $password, $dbname);

        if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
        }

        $sql = "INSERT INTO headers (url, csp, hsts, cors, xcto, xfo, rp, fp, xss)
                VALUES ('$url', '" . implode(', ', $csp_headers) . "', '" . implode(', ', $hsts_headers) . "', '" . implode(', ', $cors_headers) . "', '" . implode(', ', $xcto_headers) . "', '" . implode(', ', $xfo_headers) . "', '" . implode(', ', $rp_headers) . "', '" . implode(', ', $fp_headers) . "', '" . implode(', ', $xss_headers) . "')";

        if ($conn->query($sql) === TRUE) {
            echo "<p>Security headers saved to database successfully.</p>";
        } else {
            echo "Error: " . $sql . "<br>" . $conn->error;
        }
        $conn->close();
    }
}
?>
