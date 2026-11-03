<?php

error_reporting(0);

?>
<script type="text/javascript">
	var custo = "0";
	var descricao_chk = "CHARGE CHK | @ryan704";
	var audio = new Audio('live.wav');
</script>
<!DOCTYPE html>
<html>
<head>
	<title>CHARGE CHK | @ryan704</title>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<meta http-equiv="X-UA-Compatible" content="ie=edge">
	<!-- bootstrap -->
	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css">
	<!-- fontawesome -->
	<link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.15.1/css/all.css">
	
		<style type="text/css">
			:root {
				--bg-deep: #050505;
				--bg-mid: #151515;
				--bg-soft: #2e2e2e;
				--panel: rgba(255, 255, 255, 0.08);
				--panel-strong: rgba(255, 255, 255, 0.13);
				--line: rgba(255, 255, 255, 0.14);
				--line-bright: rgba(255, 255, 255, 0.25);
				--text: #f5f5f5;
				--muted: #a7a7a7;
				--dark-text: #101010;
				--shadow: 0 24px 70px rgba(0, 0, 0, 0.42);
			}

			* {
				box-sizing: border-box;
			}

			html,
			body {
				min-height: 100%;
				background-color: var(--bg-deep);
			}

			html {
				overscroll-behavior-y: none;
			}

			body.checker-page {
				min-height: 100vh;
				margin: 0;
				overscroll-behavior-y: none;
				padding: 28px 18px 46px;
				background:
					radial-gradient(circle at 8% 0%, rgba(255, 255, 255, 0.14), transparent 28%),
					radial-gradient(circle at 92% 100%, rgba(255, 255, 255, 0.08), transparent 32%),
					linear-gradient(135deg, var(--bg-deep) 0%, var(--bg-mid) 52%, var(--bg-soft) 100%);
				background-attachment: scroll;
				color: var(--text);
				font-family: Inter, ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif;
			}

			.checker-page button,
			.checker-page a,
			.checker-page textarea {
				font-family: inherit;
			}

			.page-shell {
				position: relative;
				max-width: 1180px;
				margin: 0 auto;
			}

			.topbar {
				display: flex;
				align-items: center;
				justify-content: space-between;
				gap: 22px;
				margin-bottom: 24px;
			}

			.brand-lockup,
			.back-link,
			.system-state,
			.status-line,
			.metric-label,
			.nav-link,
			.result-actions,
			.result-heading {
				display: flex;
				align-items: center;
			}

			.brand-lockup {
				gap: 13px;
			}

			.brand-mark {
				display: grid;
				width: 46px;
				height: 46px;
				place-items: center;
				border: 1px solid var(--line-bright);
				border-radius: 15px;
				background: linear-gradient(145deg, #f4f4f4 0%, #777 48%, #222 100%);
				box-shadow: 0 10px 25px rgba(0, 0, 0, 0.3);
				color: #0a0a0a;
				font-size: 1.15rem;
			}

			.brand-copy {
				display: flex;
				flex-direction: column;
				gap: 2px;
			}

			.eyebrow,
			.section-kicker,
			.metric-label,
			.status-label {
				color: var(--muted);
				font-size: 0.68rem;
				font-weight: 700;
				letter-spacing: 0.16em;
				text-transform: uppercase;
			}

			.brand-copy h1 {
				margin: 0;
				color: var(--text);
				font-size: 1.08rem;
				font-weight: 700;
				letter-spacing: 0.01em;
			}

			.back-link {
				order: 2;
				gap: 9px;
				padding: 10px 15px;
				border: 1px solid var(--line);
				border-radius: 12px;
				background: rgba(255, 255, 255, 0.07);
				box-shadow: 0 8px 22px rgba(0, 0, 0, 0.22);
				color: var(--text) !important;
				font-size: 0.83rem;
				font-weight: 600;
				text-decoration: none !important;
				transition: transform 0.2s ease, background 0.2s ease, border-color 0.2s ease;
			}

			.back-link:hover {
				transform: translateY(-2px);
				border-color: var(--line-bright);
				background: rgba(255, 255, 255, 0.14);
			}

			.system-state {
				gap: 8px;
				margin-left: auto;
				color: var(--muted);
				font-size: 0.78rem;
			}

			.pulse-dot {
				display: inline-block;
				width: 8px;
				height: 8px;
				border-radius: 50%;
				background: #d7d7d7;
				box-shadow: 0 0 0 5px rgba(255, 255, 255, 0.08), 0 0 16px rgba(255, 255, 255, 0.7);
			}

			.hero-panel {
				position: relative;
				display: grid;
				grid-template-columns: 1fr;
				gap: 24px;
				padding: 30px;
				border: 1px solid var(--line);
				border-radius: 26px;
				background: linear-gradient(135deg, rgba(255, 255, 255, 0.14), rgba(255, 255, 255, 0.045));
				box-shadow: var(--shadow);
				overflow: hidden;
			}

			.hero-panel::after {
				content: "";
				position: absolute;
				width: 260px;
				height: 260px;
				right: -120px;
				top: -130px;
				border: 1px solid rgba(255, 255, 255, 0.08);
				border-radius: 50%;
				box-shadow: 0 0 0 28px rgba(255, 255, 255, 0.025), 0 0 0 56px rgba(255, 255, 255, 0.018);
				pointer-events: none;
			}

			.hero-copy {
				position: relative;
				z-index: 1;
				display: flex;
				flex-direction: column;
				justify-content: center;
				min-height: 205px;
			}

			.hero-copy h2 {
				max-width: 670px;
				margin: 9px 0 11px;
				font-size: clamp(1.65rem, 3vw, 2.55rem);
				font-weight: 800;
				line-height: 1.12;
				letter-spacing: -0.04em;
			}

			.hero-copy p {
				max-width: 590px;
				margin: 0;
				color: var(--muted);
				font-size: 0.94rem;
				line-height: 1.7;
			}

			.control-panel {
				position: relative;
				z-index: 1;
				display: flex;
				flex-direction: column;
				justify-content: space-between;
				gap: 22px;
				padding: 20px;
				border: 1px solid var(--line);
				border-radius: 20px;
				background: rgba(0, 0, 0, 0.22);
			}

			.control-heading {
				display: flex;
				align-items: center;
				justify-content: space-between;
				gap: 12px;
				color: var(--text);
				font-size: 0.95rem;
				font-weight: 700;
			}

			.control-heading i {
				color: #d0d0d0;
			}

			.action-grid {
				display: grid;
				grid-template-columns: repeat(2, minmax(0, 1fr));
				gap: 10px;
			}

			.checker-page button {
				-webkit-tap-highlight-color: transparent;
				user-select: none;
				-webkit-user-select: none;
				will-change: transform;
				transform: translateZ(0);
			}

			.action-btn,
			.icon-btn {
				border: 1px solid var(--line-bright);
				border-radius: 11px;
				background: linear-gradient(135deg, #414141 0%, #171717 100%);
				box-shadow: inset 0 1px 0 rgba(255, 255, 255, 0.08), 0 7px 16px rgba(0, 0, 0, 0.2);
				color: var(--text);
				cursor: pointer;
				font-size: 0.78rem;
				font-weight: 700;
				transition: transform 0.2s ease, filter 0.2s ease, border-color 0.2s ease;
			}

			.action-btn {
				padding: 12px 10px;
			}

			.action-btn:hover,
			.icon-btn:hover {
				transform: translateY(-2px);
				border-color: rgba(255, 255, 255, 0.5);
				filter: brightness(1.2);
			}

			.action-btn.primary {
				background: linear-gradient(135deg, #f1f1f1 0%, #8e8e8e 100%);
				color: var(--dark-text);
			}

			.checker-page button:active:not(:disabled) {
				transform: translateY(1px) scale(0.98);
				box-shadow: inset 0 2px 5px rgba(0, 0, 0, 0.42), 0 2px 7px rgba(0, 0, 0, 0.22);
				filter: brightness(0.94);
				transition-duration: 0.08s;
			}

			.action-btn:disabled {
				cursor: not-allowed;
				opacity: 0.42;
				transform: none;
				filter: grayscale(1);
				will-change: auto;
			}

			.status-line {
				justify-content: space-between;
				gap: 12px;
				padding-top: 13px;
				border-top: 1px solid var(--line);
			}

			.status-label {
				font-size: 0.62rem;
			}

			#estatus {
				border: 1px solid var(--line-bright);
				border-radius: 999px;
				background: rgba(255, 255, 255, 0.12);
				color: var(--text);
				font-size: 0.72rem;
				font-weight: 700;
				letter-spacing: 0.02em;
				padding: 7px 11px;
			}

			.dashboard-grid {
				display: grid;
				gap: 20px;
				margin-top: 20px;
			}

			.stats-grid {
				display: grid;
				grid-template-columns: repeat(4, minmax(0, 1fr));
				gap: 11px;
			}

			.metric-card {
				position: relative;
				min-height: 100px;
				padding: 16px;
				border: 1px solid var(--line);
				border-radius: 17px;
				background: linear-gradient(145deg, rgba(255, 255, 255, 0.12), rgba(255, 255, 255, 0.035));
				box-shadow: 0 12px 30px rgba(0, 0, 0, 0.18);
				overflow: hidden;
			}

			.metric-card::after {
				content: "";
				position: absolute;
				width: 68px;
				height: 68px;
				right: -30px;
				top: -30px;
				border: 1px solid rgba(255, 255, 255, 0.14);
				border-radius: 50%;
			}

			.metric-label {
				justify-content: space-between;
				gap: 8px;
				font-size: 0.61rem;
			}

			.metric-label i {
				color: #d2d2d2;
				font-size: 0.86rem;
			}

			.metric-value {
				display: block;
				margin-top: 12px;
				color: var(--text);
				font-size: 1.65rem;
				font-weight: 800;
				line-height: 1;
			}

			.workspace-card {
				padding: 18px;
				border: 1px solid var(--line);
				border-radius: 24px;
				background: linear-gradient(145deg, rgba(255, 255, 255, 0.1), rgba(255, 255, 255, 0.035));
				box-shadow: var(--shadow);
			}

			.workspace-heading {
				display: flex;
				align-items: end;
				justify-content: space-between;
				gap: 18px;
				padding: 6px 8px 18px;
			}

			.workspace-heading h2 {
				margin: 6px 0 0;
				font-size: 1.18rem;
				font-weight: 750;
			}

			.workspace-heading p {
				margin: 0;
				color: var(--muted);
				font-size: 0.78rem;
			}

			.nav-tabs {
				display: flex;
				gap: 8px;
				padding: 7px;
				border: 1px solid var(--line) !important;
				border-radius: 16px;
				background: rgba(0, 0, 0, 0.24);
			}

			.nav-tabs .nav-item {
				flex: 1 1 0;
			}

			.nav-tabs .nav-link {
				justify-content: center;
				gap: 9px;
				min-height: 45px;
				border: 1px solid transparent !important;
				border-radius: 11px;
				color: var(--muted);
				font-size: 0.78rem;
				font-weight: 700;
				transition: color 0.2s ease, background 0.2s ease, transform 0.2s ease;
			}

			.nav-tabs .nav-link:hover {
				border-color: var(--line) !important;
				background: rgba(255, 255, 255, 0.07) !important;
				color: var(--text);
			}

			.nav-tabs .nav-link.active {
				border-color: rgba(255, 255, 255, 0.45) !important;
				background: linear-gradient(135deg, #f1f1f1 0%, #8c8c8c 100%) !important;
				box-shadow: 0 7px 18px rgba(0, 0, 0, 0.25);
				color: #111 !important;
				transform: translateY(-1px);
			}

			.tab-content {
				padding: 0;
				background: transparent;
				color: var(--text);
			}

			.tab-pane {
				padding: 23px 8px 8px;
			}

			.input-card {
				padding: 22px;
				border: 1px solid var(--line);
				border-radius: 18px;
				background: rgba(0, 0, 0, 0.2);
			}

			.input-card label {
				display: block;
				margin-bottom: 10px;
				color: var(--text);
				font-size: 0.9rem;
				font-weight: 700;
			}


			textarea {
				display: block;
				width: 100%;
				min-height: 260px;
				padding: 17px;
				border: 1px solid var(--line-bright);
				border-radius: 14px;
				outline: none;
				background: linear-gradient(145deg, #0a0a0a 0%, #292929 100%);
				box-shadow: inset 0 1px 16px rgba(0, 0, 0, 0.32);
				color: var(--text);
				font-size: 0.9rem;
				line-height: 1.6;
				resize: vertical;
				transition: border-color 0.2s ease, box-shadow 0.2s ease;
			}

			textarea::placeholder {
				color: #777;
			}

			textarea:focus {
				border-color: rgba(255, 255, 255, 0.55);
				box-shadow: 0 0 0 4px rgba(255, 255, 255, 0.07), inset 0 1px 16px rgba(0, 0, 0, 0.35);
			}

			.input-footer {
				display: flex;
				justify-content: space-between;
				gap: 12px;
				margin-top: 11px;
				color: var(--muted);
				font-size: 0.72rem;
			}

			.result-panel {
				padding: 22px;
				border: 1px solid var(--line);
				border-radius: 18px;
				background: rgba(0, 0, 0, 0.2);
			}

			.result-heading {
				justify-content: space-between;
				gap: 18px;
				margin-bottom: 16px;
			}

			.result-heading h3 {
				margin: 5px 0 0;
				font-size: 1.05rem;
				font-weight: 750;
			}

			.result-actions {
				gap: 8px;
			}

			.icon-btn {
				width: 38px;
				height: 38px;
				padding: 0;
			}

			.result-list {
				min-height: 150px;
				max-height: 330px;
				overflow: auto;
				padding: 16px;
				border: 1px dashed var(--line-bright);
				border-radius: 13px;
				background: linear-gradient(145deg, rgba(0, 0, 0, 0.33), rgba(255, 255, 255, 0.04));
				color: #d8d8d8;
				font-size: 0.83rem;
				line-height: 1.65;
			}

			.result-list:empty::before {
				content: "Nenhum resultado disponÃ­vel ainda";
				display: grid;
				min-height: 116px;
				place-items: center;
				color: #777;
				font-size: 0.78rem;
			}

			@media (max-width: 860px) {
				.hero-panel {
					grid-template-columns: 1fr;
				}

				.stats-grid {
					grid-template-columns: repeat(3, minmax(0, 1fr));
				}
			}


				@media (max-width: 620px) {

				body.checker-page {
					padding: 18px 12px 34px;
				}

				.topbar,
				.workspace-heading,
				.result-heading {
					align-items: flex-start;
					flex-direction: column;
				}

				.system-state {
					margin-left: 0;
				}

				.back-link {
					order: initial;
				}

				.hero-panel {
					padding: 20px;
					border-radius: 20px;
				}

					.stats-grid {
						grid-template-columns: repeat(2, minmax(0, 1fr));
					}

					

					.nav-tabs .nav-link {
					min-height: 42px;
					padding: 8px 5px;
					font-size: 0;
				}

				.nav-tabs .nav-link i {
					font-size: 0.95rem;
				}

				.input-card,
				.result-panel {
					padding: 16px;
				}
			}
		</style>
</head>
<body class="checker-page">
	<input type="hidden" value="<?php echo $base64Value; ?>" name="token_api" id="token_api">

	<div class="page-shell">
					<section class="hero-panel">


			<div class="control-panel">
				<div class="action-grid">
					<button type="button" class="action-btn primary" id="chk-start"><i class="fas fa-play"></i> Iniciar</button>
					<button type="button" class="action-btn" id="chk-pause" disabled><i class="fas fa-pause"></i> Pausar</button>
					<button type="button" class="action-btn" id="chk-stop" disabled><i class="fas fa-stop"></i> Parar</button>
					<button type="button" class="action-btn" id="chk-clean"><i class="fas fa-eraser"></i> Limpar</button>
				</div>
				<div class="status-line"><span class="status-label">Status</span><span class="badge badge-secondary" id="estatus">Aguardando inicio...</span></div>
			</div>
		</section>

		<main class="dashboard-grid">
			<section class="stats-grid" aria-label="Resumo dos resultados">
				<article class="metric-card"><span class="metric-label">Aprovadas <i class="fas fa-check"></i></span><strong class="metric-value val-lives">0</strong></article>
				<article class="metric-card"><span class="metric-label">Reprovadas <i class="fas fa-ban"></i></span><strong class="metric-value val-dies">0</strong></article>
				<article class="metric-card"><span class="metric-label">Testadas <i class="fas fa-sync-alt"></i></span><strong class="metric-value val-tested">0</strong></article>
				<article class="metric-card"><span class="metric-label">Total <i class="fas fa-layer-group"></i></span><strong class="metric-value val-total">0</strong></article>
			</section>

			<section class="workspace-card">
				<ul class="nav nav-tabs" id="myTab" role="tablist">
					<li class="nav-item"><a class="nav-link active" id="home-tab" data-toggle="tab" href="#chk-home" role="tab" aria-controls="home" aria-selected="true"><i class="far fa-credit-card"></i><span>Entrada</span></a></li>
					<li class="nav-item"><a class="nav-link" id="profile-tab" data-toggle="tab" href="#chk-lives" role="tab" aria-controls="profile" aria-selected="false"><i class="fas fa-thumbs-up"></i><span>Aprovadas</span></a></li>
					<li class="nav-item"><a class="nav-link" id="dies-tab" data-toggle="tab" href="#chk-dies" role="tab" aria-controls="dies" aria-selected="false"><i class="fas fa-thumbs-down"></i><span>Reprovadas</span></a></li>
					
				</ul>

				<div class="tab-content" id="myTabContent">
											<div class="tab-pane fade show active" id="chk-home" role="tabpanel" aria-labelledby="home-tab">
							<div class="input-card">
								<textarea id="lista_cartoes" placeholder="Insira sua lista..." rows="10" cols="rounded shadow"></textarea>
							</div>
						</div>



					<script>
						function apagarValoresLives() {
							var tabela = document.getElementById("lives");
							tabela.innerHTML = "";
						}
					</script>

					<div class="tab-pane fade" id="chk-lives" role="tabpanel" aria-labelledby="profile-tab">
						<div class="result-panel">
							<div class="result-heading"><div><h3>Aprovadas</h3></div><div class="result-actions"><button type="button" class="icon-btn" id="copyButton" title="Copiar resultados"><i class="fas fa-copy"></i></button><button type="button" class="icon-btn" onclick="apagarValoresLives()" title="Limpar resultados"><i class="fas fa-trash-alt"></i></button></div></div>
							<div class="result-list" id="lives"></div>
						</div>
					</div>

					<script>
						const copyButton = document.getElementById('copyButton');
						const livesDiv = document.getElementById('lives');

						copyButton.addEventListener('click', () => {
							const range = document.createRange();
							range.selectNode(livesDiv);
							window.getSelection().removeAllRanges();
							window.getSelection().addRange(range);

							try {
								const successful = document.execCommand('copy');
								const message = successful ? 'Copiado para a Ã¡rea de transferÃªncia!' : 'NÃ£o foi possÃ­vel copiar.';
								console.log(message);
							} catch (err) {
								console.error('Erro ao copiar: ', err);
							}

							window.getSelection().removeAllRanges();
						});
					</script>

					<script>
						function apagarValoresDies() {
							var tabela = document.getElementById("dies");
							tabela.innerHTML = "";
						}
					</script>

					<div class="tab-pane fade" id="chk-dies" role="tabpanel" aria-labelledby="dies-tab">
						<div class="result-panel">
							<div class="result-heading"><div><h3>Reprovadas</h3></div><div class="result-actions"><button type="button" class="icon-btn" onclick="apagarValoresDies()" title="Limpar resultados"><i class="fas fa-trash-alt"></i></button></div></div>
							<div class="result-list" id="dies"></div>
						</div>
					</div>

				</div>
			</section>
		</main>
	</div>

	<!-- jquery -->
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
	<!-- bootstrap -->
	<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js"></script>
	<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>

	 <!--<script>-->
  <!--      function contarLinhas(textarea) {-->
  <!--          const lines = textarea.value.split("\n");-->
  <!--          return lines.length;-->
  <!--      }-->

  <!--      function verificarLinhas() {-->
  <!--          const textarea = document.getElementById("lista_cartoes");-->
  <!--          const numLinhas = contarLinhas(textarea);-->

  <!--          if (numLinhas > 500) {-->
  <!--              alert("Limite ultrapassado de linhas.");-->
  <!--          }-->
  <!--      }-->
  <!--  </script>-->
	
<script type="text/javascript">
			$(document).ready(function() {

			// variaveis de informaÃ§Ã£o

		var testadas = [];
		var total = 0;
		var tested = 0;
		var lives = 0;
		var dies = 0;
		var stopped = true;
		var paused = true;
		        var token_api = document.getElementById("token_api").value;

		function removelinha() {
			var lines = $("textarea").val().split('\n');
			lines.splice(0, 1);
			$("textarea").val(lines.join("\n"));
		}

		function testar(tested, total, lista) {
			// verifica se nao estÃ¡ parado o checker
			if (stopped == true) {
				return false;
			}

			// verifica se nao estÃ¡ pausado o checker
			if (paused == true) {
				return false;
			}

			// verifica se ja terminou de testar
			if (tested >= total) {
				console.log('finalizado ' + tested + " de " + total);
				$("#estatus").attr("class", "badge badge-success").text("Teste finalizado");
				$("#chk-start").removeAttr('disabled');
				$("#chk-clean").removeAttr('disabled');
				$("#chk-stop").attr("disabled", "true");
				$("#chk-pause").attr("disabled", "true");
				return false;
			}

			// conteudo que serÃ¡ testado
			var conteudo = lista[tested];
            		            var token_api = document.getElementById("token_api").value;

		            $.ajax({
		                url: 'api.php',

                type: 'GET',
                                data: { lista: conteudo, token_api: token_api },

			})
			.done(function(response) {
				// verifica se nao estÃ¡ parado o checker
				if (stopped == true) {
					return false;
				}

				// verifica se nao estÃ¡ pausado o checker
				if (paused == true) {
					return false;
				}

				tested++;

				// verifica o retorno
				if (response.indexOf("Aprovada") >= 0) {
					lives++
					$("#estatus").attr("class", "badge badge-success").text(conteudo + " -> APR");
					$("#lives").append(response + "<br>");
					audio.play();
				} else if (response.indexOf("Reprovada") >= 0) {
					dies++
					$("#estatus").attr("class", "badge badge-danger").text(conteudo + " -> REP");
					$("#dies").append(response + "<br>");
					}

				// atualiza resultados
				$(".val-total").text(total);
				$(".val-lives").text(lives);
				$(".val-dies").text(dies);
				$(".val-tested").text(tested);

				// remove linha
				removelinha();
				console.log(response);

				// executa a funÃ§Ã£o novamente
				testar(tested, total, lista);
			})
			.fail(function() {
				return false;
			})
		}

		// ========== START ========== //
		function start() {
			var lista = $("textarea").val().trim().split('\n');

			var total = lista.length;

			$(".val-total").text(total);
			stopped = false;
			paused = false;
			$("#estatus").attr("class", "badge badge-success").text("Checker iniciado, aguarde...");

			// Libera os botÃµes
			$("#chk-stop").removeAttr('disabled');
			$("#chk-pause").removeAttr('disabled');
			$("#chk-start").attr("disabled", "true");
			$("#chk-clean").attr("disabled", "true");

			// Inicia o teste
			testar(tested, total, lista);
		}


		$("#chk-start").click(function() {
			if ($('textarea').val().trim() == "") {
				$('textarea').focus();
			} else {
				start();
			}
		});

		// ========== PAUSE ========== //
		function pause() {
			$("#chk-start").removeAttr('disabled');
			$("#chk-pause").attr("disabled", "true");
			paused = true;
			console.log('checker pausado');
			$("#estatus").attr("class", "badge badge-info").text("Checker pausado...");
		}

		$("#chk-pause").click(function() {
			pause();
		});

		// ========== STOP ========== //
		function stop() {
			stopped = true;
			$("#chk-start").removeAttr('disabled');
			$("#chk-clean").removeAttr('disabled');
			$("#chk-stop").attr("disabled", "true");
			$("#chk-pause").attr("disabled", "true");
			console.log('checker parado');
			$("#estatus").attr("class", "badge badge-secondary").text("Checker parado...");
		}

		$("#chk-stop").click(function() {
			stop();
		});

		// ========== CLEAN ========== //
		function clean() {
			testadas = [];
			total = 0;
			tested = 0;
			lives = 0;
			dies = 0;
			stopped = true;

			// atualiza resultados
			$(".val-total").text(total);
			$(".val-lives").text(lives);
			$(".val-dies").text(dies);
			
			$(".val-tested").text(tested);
			$("textarea").val("");
		}

		$("#chk-clean").click(function() {
			clean();
		});
	});
	
    
</script>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/crypto-js/4.0.0/crypto-js.min.js"></script>
    <script src="script.js"></script>

</body>
</html>
