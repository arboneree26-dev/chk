<?php
// ========== INDEX - PAINEL CAKTO CHECKER @buzeira171 ==========
error_reporting(0);
date_default_timezone_set('America/Sao_Paulo');

// Processa requisições AJAX
if (isset($_POST['action']) && $_POST['action'] === 'check') {
    header('Content-Type: application/json; charset=utf-8');
    
    $lista = trim($_POST['lista'] ?? '');
    
    if (empty($lista)) {
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
    
    echo json_encode([
        'ok' => true,
        'total' => count($novosResultados),
        'resultados' => $novosResultados
    ], JSON_UNESCAPED_UNICODE);
    exit;
}

// Status do sistema
$slugControlFile = __DIR__ . '/slug_control.txt';
$slugAtual = file_exists($slugControlFile) ? (int)file_get_contents($slugControlFile) : 0;
$debugFiles = glob(__DIR__ . '/debug_cakto_*.txt');
$debugSize = 0;
foreach ($debugFiles as $f) $debugSize += filesize($f);
$htmlFiles = glob(__DIR__ . '/HTML DEBUG/*.html');
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
  --green:#22c55e; --red:#ef4444; --yellow:#eab308;
  --radius:12px; --mono:'Consolas','Monaco',monospace;
}
*{margin:0;padding:0;box-sizing:border-box}
body{font-family:-apple-system,BlinkMacSystemFont,'Segoe UI',sans-serif;background:var(--bg);color:var(--text);min-height:100vh;line-height:1.6}
.header{background:linear-gradient(135deg,#1e293b 0%,#0f172a 100%);border-bottom:1px solid var(--border);padding:24px 32px;display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:16px}
.header h1{font-size:22px;font-weight:700;display:flex;align-items:center;gap:12px}
.badge{font-size:11px;padding:3px 10px;border-radius:999px;background:rgba(59,130,246,.15);color:var(--accent);border:1px solid rgba(59,130,246,.3);font-weight:600;letter-spacing:.5px}
.status-pill{display:inline-flex;align-items:center;gap:6px;font-size:12px;padding:6px 12px;border-radius:999px;background:rgba(34,197,94,.1);color:var(--green);border:1px solid rgba(34,197,94,.25)}
.status-pill::before{content:'';width:8px;height:8px;border-radius:50%;background:var(--green);box-shadow:0 0 8px var(--green);animation:pulse 2s infinite}
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
.output{background:#0d1117;border:1px solid var(--border);border-radius:10px;padding:16px;font-family:var(--mono);font-size:13px;white-space:pre-wrap;word-break:break-all;max-height:500px;overflow-y:auto;line-height:1.9}
.output .line{display:block;padding:4px 0;border-bottom:1px dashed rgba(255,255,255,.05)}
.output .line:last-child{border-bottom:none}
.output .line.live{color:var(--green)}
.output .line.die{color:var(--red)}
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
.footer{text-align:center;padding:24px;font-size:12px;color:var(--text-dim);border-top:1px solid var(--border);margin-top:32px}
.footer a{color:var(--accent);text-decoration:none}
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
  <span class="status-pill">Online</span>
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
      <div class="stat-label">Logs Debug</div>
      <div class="stat-value yellow"><?= count($debugFiles) ?></div>
    </div>
    <div class="stat-card">
      <div class="stat-label">Capturas 3DS</div>
      <div class="stat-value green"><?= count($htmlFiles) ?></div>
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

  <div class="result-panel panel" id="resultPanel">
    <div class="result-header">
      <div class="panel-title"><span class="dot"></span> Aprovados (LIVE)</div>
      <span class="result-count" id="resultCount"></span>
    </div>
    <div class="output" id="output">
      <div class="empty">Nenhum cartão aprovado ainda</div>
    </div>
  </div>

  <div class="panel" style="margin-top:20px;">
    <div class="panel-title"><span class="dot"></span> Informações do Sistema</div>
    <div class="info-list">
      <div class="info-row"><span class="key">Versão</span><span class="val">v2.2 (Slug Rotativo)</span></div>
      <div class="info-row"><span class="key">Endpoint API</span><span class="val mono-small">/cakto.php?lista=CC|MM|YYYY|CVV</span></div>
      <div class="info-row"><span class="key">Métodos Suportados</span><span class="val">GET · POST · JSON Body</span></div>
      <div class="info-row"><span class="key">Tamanho Logs</span><span class="val"><?= $debugSize > 0 ? round($debugSize / 1024, 1) . ' KB' : '0 KB' ?></span></div>
      <div class="info-row"><span class="key">Diretório HTML DEBUG</span><span class="val mono-small"><?= is_dir(__DIR__ . '/HTML DEBUG') ? '✅ Criado' : '⚠️ Não existe' ?></span></div>
    </div>
  </div>

</div>

<footer class="footer">
  Cakto Checker API &copy; <?= date('Y') ?> · Desenvolvido por <a href="#">@buzeira171</a>
</footer>

<script>
// ===== Estado global: só acumula LIVE =====
let livesAcumulados = [];

// Carrega do localStorage (persiste mesmo se recarregar a página)
function carregarLives(){
  try{
    const raw = localStorage.getItem('cakto_lives');
    if(raw) livesAcumulados = JSON.parse(raw);
  }catch(e){ livesAcumulados = []; }
  renderizarLives();
}

function salvarLives(){
  try{
    localStorage.setItem('cakto_lives', JSON.stringify(livesAcumulados));
  }catch(e){}
}

function renderizarLives(){
  const output = document.getElementById('output');
  const panel = document.getElementById('resultPanel');
  const count = document.getElementById('resultCount');
  
  if(livesAcumulados.length === 0){
    panel.classList.remove('show');
    output.innerHTML = '<div class="empty">Nenhum cartão aprovado ainda</div>';
    count.textContent = '';
  } else {
    panel.classList.add('show');
    output.innerHTML = '';
    livesAcumulados.forEach(line => {
      const div = document.createElement('span');
      div.className = 'line live';
      div.textContent = line;
      output.appendChild(div);
    });
    count.innerHTML = `<strong>${livesAcumulados.length}</strong> aprovado(s) acumulado(s)`;
  }
  
  document.getElementById('statLives').textContent = livesAcumulados.length;
}

async function checkCards(){
  const lista = document.getElementById('lista').value.trim();
  if(!lista){
    alert('Cole pelo menos um cartão!');
    return;
  }
  
  const btn = document.getElementById('btnCheck');
  const spinner = document.getElementById('spinner');
  const btnText = document.getElementById('btnText');
  
  btn.disabled = true;
  spinner.style.display = 'inline-block';
  btnText.textContent = 'Validando...';
  
  // Mensagem temporária enquanto processa
  const output = document.getElementById('output');
  const panel = document.getElementById('resultPanel');
  panel.classList.add('show');
  output.innerHTML = '<div class="empty">⏳ Processando... aguarde</div>';
  
  try{
    const form = new FormData();
    form.append('action', 'check');
    form.append('lista', lista);
    
    const resp = await fetch('', { method: 'POST', body: form });
    const data = await resp.json();
    
    if(!data.ok){
      alert('Erro: ' + data.msg);
      renderizarLives();
      return;
    }
    
    // 🔥 Aqui está a mágica: só adiciona os LIVE, descarta os DIE
    let novosLives = 0;
    data.resultados.forEach(r => {
      if(r.live && !livesAcumulados.includes(r.linha)){
        livesAcumulados.push(r.linha);
        novosLives++;
      }
    });
    
    salvarLives();
    renderizarLives();
    
    if(novosLives > 0){
      console.log(`✅ ${novosLives} novo(s) LIVE adicionado(s)`);
    } else {
      console.log('Nenhum novo LIVE nesta lista');
    }
    
    // Limpa textarea automaticamente após checar
    document.getElementById('lista').value = '';
    
  }catch(e){
    alert('Erro: ' + e.message);
    renderizarLives();
  }finally{
    btn.disabled = false;
    spinner.style.display = 'none';
    btnText.textContent = '🚀 Iniciar Validação';
  }
}

async function copiar(){
  if(livesAcumulados.length === 0){
    alert('Nenhum aprovado para copiar!');
    return;
  }
  const texto = livesAcumulados.join('\n');
  try{
    await navigator.clipboard.writeText(texto);
    const btn = event.target;
    const orig = btn.innerHTML;
    btn.innerHTML = '✅ Copiado!';
    setTimeout(() => btn.innerHTML = orig, 1500);
  }catch{
    alert('Erro ao copiar. Selecione manualmente.');
  }
}

function limparTudo(){
  if(livesAcumulados.length === 0 && !document.getElementById('lista').value){
    return;
  }
  if(!confirm('Isso vai limpar TODOS os aprovados acumulados. Continuar?')){
    return;
  }
  livesAcumulados = [];
  salvarLives();
  renderizarLives();
  document.getElementById('lista').value = '';
}

// Inicializa ao carregar a página
carregarLives();
</script>

</body>
</html>
