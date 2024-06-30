<?php
$certFile = 'path/to/server.crt';
$keyFile = 'path/to/server.key';

$context = stream_context_create([
    'ssl' => [
        'local_cert' => $certFile,
        'local_pk' => $keyFile,
        'allow_self_signed' => true,
        'verify_peer' => false
    ]
]);

$server = stream_socket_server('tcp://0.0.0.0:8000', $errno, $errstr, STREAM_SERVER_BIND | STREAM_SERVER_LISTEN, $context);

if (!$server) {
    die("Error to create a server : $errstr ($errno)\n");
}

echo "SSL started on 8080...\n";

while ($client = @stream_socket_accept($server)) {
    stream_socket_enable_crypto($client, true, STREAM_CRYPTO_METHOD_SSLv23_SERVER);
  
    $request = fread($client, 1024);

    echo "Requête reçue : $request\n";

    $response = "HTTP/1.1 200 OK\r\n";
    $response .= "Content-Type: text/plain\r\n";
    $response .= "Content-Length: 12\r\n";
    $response .= "\r\n";
    $response .= "SSL Caty Server!";
    fwrite($client, $response);

    fclose($client);
}

fclose($server);
?>

