<?php 
      $googleAds = json_decode($social['google_ads'], true);
      if(count($googleAds) > 0):
      foreach ($googleAds as $index => $gtag):
      if($index == 0): 
      ?>
      <!-- Google tag (gtag.js) -->
      <script async src="https://www.googletagmanager.com/gtag/js?id=<?= $gtag['id'] ?>"></script>
      <script>
        window.dataLayer = window.dataLayer || [];
        function gtag(){dataLayer.push(arguments);}
        gtag('js', new Date());
      <?php endif; ?>
      
      gtag('config', '<?= $gtag['id'] ?>', { name: 'tracker<?= $index ?>' });
      gtag('event', 'page_view', {
          'send_to': 'tracker<?= $index ?>',
          'event_category': 'page'
      });
      <?php endforeach; ?>
      </script>
      <?php endif; ?>