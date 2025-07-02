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
      flex-direction: column;
      justify-content: center;
      align-items: center;
      background: #222;
      color: #fff;
      font-family: sans-serif;
    }
    #controls {
      margin-bottom: 10px;
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
      user-select: none;
    }
    .cake {
      position: absolute;
      font-size: 40px;
      pointer-events: none;
    }
    .effect {
      position: absolute;
      font-size: 32px;
      pointer-events: none;
      animation: fade 1s forwards;
    }
    @keyframes fade {
      from { opacity: 1; }
      to { opacity: 0; }
    }
  </style>
  <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
  <script>
    $(function(){
      const container = $('#container');
      const flowers = [];
      let cake = null;

      function randomPos(size){
        return {
          x: Math.random() * (container.width() - size),
          y: Math.random() * (container.height() - size)
        };
      }

      function createFlower(){
        const el = $('<div class="flower">\ud83c\udf39<\/div>');
        const pos = randomPos(48);
        el.css({left: pos.x, top: pos.y});
        container.append(el);
        const flower = {
          el: el,
          x: pos.x,
          y: pos.y,
          dx: (Math.random()*2-1)*2,
          dy: (Math.random()*2-1)*2,
          hunger: 0,
          anger: Math.random(),
          love: Math.random(),
          pause: 0
        };
        flowers.push(flower);
      }

      function spawnCake(){
        if(cake){ cake.el.remove(); }
        const el = $('<div class="cake">\ud83c\udf70<\/div>');
        const pos = randomPos(40);
        el.css({left: pos.x, top: pos.y});
        container.append(el);
        cake = {el: el, x: pos.x, y: pos.y};
      }

      function showEffect(x,y,emoji){
        const el = $('<div class="effect">'+emoji+'<\/div>');
        el.css({left:x, top:y});
        container.append(el);
        setTimeout(()=>el.remove(),1000);
      }

      function handleCollision(f1,f2){
        if(f1.pause>0 || f2.pause>0) return;
        const midX = (f1.x+f2.x)/2;
        const midY = (f1.y+f2.y)/2 - 20;
        if(f1.love+f2.love >= f1.anger+f2.anger){
          showEffect(midX,midY,'\u2764\ufe0f');
        }else{
          showEffect(midX,midY,'\u26a1');
        }
        f1.pause = f2.pause = 20; // ~1s
        f1.dx = -f1.dx; f1.dy = -f1.dy;
        f2.dx = -f2.dx; f2.dy = -f2.dy;
      }

      function distance(a,b){
        const dx=a.x-b.x; const dy=a.y-b.y; return Math.sqrt(dx*dx+dy*dy);
      }

      function move(){
        flowers.forEach(f=>{
          if(f.pause>0){ f.pause--; return; }
          if(cake){
            const dx=cake.x-f.x; const dy=cake.y-f.y;
            const d=Math.sqrt(dx*dx+dy*dy);
            f.dx = (dx/d)*2; f.dy=(dy/d)*2;
            if(d<30){
              f.hunger += 1;
              cake.el.remove();
              cake=null;
            }
          }
          f.x += f.dx; f.y += f.dy;
          if(f.x<0||f.x>container.width()-48) f.dx=-f.dx;
          if(f.y<0||f.y>container.height()-48) f.dy=-f.dy;
          f.x=Math.max(0,Math.min(container.width()-48,f.x));
          f.y=Math.max(0,Math.min(container.height()-48,f.y));
          f.el.css({left:f.x, top:f.y});
        });

        for(let i=0;i<flowers.length;i++){
          for(let j=i+1;j<flowers.length;j++){
            if(distance(flowers[i],flowers[j]) < 48){
              handleCollision(flowers[i],flowers[j]);
            }
          }
        }
      }

      $('#addFlower').on('click', createFlower);
      $('#addCake').on('click', spawnCake);

      createFlower();
      createFlower();
      setInterval(move,50);
    });
  </script>
</head>
<body>
  <div id="controls">
    <button id="addFlower">Ajouter une rose</button>
    <button id="addCake">Gâteau</button>
  </div>
  <div id="container"></div>
</body>
</html>
