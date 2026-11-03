<?php
// ========== INDEX - PAINEL CAKTO CHECKER @buzeira171 ==========
error_reporting(0);
date_default_timezone_set('America/Sao_Paulo');

// ========== PROCESSA REQUISIÇÕES AJAX ==========
if (isset($_POST['action']) && $_POST['action'] === 'check') {
    while (ob_get_level()) ob_end_clean();
    ob_start();
    
    header('Content-Type: application/json; charset=utf-8');
    
    try {
        $lista = trim($_POST['lista'] ?? '');
        
        if (empty($lista)) {
            ob_end_clean();
            echo json_encode(['ok' => false, 'msg' => 'Nenhum cartão informado']);
            exit;
        }
        
        $cartoes = preg_split('/[\r\n]+/', $lista, -1, PREG_SPLIT_NO_EMPTY);
        $novosResultados = [];
        
        foreach ($cartoes as $linha) {
            $linha = trim($linha);
            if (empty($linha)) continue;
            
            $_GET['lista'] = $linha;
            
            ob_start();
            include __DIR__ . '/cakto.php';
            $output = trim(ob_get_clean());
            
            $novosResultados[] = [
                'linha' => $output,
                'live'  => (strpos($output, '✅ LIVE') !== false)
            ];
        }
        
        ob_end_clean();
        echo json_encode([
            'ok' => true,
            'total' => count($novosResultados),
            'resultados' => $novosResultados
        ], JSON_UNESCAPED_UNICODE);
        exit;
        
    } catch (Throwable $e) {
        ob_end_clean();
        echo json_encode([
            'ok' => false,
            'msg' => 'Erro interno: ' . $e->getMessage()
        ]);
        exit;
    }
}

// ========== STATUS DO SISTEMA ==========
$slugControlFile = __DIR__ . '/slug_control.txt';
$slugAtual = file_exists($slugControlFile) ? (int)file_get_contents($slugControlFile) : 0;
$debugFiles = glob(__DIR__ . '/debug_cakto_*.txt');
$debugSize = 0;
foreach ($debugFiles as $f) $debugSize += filesize($f);
$htmlFiles = glob(__DIR__ . '/HTML DEBUG/*.html');

// Detecta se cakto.php existe
$caktoExiste = file_exists(__DIR__ . '/cakto.php');
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Cakto Checker API @buzeira171</title>
<style>
:root{
  --bg:#0a0e17; --bg-card:#111827; --bg-input:#1a2234;
  --border:#1f2a3f; --text:#e5e7eb; --text-dim:#8b95a8;
  --accent:#3b82f6; --accent-hover:#2563eb;
  --green:#22c55e; --red:#ef4444; --yellow:#eab308; --orange:#f97316;
  --radius:12px; --mono:'Consolas','Monaco',monospace;
}
*{margin:0;padding:0;box-sizing:border-box}
body{font-family:-apple-system,BlinkMacSystemFont,'Segoe UI',sans-serif;background:var(--bg);color:var(--text);min-height:100vh;line-height:1.6}
.header{background:linear-gradient(135deg,#1e293b 0%,#0f172a 100%);border-bottom:1px solid var(--border);padding:24px 32px;display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:16px}
.header h1{font-size:22px;font-weight:700;display:flex;align-items:center;gap:12px}
.badge{font-size:11px;padding:3px 10px;border-radius:999px;background:rgba(59,130,246,.15);color:var(--accent);border:1px solid rgba(59,130,246,.3);font-weight:600;letter-spacing:.5px}
.status-pill{display:inline-flex;align-items:center;gap:6px;font-size:12px;padding:6px 12px;border-radius:999px;background:rgba(34,197,94,.1);color:var(--green);border:1px solid rgba(34,197,94,.25)}
.status-pill.error{background:rgba(239,68,68,.1);color:var(--red);border-color:rgba(239,68,68,.25)}
.status-pill::before{content:'';width:8px;height:8px;border-radius:50%;background:currentColor;box-shadow:0 0 8px currentColor;animation:pulse 2s infinite}
@keyframes pulse{0%,100%{opacity:1}50%{opacity:.4}}
.container{max-width:1100px;margin:0 auto;padding:32px 20px}
.stats{display:grid;grid-template-columns:repeat(auto-fit,minmax(180px,1fr));gap:16px;margin-bottom:24px}
.stat-card{background:var(--bg-card);border:1px solid var(--border);border-radius:var(--radius);padding:20px;transition:border-color .2s}
.stat-card:hover{border-color:var(--accent)}
.stat-label{font-size:12px;color:var(--text-dim);text-transform:uppercase;letter-spacing:1px;font-weight:600;margin-bottom:8px}
.stat-value{font-size:26px;font-weight:700;font-family:var(--mono)}
.stat-value.blue{color:var(--accent)}
.stat-value.green{color:var(--green)}
.stat-value.yellow{color:var(--yellow)}
.stat-value.red{color:var(--red)}
.panel{background:var(--bg-card);border:1px solid var(--border);border-radius:var(--radius);padding:24px}
.panel-title{font-size:15px;font-weight:700;margin-bottom:16px;display:flex;align-items:center;gap:10px;color:var(--text)}
.panel-title .dot{width:8px;height:8px;border-radius:50%;background:var(--accent)}
textarea{width:100%;min-height:140px;background:var(--bg-input);border:1px solid var(--border);border-radius:10px;padding:14px;color:var(--text);font-family:var(--mono);font-size:13px;resize:vertical;outline:none;transition:border-color .2s;line-height:1.7}
textarea:focus{border-color:var(--accent)}
textarea::placeholder{color:#4b5563}
.actions{display:flex;gap:12px;margin-top:16px;flex-wrap:wrap}
button{font-family:inherit;font-size:14px;font-weight:600;padding:12px 24px;border-radius:10px;border:none;cursor:pointer;transition:all .2s;display:inline-flex;align-items:center;gap:8px}
.btn-primary{background:var(--accent);color:#fff}
.btn-primary:hover{background:var(--accent-hover)}
.btn-primary:disabled{background:#374151;color:#6b7280;cursor:not-allowed}
.btn-ghost{background:transparent;color:var(--text-dim);border:1px solid var(--border)}
.btn-ghost:hover{border-color:var(--text-dim);color:var(--text)}
.btn-ghost:disabled{cursor:not-allowed;opacity:.5}
.btn-danger{background:rgba(239,68,68,.1);color:var(--red);border:1px solid rgba(239,68,68,.3)}
.btn-danger:hover{background:rgba(239,68,68,.2)}
.spinner{width:16px;height:16px;border:2px solid rgba(255,255,255,.3);border-top-color:#fff;border-radius:50%;animation:spin .7s linear infinite;display:none}
@keyframes spin{to{transform:rotate(360deg)}}
.result-panel{margin-top:20px;display:none}
.result-panel.show{display:block}
.result-header{display:flex;justify-content:space-between;align-items:center;margin-bottom:14px;flex-wrap:wrap;gap:10px}
.result-count{font-size:12px;color:var(--text-dim)}
.result-count strong{color:var(--green)}
.result-count .red{color:var(--red)}
.output{background:#0d1117;border:1px solid var(--border);border-radius:10px;padding:16px;font-family:var(--mono);font-size:13px;white-space:pre-wrap;word-break:break-all;max-height:500px;overflow-y:auto;line-height:1.9}
.output .line{display:block;padding:6px 10px;margin:2px 0;border-radius:6px}
.output .line.live{color:var(--green);background:rgba(34,197,94,.08);border-left:3px solid var(--green)}
.output .line.die{color:var(--red);background:rgba(239,68,68,.08);border-left:3px solid var(--red)}
.output .line.info{color:var(--accent);background:rgba(59,130,246,.08);border-left:3px solid var(--accent)}
.output .line.warn{color:var(--yellow);background:rgba(234,179,8,.08);border-left:3px solid var(--yellow)}
.output .line.err{color:var(--red);background:rgba(239,68,68,.15);border-left:3px solid var(--red);font-weight:600}
.output .empty{color:var(--text-dim);text-align:center;padding:20px}
.output::-webkit-scrollbar{width:8px}
.output::-webkit-scrollbar-track{background:transparent}
.output::-webkit-scrollbar-thumb{background:var(--border);border-radius:4px}
.info-list{display:flex;flex-direction:column;gap:10px}
.info-row{display:flex;justify-content:space-between;padding:10px 0;border-bottom:1px solid var(--border);font-size:13px}
.info-row:last-child{border-bottom:none}
.info-row .key{color:var(--text-dim)}
.info-row .val{font-family:var(--mono);color:var(--text)}
.info-row .val.mono-small{font-size:11px;color:var(--text-dim)}
.info-row .val.ok{color:var(--green)}
.info-row .val.bad{color:var(--red)}
.footer{text-align:center;padding:24px;font-size:12px;color:var(--text-dim);border-top:1px solid var(--border);margin-top:32px}
.footer a{color:var(--accent);text-decoration:none}
.console{
  background:#000;border:1px solid var(--border);border-radius:10px;
  padding:12px 16px;font-family:var(--mono);font-size:12px;
  max-height:200px;overflow-y:auto;line-height:1.7;
  margin-top:16px;
}
.console .c-line{padding:2px 0}
.console .c-line .ts{color:#4b5563;margin-right:8px}
.console .c-line.ok{color:var(--green)}
.console .c-line.err{color:var(--red)}
.console .c-line.warn{color:var(--yellow)}
.console .c-line.info{color:var(--accent)}
.console::-webkit-scrollbar{width:6px}
.console::-webkit-scrollbar-thumb{background:var(--border);border-radius:3px}
@media(max-width:640px){
  .header{padding:16px 20px}
  .header h1{font-size:18px}
  .container{padding:20px 16px}
  .panel{padding:18px}
  button{width:100%;justify-content:center}
}
</style>
</head>
<body>

<header class="header">
  <h1>🛡️ Cakto Checker <span class="badge">API v2.2</span></h1>
  <span class="status-pill <?= $caktoExiste ? '' : 'error' ?>">
    <?= $caktoExiste ? 'Online' : 'cakto.php ausente' ?>
  </span>
</header>

<div class="container">

  <div class="stats">
    <div class="stat-card">
      <div class="stat-label">Slug Atual</div>
      <div class="stat-value blue">#<?= $slugAtual ?></div>
    </div>
    <div class="stat-card">
      <div class="stat-label">Aprovados (sessão)</div>
      <div class="stat-value green" id="statLives">0</div>
    </div>
    <div class="stat-card">
      <div class="stat-label">Reprovados (sessão)</div>
      <div class="stat-value red" id="statDies">0</div>
    </div>
    <div class="stat-card">
      <div class="stat-label">Logs Debug</div>
      <div class="stat-value yellow"><?= count($debugFiles) ?></div>
    </div>
  </div>

  <div class="panel">
    <div class="panel-title"><span class="dot"></span> Testar Cartões</div>
    <textarea id="lista" placeholder="Cole os cartões (um por linha)&#10;Formato: numero|mes|ano|cvv&#10;Exemplo: 5122672305123117|04|2028|458"></textarea>
    <div class="actions">
      <button class="btn-primary" id="btnCheck" onclick="checkCards()">
        <span class="spinner" id="spinner"></span>
        <span id="btnText">🚀 Iniciar Validação</span>
      </button>
      <button class="btn-ghost" onclick="copiar()">📋 Copiar Aprovados</button>
      <button class="btn-danger" onclick="limparTudo()">🗑️ Limpar Tudo</button>
    </div>
  </div>

  <!-- Painel único: mostra LIVE + DIE da última rodada + histórico de LIVE -->
  <div class="result-panel panel" id="resultPanel">
    <div class="result-header">
      <div class="panel-title"><span class="dot"></span> Resultados</div>
      <span class="result-count" id="resultCount"></span>
    </div>
    <div class="output" id="output">
      <div class="empty">Nenhum resultado ainda. Cole cartões e clique em Iniciar Validação.</div>
    </div>
  </div>

  <!-- Console visual: mostra TUDO que acontece -->
  <div class="panel" style="margin-top:20px;">
    <div class="panel-title"><span class="dot"></span> Console de Eventos</div>
    <div class="console" id="console">
      <div class="c-line info"><span class="ts">--:--:--</span>Painel carregado</div>
    </div>
  </div>

  <div class="panel" style="margin-top:20px;">
    <div class="panel-title"><span class="dot"></span> Informações do Sistema</div>
    <div class="info-list">
      <div class="info-row"><span class="key">Versão</span><span class="val">v2.2 (Slug Rotativo)</span></div>
      <div class="info-row"><span class="key">Endpoint API</span><span class="val mono-small">/cakto.php?lista=CC|MM|YYYY|CVV</span></div>
      <div class="info-row"><span class="key">Métodos Suportados</span><span class="val">GET · POST · JSON Body</span></div>
      <div class="info-row"><span class="key">Tamanho Logs</span><span class="val"><?= $debugSize > 0 ? round($debugSize / 1024, 1) . ' KB' : '0 KB' ?></span></div>
      <div class="info-row">
        <span class="key">Arquivo cakto.php</span>
        <span class="val <?= $caktoExiste ? 'ok' : 'bad' ?>">
          <?= $caktoExiste ? '✅ Encontrado' : '❌ NÃO EXISTE' ?>
        </span>
      </div>
      <div class="info-row">
        <span class="key">Diretório HTML DEBUG</span>
        <span class="val mono-small"><?= is_dir(__DIR__ . '/HTML DEBUG') ? '✅ Criado' : '⚠️ Não existe (será criado)' ?></span>
      </div>
      <div class="info-row">
        <span class="key">PHP Versão</span>
        <span class="val"><?= phpversion() ?></span>
      </div>
      <div class="info-row">
        <span class="key">cURL disponível</span>
        <span class="val <?= function_exists('curl_init') ? 'ok' : 'bad' ?>">
          <?= function_exists('curl_init') ? '✅ Sim' : '❌ NÃO (essencial!)' ?>
        </span>
      </div>
    </div>
  </div>

</div>

<footer class="footer">
  Cakto Checker API &copy; <?= date('Y') ?> · Desenvolvido por <a href="#">@buzeira171</a>
</footer>

<script>
// ========== ESTADO ==========
let livesAcumulados = [];
let ultimaRodada = []; // todos os resultados da última rodada
let totalDies = 0;

// ========== CONSOLE VISUAL ==========
function log(msg, tipo='info'){
  const consoleEl = document.getElementById('console');
  const now = new Date();
  const ts = String(now.getHours()).padStart(2,'0') + ':' +
             String(now.getMinutes()).padStart(2,'0') + ':' +
             String(now.getSeconds()).padStart(2,'0');
  const div = document.createElement('div');
  div.className = 'c-line ' + tipo;
  div.innerHTML = `<span class="ts">${ts}</span>${msg}`;
  consoleEl.appendChild(div);
  consoleEl.scrollTop = consoleEl.scrollHeight;
}

// ========== LOCALSTORAGE ==========
function carregarLives(){
  try{
    const raw = localStorage.getItem('cakto_lives');
    if(!raw){
      livesAcumulados = [];
    } else {
      const parsed = JSON.parse(raw);
      if(Array.isArray(parsed)){
        livesAcumulados = parsed.filter(x => typeof x === 'string' && x.length > 0);
      } else {
        livesAcumulados = [];
        localStorage.removeItem('cakto_lives');
      }
    }
    log(`Histórico carregado: ${livesAcumulados.length} aprovado(s)`, 'info');
  }catch(e){
    log('localStorage corrompido, resetando: ' + e.message, 'warn');
    livesAcumulados = [];
    try{ localStorage.removeItem('cakto_lives'); }catch(_){}
  }
  renderizarResultados();
}

function salvarLives(){
  try{
    localStorage.setItem('cakto_lives', JSON.stringify(livesAcumulados));
  }catch(e){
    log('Erro ao salvar no localStorage: ' + e.message, 'warn');
  }
}

// ========== RENDERIZAÇÃO ==========
function renderizarResultados(){
  const output = document.getElementById('output');
  const panel = document.getElementById('resultPanel');
  const count = document.getElementById('resultCount');
  
  if(livesAcumulados.length === 0 && ultimaRodada.length === 0){
    panel.classList.remove('show');
    output.innerHTML = '<div class="empty">Nenhum resultado ainda. Cole cartões e clique em Iniciar Validação.</div>';
    count.textContent = '';
  } else {
    panel.classList.add('show');
    output.innerHTML = '';
    
    // 1) Aprovados acumulados (histórico)
    if(livesAcumulados.length > 0){
      const titulo = document.createElement('div');
      titulo.className = 'line info';
      titulo.textContent = '🟢 APROVADOS ACUMULADOS (' + livesAcumulados.length + ')';
      titulo.style.fontWeight = '700';
      titulo.style.marginBottom = '6px';
      output.appendChild(titulo);
      
      livesAcumulados.forEach(line => {
        const div = document.createElement('div');
        div.className = 'line live';
        div.textContent = line;
        output.appendChild(div);
      });
    }
    
    // 2) Resultados da última rodada (inclui DIE)
    if(ultimaRodada.length > 0){
      const titulo = document.createElement('div');
      titulo.className = 'line info';
      titulo.textContent = '📊 ÚLTIMA RODADA (' + ultimaRodada.length + ' cartão(ões))';
      titulo.style.fontWeight = '700';
      titulo.style.marginTop = '16px';
      titulo.style.marginBottom = '6px';
      output.appendChild(titulo);
      
      ultimaRodada.forEach(r => {
        const div = document.createElement('div');
        div.className = 'line ' + (r.live ? 'live' : 'die');
        div.textContent = r.linha;
        output.appendChild(div);
      });
    }
    
    // Contador
    const lives = livesAcumulados.length;
    const dies = ultimaRodada.filter(r => !r.live).length;
    count.innerHTML = 
      `<strong>${lives}</strong> aprovado(s) acumulado(s) · ` +
      `<span class="red">${dies}</span> reprovado(s) na última rodada`;
  }
  
  document.getElementById('statLives').textContent = livesAcumulados.length;
  document.getElementById('statDies').textContent = ultimaRodada.filter(r => !r.live).length;
}

// ========== CHECAR CARTÕES ==========
async function checkCards(){
  const lista = document.getElementById('lista').value.trim();
  
  if(!lista){
    log('⚠️ Nenhum cartão informado. Cole algo na textarea.', 'warn');
    return;
  }
  
  const btn = document.getElementById('btnCheck');
  const spinner = document.getElementById('spinner');
  const btnText = document.getElementById('btnText');
  
  btn.disabled = true;
  spinner.style.display = 'inline-block';
  btnText.textContent = 'Validando...';
  
  log('🚀 Iniciando validação de cartão(ões)...', 'info');
  
  // Limpa rodada anterior
  ultimaRodada = [];
  
  const output = document.getElementById('output');
  output.innerHTML = '<div class="empty">⏳ Processando... aguarde (pode demorar)</div>';
  document.getElementById('resultPanel').classList.add('show');
  
  try{
    const form = new FormData();
    form.append('action', 'check');
    form.append('lista', lista);
    
    log('📤 Enviando requisição para o servidor...', 'info');
    
    const resp = await fetch(window.location.href, { 
      method: 'POST', 
      body: form 
    });
    
    log(`📥 Resposta recebida (HTTP ${resp.status})`, 'info');
    
    const text = await resp.text();
    
    // 🔍 Verifica se é JSON válido
    let data;
    try{
      data = JSON.parse(text);
    }catch(parseErr){
      log('❌ ERRO: Servidor retornou resposta NÃO-JSON', 'err');
      log('Prévia da resposta: ' + text.substring(0, 200), 'err');
      
      // Mostra o erro NO PAINEL, não em alert
      output.innerHTML = '';
      const errDiv = document.createElement('div');
      errDiv.className = 'line err';
      errDiv.textContent = '❌ SERVIDOR RETORNOU RESPOSTA INVÁLIDA (não é JSON)';
      output.appendChild(errDiv);
      
      const previewDiv = document.createElement('div');
      previewDiv.className = 'line warn';
      previewDiv.textContent = 'Prévia: ' + text.substring(0, 300);
      output.appendChild(previewDiv);
      
      const hintDiv = document.createElement('div');
      hintDiv.className = 'line info';
      hintDiv.textContent = '💡 Dica: verifique se cakto.php existe e se o PHP está rodando corretamente no servidor.';
      output.appendChild(hintDiv);
      
      renderizarResultados();
      return;
    }
    
    if(!data.ok){
      log('❌ Servidor retornou erro: ' + (data.msg || 'desconhecido'), 'err');
      
      output.innerHTML = '';
      const errDiv = document.createElement('div');
      errDiv.className = 'line err';
      errDiv.textContent = '❌ ERRO DO SERVIDOR: ' + (data.msg || 'desconhecido');
      output.appendChild(errDiv);
      renderizarResultados();
      return;
    }
    
    log(`✅ ${data.total} cartão(ões) processado(s)`, 'ok');
    
    // Processa resultados
    let novosLives = 0;
    let novosDies = 0;
    
    (data.resultados || []).forEach(r => {
      if(!r || typeof r.linha !== 'string') return;
      
      ultimaRodada.push(r);
      
      if(r.live){
        if(!livesAcumulados.includes(r.linha)){
          livesAcumulados.push(r.linha);
          novosLives++;
          log('✅ LIVE: ' + r.linha.substring(0, 60) + '...', 'ok');
        } else {
          log('🔁 LIVE já estava no histórico (ignorado)', 'info');
        }
      } else {
        novosDies++;
        log('❌ DIE: ' + r.linha.substring(0, 60) + '...', 'err');
      }
    });
    
    salvarLives();
    renderizarResultados();
    
    log(`📊 Resumo: ${novosLives} novo(s) LIVE · ${novosDies} DIE`, 'info');
    
    // Limpa textarea
    document.getElementById('lista').value = '';
    
  }catch(e){
    log('❌ ERRO de conexão: ' + e.message, 'err');
    
    output.innerHTML = '';
    const errDiv = document.createElement('div');
    errDiv.className = 'line err';
    errDiv.textContent = '❌ ERRO DE CONEXÃO: ' + e.message;
    output.appendChild(errDiv);
    
    const hintDiv = document.createElement('div');
    hintDiv.className = 'line info';
    hintDiv.textContent = '💡 Verifique sua internet ou se o servidor está online.';
    output.appendChild(hintDiv);
    
    renderizarResultados();
    
  }finally{
    btn.disabled = false;
    spinner.style.display = 'none';
    btnText.textContent = '🚀 Iniciar Validação';
  }
}

// ========== COPIAR ==========
async function copiar(){
  if(livesAcumulados.length === 0){
    log('⚠️ Nenhum aprovado para copiar', 'warn');
    return;
  }
  const texto = livesAcumulados.join('\n');
  try{
    await navigator.clipboard.writeText(texto);
    log(`📋 ${livesAcumulados.length} aprovado(s) copiado(s)`, 'ok');
    const btn = event.target;
    const orig = btn.innerHTML;
    btn.innerHTML = '✅ Copiado!';
    setTimeout(() => btn.innerHTML = orig, 1500);
  }catch(err){
    log('❌ Erro ao copiar: ' + err.message, 'err');
  }
}

// ========== LIMPAR ==========
function limparTudo(){
  if(livesAcumulados.length === 0 && ultimaRodada.length === 0 && !document.getElementById('lista').value){
    log('⚠️ Nada para limpar', 'warn');
    return;
  }
  
  // Confirmação via double-check visual (sem alert)
  const btn = event.target;
  if(btn.dataset.confirm !== '1'){
    btn.dataset.confirm = '1';
    const orig = btn.innerHTML;
    btn.innerHTML = '⚠️ Clique novamente para confirmar';
    setTimeout(() => {
      btn.innerHTML = orig;
      btn.dataset.confirm = '0';
    }, 3000);
    return;
  }
  
  btn.dataset.confirm = '0';
  const orig = btn.innerHTML;
  btn.innerHTML = '🗑️ Limpar Tudo';
  
  livesAcumulados = [];
  ultimaRodada = [];
  salvarLives();
  renderizarResultados();
  document.getElementById('lista').value = '';
  log('🗑️ Histórico e resultados limpos', 'warn');
}

// ========== INICIALIZA ==========
carregarLives();
</script>

</body>
</html>
