<?php
// Enregistre ce fichier avec l'extension .php et ouvre-le dans un navigateur via un serveur local.
?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <title>Fleur Animée</title>
  <style>
    body {
      margin: 0;
      height: 100vh;
      display: flex;
      justify-content: center;
      align-items: center;
      background: #222;
    }
    #container {
      width: 700px;
      height: 700px;
      background-color: #005500;
      position: relative;
      overflow: hidden;
      border-radius: 12px;
    }
    .flower {
      position: absolute;
      font-size: 48px;
      transition: top 0.6s ease, left 0.6s ease;
    }
  </style>
  <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
  <script>
    $(document).ready(function(){
      function moveFlower(flower) {
        const container = $('#container');

        const maxX = container.width() - flower.outerWidth();
        const maxY = container.height() - flower.outerHeight();

        const newX = Math.random() * maxX;
        const newY = Math.random() * maxY;

        flower.css({
          left: newX + 'px',
          top: newY + 'px'
        });
      }

      // Place les fleurs au centre au début
      const flowers = $('.flower');
      flowers.each(function(){
        const f = $(this);
        f.css({
          left: ($('#container').width() - f.outerWidth()) / 2 + 'px',
          top: ($('#container').height() - f.outerHeight()) / 2 + 'px'
        });
      });

      // Déplace les fleurs toutes les 1.5 secondes
      setInterval(function(){ moveFlower($('#flower1')); }, 1500);
      setInterval(function(){ moveFlower($('#flower2')); }, 1500);
    });
  </script>
</head>
<body>
  <div id="container">
    <div id="flower1" class="flower">🌹</div>
    <div id="flower2" class="flower">🌹</div>
  </div>
</body>
</html>
