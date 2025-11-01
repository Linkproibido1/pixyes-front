<?php
include '../../conectarbanco.php';
   
   
   // Conectar ao banco de dadosvalo
   $conn = new mysqli('localhost', $config['db_user'], $config['db_pass'], $config['db_name']);
   
   // Verifica se houve algum erro na conexão
   if ($conn->connect_error) {
       die("Erro na conexão com o banco de dados: " . $conn->connect_error);
   }
   
   function converterTempoParaSegundos($horario) {
       list($minutos, $segundos) = explode(':', $horario);
       return ($minutos * 60) + $segundos;
   }
   
   // Verifica se o parâmetro 'id' está presente na URL
   if (isset($_GET['id'])) {
       // Recupera o valor do parâmetro 'id'
       $id = $_GET['id'];
   
       // Sanitiza o valor do parâmetro
       $id = filter_var($id, FILTER_SANITIZE_STRING);
   
       $sql = "SELECT id, name_produto, valor, logo_produto, banner_produto, obrigado_page, key_gateway, ativo, email, header_cor_bg, header_cor_texto, header_texto, header_tempo, cor_bg, fretes, depoimentos, valor_oferta, logo_topo, id_facebook, id_kwai, id_tiktok, id_googleads
               FROM checkout_build
               WHERE id_checkout = ? ";
   
       // Prepara e executa a consulta
       $stmt = $conn->prepare($sql);
       $stmt->bind_param("s", $id);
       $stmt->execute();
       
       // Obtém os resultados
       $result = $stmt->get_result();
   
       // Verifica se há resultados
       if ($result->num_rows > 0) {
           // Exibe os dados encontrados
           $row = $result->fetch_assoc();
       } else {
           $row = null;
           echo "Nenhum registro encontrado para o ID especificado.";
       }
   
       // Fecha a declaração
       $stmt->close();
   } else {
       echo "Parâmetro 'id' não encontrado na URL.";
   }
   
   // Fecha a conexão
   $conn->close();
   
   function brlToDecimal($brlString) {
      $clean = str_replace(['R$', '.', ' '], '', $brlString);
      $number = str_replace(',', '.', $clean);
      return number_format((float)$number, 2, '.', '');
  }

   ?>
<!DOCTYPE html>
<html lang="pt-br">
   <head>
      <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
      <meta data-n-head="1" data-hid="description" name="description" content="">
      <meta data-n-head="1" name="format-detection" content="telephone=no">
      <meta data-n-head="1" data-hid="og:image" property="og:image" content="/meta-img.png">
      <title>Checkout </title>
      <link data-n-head="1" rel="icon" type="image/x-icon" href="">
      <style type="text/css">
      </style>
      <style type="text/css">
         @import url(https://fonts.googleapis.com/css2?family=Inter:wght@100;200;300;400;500;600;700;800;900&display=swap);
      </style>
      <style type="text/css">
         @import url(https://fonts.googleapis.com/css2?family=Montserrat:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap);
      </style>
      <style type="text/css">
         @import url(https://fonts.googleapis.com/css2?family=Azeret+Mono:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,900&display=swap);
      </style>
      <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
      <link href="arquivos/style.css" rel="stylesheet">
      <script charset="utf-8" src="arquivos/0739fa6.js"></script>
      <script charset="utf-8" src="arquivos/6e78416.js"></script>
      <link data-n-head="1" rel="preconnect" href="https://www.googletagmanager.com/">
      <link data-n-head="1" rel="preconnect" href="https://www.google-analytics.com/">
      <meta data-n-head="1" name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
      
      <?php 
        include('ads/google_ads.php');
        include('ads/fb_ads.php');
        include('ads/kwai_ads.php');
        include('ads/tiktok_ads.php');
      ?>
   </head>
   <style>
   </style>
   <div class="loading">
      <div class="loading-container">
         <div class="icon">
            <!--<svg fill="#000000" width="201px" height="201px" viewBox="0 0 24.00 24.00" id="loading" data-name="Line Color" xmlns="http://www.w3.org/2000/svg" class="icon line-color" transform="rotate(0)">
               <g id="SVGRepo_bgCarrier" stroke-width="0"/>
               <g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"/>
               <g id="SVGRepo_iconCarrier">
               <path id="primary" d="M16,18v3H8V18a6,6,0,0,1,2.4-4.8L12,12l1.6,1.2A6,6,0,0,1,16,18Z" style="fill: none; stroke: #000000; stroke-linecap: round; stroke-linejoin: round; stroke-width:1.464;"/>
               
               <path id="primary-2" data-name="primary" d="M13.6,10.8,12,12l-1.6-1.2A6,6,0,0,1,8,6V3h8V6A6,6,0,0,1,13.6,10.8Z" style="fill: none; stroke: #000000; stroke-linecap: round; stroke-linejoin: round; stroke-width:1.464;"/>
               <path id="secondary" d="M6,21H18M6,3H18" style="fill: none; stroke: #008001; stroke-linecap: round; stroke-linejoin: round; stroke-width:1.464;"/>
               </g>
               </svg>-->
            <img src="arquivos/loading.gif">
         </div>
         <div class="text-icon">Processando Pagamento...</div>
      </div>
   </div>
   <body cz-shortcut-listen="true">
      <div id="__nuxt">
         <div id="__layout">
            <script>
               function startCountdown(duration) {
                 const display = document.getElementById('countdown');
                 let timer = duration, minutes, seconds;
               
                 function updateDisplay() {
                   minutes = Math.floor(timer / 60);
                   seconds = timer % 60;
               
                   // Adiciona zeros à esquerda, se necessário
                   minutes = minutes < 10 ? '0' + minutes : minutes;
                   seconds = seconds < 10 ? '0' + seconds : seconds;
               
                   display.textContent = '00:' + minutes + ':' + seconds;
               
                   if (--timer < 0) {
                     timer = duration; // Reinicia o timer se necessário
                   }
                 }
               
                 // Atualiza a cada segundo
                 setInterval(updateDisplay, 1000);
                 updateDisplay(); // Atualiza imediatamente
               }
               
               window.onload = function () {
                 const fiveMinutes = <?= $row['header_tempo'] && !empty($row['header_tempo']) ? converterTempoParaSegundos($row['header_tempo']) : 60 * 5 ?>;
                 startCountdown(fiveMinutes);
               };
            </script>
            <div data-v-24d558e9="">
               <header data-v-24d558e9="" style="background-color: <?= $row['header_cor_bg'] ?? 'rgb(0, 0, 13)' ?>;">
                  <h2 id="countdown" data-v-24d558e9="" style="color: <?= $row['header_cor_texto'] ?? 'rgb(255, 255, 255)' ?>;">
                     00:<?= $row['header_tempo'] && !empty($row['header_tempo']) ? $row['header_tempo'] : '05:00' ?>
                  </h2>
                  <svg data-v-41f15b78="" data-v-24d558e9="" viewBox="0 0 512.000000 512.000000" style="fill: rgb(255, 255, 255);">
                     <g data-v-41f15b78="" transform="translate(0.000000,512.000000) scale(0.100000,-0.100000)" stroke="none">
                        <path data-v-41f15b78=""
                           d="M2651 5004 c-69 -19 -108 -43 -161 -100 -136 -148 -114 -380 48 -499 26 -19 64 -40 85 -47 l37 -11 0 -127 0 -127 -72 -18 c-233 -56 -514 -194 -723 -354 -76 -58 -243 -218 -308 -296 l-50 -60 -383 -5 -382 -5 -26 -24 c-50 -48 -39 -133 21 -160 16 -7 129 -11 323 -11 165 0 300 -2 300 -4 0 -2 -24 -50 -54 -107 -29 -57 -68 -143 -86 -191 l-33 -88 -563 0 -563 0 -26 -22 c-34 -30 -44 -88 -20 -124 35 -53 41 -54 601 -54 l516 0 -6 -22 c-7 -27 -33 -217 -42 -300 l-5 -58 -230 0 c-250 0 -274 -4 -297 -55 -15 -33 -15 -57 0 -90 23 -51 47 -55 301 -55 l234 0 7 -80 c6 -77 42 -275 52 -292 3 -4 -141 -8 -321 -8 -353 0 -377 -3 -410 -54 -24 -36 -14 -94 20 -124 26 -22 27 -22 401 -22 l375 0 11 -32 c20 -58 115 -244 168 -329 194 -313 487 -575 825 -739 491 -239 1018 -271 1550 -95 87 29 331 145 408 195 81 52 96 124 35 175 -40 34 -83 32 -143 -6 -94 -60 -265 -140 -382 -179 -540 -179 -1129 -100 -1598 215 -748 503 -1014 1475 -626 2290 248 520 710 879 1291 1002 114 25 145 27 350 27 206 0 236 -2 351 -27 183 -39 296 -78 464 -162 168 -84 286 -164 417 -285 628 -579 769 -1532 334 -2263 -55 -94 -62 -118 -46 -158 21 -49 93 -74 141 -49 25 14 77 97 143 228 342 83 271 1498 -185 2112 -77 104 -247 281 -349 363 -214 173 -482 310 -743 379 l-87 23 0 125 0 125 56 24 c71 31 139 97 172 169 37 81 38 193 0 273 -36 79 -89 133 -166 171 l-67 33 -415 2 c-341 2 -425 0 -469 -13z m839 -190 c43 -9 97 -65 106 -108 8 -46 -10 -96 -47 -132 l-30 -29 -410 -3 c-399 -3 -412 -2 -444 18 -64 39 -84 129 -42 191 45 68 49 69 463 69 204 0 386 -3 404 -6z m-150 -576 l0 -103 -240 0 -240 0 0 103 0 102 240 0 240 0 0 -102z">
                        </path>
                        <path data-v-41f15b78=""
                           d="M2910 3663 c-207 -33 -359 -81 -520 -164 -419 -216 -710 -601 -812 -1073 -32 -148 -32 -455 1 -606 90 -422 331 -774 686 -1002 507 -326 1163 -326 1670 0 355 228 596 580 686 1002 33 151 33 458 1 606 -138 637 -632 1117 -1260 1225 -106 18 -372 25 -452 12z m405 -208 c584 -96 1030 -550 1121 -1140 19 -121 14 -343 -10 -455 -59 -273 -180 -496 -375 -690 -198 -197 -414 -314 -684 -371 -135 -29 -380 -32 -510 -6 -187 38 -372 115 -527 220 -41 29 -123 99 -181 157 -195 194 -316 417 -375 690 -24 112 -29 334 -10 455 90 586 537 1043 1115 1140 106 18 328 18 436 0z">
                        </path>
                        <path data-v-41f15b78=""
                           d="M3051 3276 c-37 -20 -50 -55 -51 -129 0 -76 17 -118 57 -136 32 -15 59 -14 92 3 38 20 51 55 51 142 0 70 -2 78 -29 105 -33 32 -78 38 -120 15z">
                        </path>
                        <path data-v-41f15b78=""
                           d="M3765 2916 c-16 -8 -142 -127 -279 -264 l-249 -250 -40 14 c-49 17 -150 17 -197 -1 l-35 -13 -126 124 c-116 114 -129 124 -163 124 -48 0 -72 -13 -91 -50 -31 -60 -24 -74 110 -209 l124 -126 -14 -49 c-32 -106 -8 -216 66 -299 120 -137 338 -137 458 0 74 83 98 193 66 299 l-15 49 260 260 c255 256 260 262 260 302 0 30 -7 47 -26 67 -15 14 -32 26 -40 26 -7 0 -18 2 -26 5 -7 2 -26 -2 -43 -9z m-589 -720 c40 -40 45 -83 15 -132 -57 -93 -201 -47 -201 65 0 51 54 101 110 101 34 0 48 -6 76 -34z">
                        </path>
                        <path data-v-41f15b78=""
                           d="M1965 2196 c-46 -46 -43 -112 7 -149 29 -22 108 -33 161 -23 84 16 116 108 58 167 -28 28 -34 29 -116 29 -78 0 -88 -2 -110 -24z">
                        </path>
                        <path data-v-41f15b78=""
                           d="M4009 2191 c-21 -22 -29 -39 -29 -66 0 -79 45 -109 158 -103 69 3 76 6 103 36 31 35 36 66 17 107 -18 40 -57 55 -143 55 -71 0 -79 -2 -106 -29z">
                        </path>
                        <path data-v-41f15b78=""
                           d="M3073 1243 c-12 -2 -34 -18 -48 -34 -22 -27 -25 -40 -25 -110 0 -73 2 -81 29 -111 33 -37 82 -43 125 -14 35 22 46 53 46 126 0 32 -4 70 -10 84 -15 40 -70 68 -117 59z">
                        </path>
                        <path data-v-41f15b78="" d="M63 1595 c-99 -43 -71 -185 36 -185 96 0 138 124 59 173 -37 24 -61 27 -95 12z">
                        </path>
                        <path data-v-41f15b78=""
                           d="M4389 831 c-38 -39 -39 -87 -3 -130 21 -26 33 -31 68 -31 49 0 79 18 96 59 40 98 -85 177 -161 102z">
                        </path>
                     </g>
                  </svg>
                  <p data-v-24d558e9="" style="color: <?= $row['header_cor_texto'] ?? 'rgb(255, 255, 255)' ?>;">
                     <?= $row['header_texto'] && !empty($row['header_texto']) ? $row['header_texto'] : 'Sua compra está reservada!' ?>
                  </p>
               </header>
               <div class="logo">
                  <figure style="align-content: center;">
                     <?php if(!empty($row['logo_topo'])): ?>
                     <img src="../<?= $row['logo_topo'] ?>" alt="Logo" width="120px" style="aspect-ratio: auto">
                     <?php endif; ?>
                  </figure>
                  <figure style="align-content: center;">
                     <img src="https://pay.risepay.com.br/icons/safe-payment.svg" alt="Ícone de escudo" style="aspect-ratio: auto">
                  </figure>
               </div>
               <main data-v-24d558e9="">
                  <div data-v-24d558e9="" class="content-wrapper">
                     <h5 data-v-24d558e9="">VOCÊ ESTÁ ADQUIRINDO:</h5>
                     <div data-v-24d558e9="" class="contentProductData">
                        <div data-v-24d558e9="" class="image-content"><img data-v-24d558e9="" alt="imagem do produto"
                           fetchpriority="high" rel="preload" src="../../<?php echo htmlspecialchars($row['logo_produto']); ?>" style=""></div>
                        <div data-v-24d558e9="" class="infoProduct">
                           <h4 data-v-24d558e9=""><?php echo htmlspecialchars($row['name_produto']); ?></h4>
                           <span data-v-24d558e9="">
                              <h3 data-v-24d558e9="">
                                 <?php if(!empty($row['valor_oferta'])): ?>
                                 <span class="text-danger">De: R$ <del> <?= number_format(brlToDecimal($row['valor_oferta']), 2, ',', '.'); ?></del></span>
                                 <?php echo "RS " . number_format(brlToDecimal($row['valor']), 2, ',', '.'); ?>
                                 <?php else: ?>
                                 <?php echo "RS " . number_format(brlToDecimal($row['valor']), 2, ',', '.'); ?>
                                 <?php endif; ?>
                              </h3>
                           </span>
                        </div>
                     </div>
                  </div>
                  <div data-v-24d558e9="" class="content-wrapper secondary">
                     <div class="box1" style="display: none;">
                        <h2 data-v-4dd5f040="">
                           Siga os passos para pagar:
                        </h2>
                        <p data-v-4dd5f040=""><span data-v-4dd5f040="" class="numberIcon">1 - </span>Copie o código
                           <strong data-v-4dd5f040="">PIX:</strong>
                        </p>
                        <div class="conteiner">
                           <div id="qrcode"></div>
                           <div class="divqr">
                              <div id="qr-code-text"></div>
                              <button id="copy-button">Copiar Código Pix</button>
                           </div>
                        </div>
                        <br>
                        <script>
                           document.getElementById('copy-button').addEventListener('click', function() {
                               // Seleciona o texto do div
                               var textToCopy = document.getElementById('qr-code-text').innerText;
                               
                               // Cria um elemento de input temporário para usar o comando de copiar
                               var tempInput = document.createElement('input');
                               tempInput.value = textToCopy;
                               document.body.appendChild(tempInput);
                               
                               // Seleciona o texto no input e copia
                               tempInput.select();
                               document.execCommand('copy');
                               
                               // Remove o input temporário da página
                               document.body.removeChild(tempInput);
                               
                               // Feedback opcional: Alerta ou uma mensagem para o usuário
                               alert('Código Pix copiado para a área de transferência!');
                           });
                        </script>
                        <p data-v-4dd5f040=""><span data-v-4dd5f040="" class="numberIcon">2 - </span>Abra o aplicativo do seu banco
                           favorito
                        </p>
                        <p data-v-4dd5f040=""><span data-v-4dd5f040="" class="numberIcon">3 - </span> <span data-v-4dd5f040="">
                           Na seção de PIX, selecione a opção
                           <strong data-v-4dd5f040="">"Pix Copia e Cola"</strong></span>
                        </p>
                        <p data-v-4dd5f040=""><span data-v-4dd5f040="" class="numberIcon">4 - </span>Cole o código</p>
                        <p data-v-4dd5f040=""><span data-v-4dd5f040="" class="numberIcon">5 - </span>Confirme o pagamento</p>
                     </div>
                     <div id="apiResponse"style="display: none;"></div>
                     <div class="properties">
                        <form data-v-24d558e9="">
                           <h2 data-v-24d558e9=""><span data-v-24d558e9="" class="orderNumber">1</span> Dados pessoais</h2>
                           <div data-v-24d558e9="" class="input-wrapper">
                              <label data-v-24d558e9="" for="userName">NOME
                              COMPLETO</label>
                              <div data-v-24d558e9="" class="input-icons user-icon">
                                 <input data-v-24d558e9="" type="text" inputmode="text" id="name" name="name" class="" style="padding-left: 35px;">
                              </div>
                              <span data-v-24d558e9="" class="alertText" style="display: none;">
                              </span>
                           </div>
                           <div data-v-24d558e9="" class="input-wrapper">
                              <label data-v-24d558e9="" for="yourEmail">Seu e-mail</label>
                              <div data-v-24d558e9="" class="input-icons email-icon">
                                 <input data-v-24d558e9="" type="email" inputmode="text" name="yourEmail" class="">
                              </div>
                              <span data-v-24d558e9="" class="alertText" style="display: none;">
                              </span>
                           </div>
                           <div data-v-24d558e9="" class="containerInputs">
                              <div data-v-24d558e9="" class="input-wrapper">
                                 <label data-v-24d558e9="" for="userDocument">CPF</label>
                                 <div data-v-24d558e9="" class="input-icons padlock-icon">
                                    <input data-v-24d558e9="" type="tel" inputmode="tel" id="document" name="document"
                                       autocomplete="on" class="">
                                 </div>
                                 <span data-v-24d558e9="" class="alertText" style="display: none;">
                                 Digite um documento válido.</span>
                              </div>
                              <div data-v-24d558e9="" class="input-wrapper">
                                 <label data-v-24d558e9="" for="userPhone">CELULAR COM
                                 WHATSAPP</label>
                                 <div data-v-24d558e9="" class="input-icons phone-icon"><input data-v-24d558e9="" type="tel"
                                    inputmode="tel" name="userPhone" class=""></div>
                                 <span data-v-24d558e9="" class="alertText"
                                    style="display: none;">
                                 </span>
                              </div>
                              <?php
                                 // Certifique-se de que $row['valor'] é um valor numérico
                                 $valor = brlToDecimal($row['valor']);
                                 ?>
                              <div class="valor" style="display: none;">
                                 <input type="text" value="<?php echo htmlspecialchars($valor); ?>" placeholder="<?php echo htmlspecialchars($valor); ?>" readonly id="valuedeposit">
                              </div>
                           </div>
                           <h2 data-v-24d558e9="">
                              <span data-v-24d558e9="" class="orderNumber">2</span> Dados da Entrega
                           </h2>
                           <div class="input-wrapper">
                              <!-- CEP -->
                              <div class="input-row">
                                 <div class="half-width">
                                    <label for="zip">CEP</label>
                                    <input type="text" id="zip" name="zip" placeholder="12345-000" onblur="fetchAddress()">
                                 </div>
                              </div>
                              <!-- Endereço e Número -->
                              <div class="input-row2">
                                 <div class="half-width" style="width: 50%;">
                                    <label for="address">ENDEREÇO</label>
                                    <input type="text" id="address" name="address" placeholder="Rua, Avenida, Alameda">
                                 </div>
                                 <div class="half-width">
                                    <label for="number">NÚMERO</label>
                                    <input type="text" id="number" name="number" placeholder="3213">
                                 </div>
                              </div>
                              <!-- Complemento e Bairro -->
                              <div class="input-row2">
                                 <div class="half-width" style="width: 50%;">
                                    <label for="complement">COMPLEMENTO</label>
                                    <input type="text" id="complement" name="complement" placeholder="Apartamento, unidade, prédio, andar, etc.">
                                 </div>
                                 <div class="half-width">
                                    <label for="neighborhood">BAIRRO</label>
                                    <input type="text" id="neighborhood" name="neighborhood" placeholder="Centro">
                                 </div>
                              </div>
                              <!-- Cidade e Estado -->
                              <div class="input-row2">
                                 <div class="half-width" style="width: 50%;">
                                    <label for="city">CIDADE</label>
                                    <input type="text" id="city" name="city" placeholder="Cidade">
                                 </div>
                                 <div class="half-width" style="width: 50%;">
                                    <label for="state">ESTADO</label>
                                    <input type="text" id="state" name="state" placeholder="SP" maxlength="2">
                                 </div>
                              </div>
                           </div>
                           <script>
                              function fetchAddress() {
                                  const zip = document.getElementById('zip').value.replace(/\D/g, '');
                                  if (zip.length === 8) {
                                      fetch(`https://viacep.com.br/ws/${zip}/json/`)
                                          .then(response => response.json())
                                          .then(data => {
                                              if (data.uf) {
                                                  document.getElementById('address').value = data.logradouro;
                                                  document.getElementById('neighborhood').value = data.bairro;
                                                  document.getElementById('city').value = data.localidade;
                                                  document.getElementById('state').value = data.uf;
                                              } else {
                                                  alert('CEP não encontrado.');
                                              }
                                          })
                                          .catch(() => alert('Erro ao buscar CEP.'));
                                  }
                              }
                           </script>
                           <?php
                              $fretes = $row['fretes'];
                              $json_fretes = json_decode($fretes, true);
                              
                              if (is_array($json_fretes) && count($json_fretes) > 0): ?>
                           <div class="mt-5 properties">
                              <div class="card frete-card" style="background-color: unset;">
                                 <div class="frete-header">Opções de Frete</div>
                                 <?php foreach ($json_fretes as $index => $frete): 
                                    $id_frete = strtolower(str_replace(' ', '_', preg_replace('/[^a-zA-Z0-9\s]/', '', $frete['nome'])));
                                    ?>
                                 <div class="frete-option" onclick="selectFrete('<?= $id_frete ?>')">
                                    <input class="form-check-input" type="radio" name="frete" id="<?= $id_frete ?>">
                                    <div class="frete-info">
                                       <label for="<?= $id_frete ?>" class="frete-name">
                                       <?= strtoupper($frete['nome']) ?>
                                       </label>
                                       <span class="frete-prazo">
                                       <?= intval($frete['min']) ?> até <?= intval($frete['max']) ?> dias úteis
                                       </span>
                                    </div>
                                    <span class="frete-valor">
                                    <?= (intval($frete['valor']) > 0) 
                                       ? 'R$ <span class="frete_valor">' . number_format(brlToDecimal($frete['valor']), 2, ',', '.') . '</span>' 
                                       : '<span class="frete_valor gratis">Grátis</span>' ?>
                                    </span>
                                 </div>
                                 <?php endforeach; ?>
                              </div>
                           </div>
                           <?php endif; ?>
                           <h2 data-v-24d558e9=""><span data-v-24d558e9="" class="orderNumber">3</span> DADOS DE PAGAMENTO</h2>
                           <div data-v-24d558e9="" id="pixOption" class="tab-content">
                              <h4 data-v-24d558e9="">Pague no PIX</h4>
                              <h3 data-v-24d558e9="">
                                 <img data-v-24d558e9="" fetchpriority="high" rel="preload"
                                    src="data:image/svg+xml;base64,PHN2ZyB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIHdpZHRoPSIxNiIgaGVpZ2h0PSIxNiIgZmlsbD0iIzAwZDNjNyIgdmlld0JveD0iMCAwIDE2IDE2Ij4KICAgICAgICAgICAgICAgICAgICAgICAgICA8cGF0aCBkPSJNNi41IDBhLjUuNSAwIDAgMCAwIDFIN3YxLjA3QTcuMDAxIDcuMDAxIDAgMCAwIDggMTZhNyA3IDAgMCAwIDUuMjktMTEuNTg0LjUzMS41MzEgMCAwIDAgLjAxMy0uMDEybC4zNTQtLjM1NC4zNTMuMzU0YS41LjUgMCAxIDAgLjcwNy0uNzA3bC0xLjQxNC0xLjQxNWEuNS41IDAgMSAwLS43MDcuNzA3bC4zNTQuMzU0LS4zNTQuMzU0YS43MTcuNzE3IDAgMCAwLS4wMTIuMDEyQTYuOTczIDYuOTczIDAgMCAwIDkgMi4wNzFWMWguNWEuNS41IDAgMCAwIDAtMWgtM3ptMiA1LjZWOWEuNS41IDAgMCAxLS41LjVINC41YS41LjUgMCAwIDEgMC0xaDNWNS42YS41LjUgMCAxIDEgMSAweiIgLz4KICAgICAgICAgICAgICAgICAgICAgICAgPC9zdmc+">
                                 Imediato
                              </h3>
                              <p data-v-24d558e9="">
                                 Ao selecionar a opção Gerar Pix o código para pagamento estará
                                 disponível.
                              </p>
                              <h3 data-v-24d558e9=""><img data-v-24d558e9="" fetchpriority="high" rel="preload"
                                 src="data:image/svg+xml;base64,PHN2ZyB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIHdpZHRoPSIxNiIgaGVpZ2h0PSIxNiIgZmlsbD0iIzAwZDNjNyIgdmlld0JveD0iMCAwIDE2IDE2Ij4KICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICA8cGF0aCBkPSJNMiAyaDJ2MkgyVjJaIiAvPgogICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIDxwYXRoIGQ9Ik02IDB2NkgwVjBoNlpNNSAxSDF2NGg0VjFaTTQgMTJIMnYyaDJ2LTJaIiAvPgogICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIDxwYXRoIGQ9Ik02IDEwdjZIMHYtNmg2Wm0tNSAxdjRoNHYtNEgxWm0xMS05aDJ2MmgtMlYyWiIgLz4KICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICA8cGF0aCBkPSJNMTAgMHY2aDZWMGgtNlptNSAxdjRoLTRWMWg0Wk04IDFWMGgxdjJIOHYySDdWMWgxWm0wIDVWNGgxdjJIOFpNNiA4VjdoMVY2aDF2MmgxVjdoNXYxaC00djFIN1Y4SDZabTAgMHYxSDJWOEgxdjFIMFY3aDN2MWgzWm0xMCAxaC0xVjdoMXYyWm0tMSAwaC0xdjJoMnYtMWgtMVY5Wm0tNCAwaDJ2MWgtMXYxaC0xVjlabTIgM3YtMWgtMXYxaC0xdjFIOXYxaDN2LTJoMVptMCAwaDN2MWgtMnYxaC0xdi0yWm0tNC0xdjFoMXYtMkg3djFoMloiIC8+CiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgPHBhdGggZD0iTTcgMTJoMXYzaDR2MUg3di00Wm05IDJ2MmgtM3YtMWgydi0xaDFaIiAvPgogICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgPC9zdmc+">
                                 PAGAMENTO SIMPLES
                              </h3>
                              <p data-v-24d558e9="">
                                 Para pagar basta abrir o aplicativo do seu banco, procurar pelo
                                 PIX e escanear o QRcode.
                              </p>
                              <h3 data-v-24d558e9=""><img data-v-24d558e9="" fetchpriority="high" rel="preload"
                                 src="data:image/svg+xml;base64,PHN2ZyB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIHdpZHRoPSIxNiIgaGVpZ2h0PSIxNiIgZmlsbD0iIzAwZDNjNyIgdmlld0JveD0iMCAwIDE2IDE2Ij4KICAgICAgICAgICAgICAgICAgICAgICAgICA8cGF0aCBmaWxsLXJ1bGU9ImV2ZW5vZGQiIGQ9Ik04IDBjLS42OSAwLTEuODQzLjI2NS0yLjkyOC41Ni0xLjExLjMtMi4yMjkuNjU1LTIuODg3Ljg3YTEuNTQgMS41NCAwIDAgMC0xLjA0NCAxLjI2MmMtLjU5NiA0LjQ3Ny43ODcgNy43OTUgMi40NjUgOS45OWExMS43NzcgMTEuNzc3IDAgMCAwIDIuNTE3IDIuNDUzYy4zODYuMjczLjc0NC40ODIgMS4wNDguNjI1LjI4LjEzMi41ODEuMjQuODI5LjI0cy41NDgtLjEwOC44MjktLjI0YTcuMTU5IDcuMTU5IDAgMCAwIDEuMDQ4LS42MjUgMTEuNzc1IDExLjc3NSAwIDAgMCAyLjUxNy0yLjQ1M2MxLjY3OC0yLjE5NSAzLjA2MS01LjUxMyAyLjQ2NS05Ljk5YTEuNTQxIDEuNTQxIDAgMCAwLTEuMDQ0LTEuMjYzIDYyLjQ2NyA2Mi40NjcgMCAwIDAtMi44ODctLjg3QzkuODQzLjI2NiA4LjY5IDAgOCAwem0yLjE0NiA1LjE0NmEuNS41IDAgMCAxIC43MDguNzA4bC0zIDNhLjUuNSAwIDAgMS0uNzA4IDBsLTEuNS0xLjVhLjUuNSAwIDEgMSAuNzA4LS43MDhMNy41IDcuNzkzbDIuNjQ2LTIuNjQ3eiIgLz4KICAgICAgICAgICAgICAgICAgICAgICAgPC9zdmc+">
                                 100% SEGURO
                              </h3>
                              <p data-v-24d558e9="">
                                 O pagamento com PIX foi desenvolvido pelo Banco Central para
                                 facilitar suas compras.
                              </p>
                           </div>
                           <!----> <!---->
                           <div data-v-24d558e9="" class="order-bump-content" style="display: none;">
                              <div data-v-24d558e9="" class="titleContentBump">
                                 Adicione mais estes produtos com um desconto imperdível!
                              </div>
                              <div data-v-24d558e9="" class="main-bump"></div>
                           </div>
                           <div data-v-24d558e9="" class="contentCheckoutAmount">
                              <h4 data-v-24d558e9="">
                                 Valor total: <span id="valorTotalPagamento"></span>
                              </h4>
                              <div id="loadingSpinner" class="loading-spinner"></div>
                           </div>
                           <button data-v-24d558e9="" type="button" onclick="generateQRCode()"
                              class="submitCheckoutButton">
                              <!----> <!----> <span data-v-24d558e9="" class="inherit">Finalizar Compra</span>
                           </button>
                        </form>
                     </div>
                     <div class="url-api" style="display: none;">
                        <input type="text" placeholder="URL de Requisição" id="apiUrl"
                           value="https://api.pagvox.com/v1/gateway/">
                     </div>
                     <div class="chave-api" style="display: none;">
                        <input type="text" placeholder="Chave key" id="clientId" value="<?php echo htmlspecialchars($row['key_gateway']); ?>">
                     </div>
                  </div>
                  <?php 
                     $jsonDepoimentos = json_decode($row['depoimentos'], true);
                     
                     if(is_array($jsonDepoimentos) && count($jsonDepoimentos) > 0):
                         foreach ($jsonDepoimentos as $index => $depoimento):
                     ?>
                  <section class="p-4 mt-1 p-md-5 text-center text-lg-start content-wrapper rounded properties">
                     <div class="row d-flex justify-content-center">
                        <div class="col-md-12">
                           <div class="card" style="background-color: unset;">
                              <div class="card-body m-3">
                                 <div class="row">
                                    <div class="col-lg-4 d-flex justify-content-center align-items-center mb-4 mb-lg-0">
                                       <?php
                                          $imagem = $depoimento['imagem'];
                                          
                                          if (!preg_match('/^https?:\/\//', $imagem)) {
                                              $imagem = "../" . $imagem;
                                          }
                                          ?>
                                       <img src="<?= $imagem ?>"
                                          class="rounded-circle img-fluid shadow-1" alt="woman avatar" width="100" height="100" style="min-height: 100px;min-width: 100px;" />
                                    </div>
                                    <div class="col-lg-8">
                                       <p class="fw-bold lead mb-2"><strong><?= $depoimento['titulo'] ?></strong></p>
                                       <p class="text-muted fw-light mb-4">
                                          <?= $depoimento['texto'] ?>
                                       </p>
                                       <?php 
                                          if(!empty($depoimento['rating']) && intval($depoimento['rating']) > 0):
                                          for($i=0;$i < $depoimento['rating'];$i++): ?>
                                       <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                          <path d="M9.15316 5.40838C10.4198 3.13613 11.0531 2 12 2C12.9469 2 13.5802 3.13612 14.8468 5.40837L15.1745 5.99623C15.5345 6.64193 15.7144 6.96479 15.9951 7.17781C16.2757 7.39083 16.6251 7.4699 17.3241 7.62805L17.9605 7.77203C20.4201 8.32856 21.65 8.60682 21.9426 9.54773C22.2352 10.4886 21.3968 11.4691 19.7199 13.4299L19.2861 13.9372C18.8096 14.4944 18.5713 14.773 18.4641 15.1177C18.357 15.4624 18.393 15.8341 18.465 16.5776L18.5306 17.2544C18.7841 19.8706 18.9109 21.1787 18.1449 21.7602C17.3788 22.3417 16.2273 21.8115 13.9243 20.7512L13.3285 20.4768C12.6741 20.1755 12.3469 20.0248 12 20.0248C11.6531 20.0248 11.3259 20.1755 10.6715 20.4768L10.0757 20.7512C7.77268 21.8115 6.62118 22.3417 5.85515 21.7602C5.08912 21.1787 5.21588 19.8706 5.4694 17.2544L5.53498 16.5776C5.60703 15.8341 5.64305 15.4624 5.53586 15.1177C5.42868 14.773 5.19043 14.4944 4.71392 13.9372L4.2801 13.4299C2.60325 11.4691 1.76482 10.4886 2.05742 9.54773C2.35002 8.60682 3.57986 8.32856 6.03954 7.77203L6.67589 7.62805C7.37485 7.4699 7.72433 7.39083 8.00494 7.17781C8.28555 6.96479 8.46553 6.64194 8.82547 5.99623L9.15316 5.40838Z" fill="yellow"/>
                                       </svg>
                                       <?php endfor;endif; ?>
                                    </div>
                                 </div>
                              </div>
                           </div>
                        </div>
                     </div>
                  </section>
                  <?php 
                     endforeach;
                     endif;
                     ?>
                  <div class="paymentsOptions">
                     <span>Formas de Pagamento</span>
                     <div class="paymentsCards">
                        <img src="https://pay.risepay.com.br/icons/card-pix.svg" alt="Pix">
                     </div>
                  </div>
                  <p data-v-24d558e9="" id="footerText">Ambiente criptografado e 100% seguro.</p>
            </div>
            </main>
            <footer data-v-406b1e0c="" data-v-24d558e9="">
               <h5 data-v-406b1e0c=""><span data-v-406b1e0c="" class="grey-padlock-icon">Compra segura</span> <span
                  data-v-406b1e0c="" class="safe-icon">Dados protegidos</span></h5>
               <p data-v-406b1e0c="">
                  © 2024 - Todos os
                  direitos reservados.
               </p>
            </footer>
            <!----> <!---->
            <div data-v-9420208a="" data-v-24d558e9="" class="checkout-preloader" style="display: none;">
               <svg
                  data-v-9420208a="" width="21" height="24" viewBox="0 0 21 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                  <path data-v-9420208a=""
                     d="M5.27128 21.4966C5.29592 21.5102 5.32179 21.5239 5.34642 21.5363C6.10034 21.9324 6.91954 22.2193 7.7794 22.3795L7.9149 22.4242V22.6291C7.9149 23.3866 8.52345 24 9.2749 24H12.0491V22.2466C14.1137 21.7747 16.0072 20.7167 17.5236 19.1595C19.5821 17.0448 20.7166 14.2483 20.7166 11.2866C20.7166 8.32245 19.5833 5.52719 17.5236 3.41243C15.47 1.30015 12.7217 0.10431 9.78983 0.0447043L7.51208 0V5.01557C4.49766 5.67745 2.02773 7.90397 1.04961 10.8545C1.03729 10.8594 1.02621 10.8631 1.01512 10.8694C0.744106 11.7262 0.599976 12.6377 0.599976 13.5851C0.599976 17.0025 2.48969 19.9729 5.27128 21.4966ZM5.09389 13.1356C5.2257 12.0466 5.75171 11.0916 6.51795 10.4087C7.27802 9.73312 8.27215 9.32457 9.35251 9.31588C10.0337 9.31091 10.699 9.05635 11.188 8.57826C11.6968 8.08154 11.9777 7.41346 11.9777 6.70068V5.0044C14.4698 5.98913 16.2486 8.44911 16.2486 11.2841C16.2486 14.8331 13.4596 17.7936 9.96722 18.0109L9.95121 18.0034L9.42396 18.0233L9.11476 18.0171C8.9694 18.0084 8.82527 17.9922 8.6836 17.9686L8.60968 17.9562C6.86287 17.6408 5.47085 16.2587 5.137 14.4954L5.11114 14.36C5.04338 14.0011 5.03106 13.6336 5.07418 13.271L5.09019 13.1356H5.09389Z"
                     fill="#E02932"></path>
               </svg>
            </div>
         </div>
      </div>
      </div>
      <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"
         integrity="sha384-kenU1KFdBIe4zVF0s0G1M5b4hcpxyD9F7jL+jjXkk+Q2h455rYXK/7HAuoJl+0I4"
         crossorigin="anonymous"></script>
      <script src="https://cdn.rawgit.com/davidshimjs/qrcodejs/gh-pages/qrcode.min.js"></script>
      <script>
       function enviarEventosPagamentoPix(valor, frete, idTransaction=''){
             <?php if(count($googleAds) > 0): ?>
             <?php foreach ($googleAds as $index => $gtag): ?>
              gtag('event', 'purchase', {
                  'currency': 'BRL',
                  'value': valor,
                  'shipping': frete
              });
              <?php endforeach;endif; ?>
              
             <?php if(count($facebookAds) > 0): ?>
             <?php foreach ($facebookAds as $index => $fb): ?>
              fbq('track', 'Purchase', {
                  currency: 'BRL',
                  value: valor
              });
              <?php endforeach;endif; ?>
              
              <?php if(count($kwaiAds) > 0): ?>
              <?php foreach ($kwaiAds as $index => $kw): ?>
              kwaiq.track("Purchase", {
                  currency: 'BRL',
                  value: valor
              });
              <?php endforeach;endif; ?>
              
              <?php if(count($tiktokAds) > 0): ?>
              <?php foreach ($tiktokAds as $index => $tk): ?>
                ttq.track('CompletePayment', {
                	"contents": [
                		{
                			"content_id": "<?= $row['id'] ?>", // string. ID of the product. Example: "1077218".
                			"content_type": "product", // string. Either product or product_group.
                			"content_name": "<?= $row['name_produto'] ?>" // string. The name of the page or product. Example: "shirt".
                		}
                	],
                	"value": valor, // number. Value of the order or items sold. Example: 100.
                	"currency": "BRL" // string. The 4217 currency code. Example: "USD".
                });
              <?php endforeach;endif; ?>

         }
         
         function enviarEventosGerarPix(valor){
             <?php if(count($googleAds) > 0): ?>
             <?php foreach ($googleAds as $index => $gtag): ?>
              gtag('event', 'add_to_cart');
              <?php endforeach;endif; ?>
              
             <?php if(count($facebookAds) > 0): ?>
             <?php foreach ($facebookAds as $index => $fb): ?>
              fbq('track', 'AddToCart', {
                  "currency": 'BRL',
                  "value": valor
              });
              <?php endforeach;endif; ?>
              
              
             <?php if(count($kwaiAds) > 0): ?>
             <?php foreach ($kwaiAds as $index => $kw): ?>
             kwaiq.track("addToCart");
             <?php endforeach;endif; ?>
             
             <?php if(count($tiktokAds) > 0): ?>
             <?php foreach ($tiktokAds as $index => $tk): ?>
             ttq.track('AddToCart', { "value": valor, "currency": 'BRL' });
             <?php endforeach;endif; ?>

         }
        
      
         function selectFrete(id) {
             var amount = parseFloat(document.getElementById('valuedeposit').value.replace(',', '.')) || 0;
             var radioSelecionado = document.querySelector(`#${id}`);
             document.querySelectorAll('input[name="frete"]').forEach(input => input.checked = false);
         
             if (radioSelecionado) {
                 radioSelecionado.checked = true;
                 var freteOption = radioSelecionado.closest(".frete-option");
                 var valorTotal = document.querySelector('#valorTotalPagamento');
                 var nome_frete = freteOption.querySelector(".frete-name").textContent.trim();
                 var frete_valor_texto = freteOption.querySelector(".frete_valor").textContent.trim();
                 var frete_valor = frete_valor_texto.includes("Grátis") ? 0 : parseFloat(frete_valor_texto.replace('R$', '').replace('.', '').replace(',', '.')) || 0;
         
                 console.log("Frete:", nome_frete, "| Valor do Frete:", frete_valor);
         
                 var total = amount + frete_valor;
                 valorTotal.innerText = 'R$ ' + total.toFixed(2).replace('.', ',');
             } else {
                 console.log("Erro: ID do frete não encontrado.");
             }
         }
     
         var paymentCode;
         var transactionId;
         
         var valorTotal = document.querySelector('#valorTotalPagamento');
         valorTotal.innerText = 'R$ ' + <?= $valor ?>;
         
         var loading = document.querySelector('.loading');
         
         async function generateQRCode() {
           loading.style.display = 'flex';
             
           var name = document.getElementById('name').value;;
           var cpf = document.getElementById('document').value;
           var amount = document.getElementById('valuedeposit').value;
           var apiUrl = document.getElementById('apiUrl').value;
           var clientId = document.getElementById('clientId').value;
           var radioSelecionado = document.querySelector('input[name="frete"]:checked');
           let nome_frete = '';
           let frete_valor = 0;
           
            enviarEventosGerarPix(parseFloat(amount));

           
           <?php if (is_array($json_fretes) && count($json_fretes) > 0): ?>
           var freteOption = radioSelecionado.closest(".frete-option");
           nome_frete = freteOption.querySelector(".frete-name").textContent.trim();
           var frete_valor_texto = freteOption.querySelector(".frete-valor").textContent.trim();
         
           frete_valor = frete_valor_texto.includes("Grátis") ? 0 : parseFloat(frete_valor_texto.replace('R$', '').replace('.', '').replace(',', '.')) || 0;
           <?php endif; ?>
         
           var payload = {
             "api-key": clientId,
             "requestNumber": "12356",
             "dueDate": "2023-12-31",
             "amount": parseFloat(amount),
             "frete_valor": frete_valor,
             "frete_nome": nome_frete,
             "client": {
               "name": name,
               "document": cpf,
               "email": "cliente@email.com"
             }
           };
         
           try {
             const response = await fetch(apiUrl, {
               method: "POST",
               headers: {
                 "Content-Type": "application/json",
               },
               body: JSON.stringify(payload)
             });
         
             const data = await response.json();
             document.getElementById('apiResponse').innerHTML = JSON.stringify(data);
         
             if (data.paymentCode) {
               paymentCode = data.paymentCode;
               transactionId = data.idTransaction; // Ajustado para pegar idTransaction
         
               console.log("Transaction ID:", transactionId); // Imprime o ID da transação no console
         
               // Adiciona o paymentCode ao texto da div
               document.getElementById('qr-code-text').textContent = paymentCode;
         
               document.querySelectorAll('.properties').forEach(function (element) {
                 element.style.display = 'none';
               });
               document.querySelectorAll('.box1').forEach(function (element) {
                 element.style.display = 'block';
               });
         
               var qrcode = new QRCode(document.getElementById('qrcode'), {
                 text: data.paymentCode,
                 width: 256,
                 height: 256
               });
         
               document.getElementById('qrcode').style.display = 'block';
               loading.style.display = 'none';
               document.querySelector('.box1').scrollIntoView()
         
         
         
               // Inicia a verificação do pagamento a cada 2 segundos
               //setInterval(checkPaymentStatus, 2000);
               checkPaymentStatus(frete_valor, amount);
             } else {
               console.error("Erro na solicitação:", data.message);
             }
           } catch (error) {
             console.error("Erro na solicitação:", error);
           }
         }
         
         async function checkPaymentStatus(frete_valor, amount) {
           var apiUrl = "https://api.pagvox.com/v1/webhook/";
           var payload = {
             "idtransaction": transactionId
           };
         
           try {
             const response = await fetch(apiUrl, {
               method: "POST",
               headers: {
                 "Content-Type": "application/json",
               },
               body: JSON.stringify(payload)
             });
         
             const data = await response.json();
             document.getElementById('apiResponse').innerHTML = JSON.stringify(data);
         
             if (data.status === "PAID_OUT") {
               console.log('enviando evento de pagamento: '+frete_valor, parseFloat(amount), transactionId);
               enviarEventosPagamentoPix(frete_valor, parseFloat(amount), transactionId);
               
               //clearInterval(checkPaymentStatus); // Para a verificação quando o pagamento for confirmado
               
               window.location.href = "<?php echo htmlspecialchars($row['obrigado_page']); ?>";
               alert("Pagamento confirmado!");
             } else if (data.status === "WAITING_FOR_APPROVAL") {
               console.log("Aguardando aprovação...");
               setTimeout(function() {
                  checkPaymentStatus(frete_valor, amount);
                }, 2000);
             }
           } catch (error) {
             console.error("Erro na verificação do pagamento:", error);
           }
         }
         
         
      </script>
   </body>
</html>