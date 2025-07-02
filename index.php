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
      background: #f5f5f5;
      color: #333;
      font-family: sans-serif;
    }
    #game-container {
      width: 90vw;
      aspect-ratio: 9 / 16;
      max-height: 90vh;
      background-color: #cfe8cf;
      position: relative;
      border-radius: 12px;
      overflow: hidden;
    }
    #controls {
      margin-top: 10px;
    }
    button {
      margin: 0 5px;
      padding: 6px 10px;
      font-size: 16px;
      border-radius: 6px;
      border: none;
      background-color: #e0e0e0;
    }
  </style>
  <script src="https://cdn.jsdelivr.net/npm/phaser@3/dist/phaser.js"></script>
  <script>
    window.addEventListener('load', () => {
      const container = document.getElementById('game-container');
      const width = container.clientWidth;
      const height = container.clientHeight;

      const config = {
        type: Phaser.AUTO,
        width: width,
        height: height,
        parent: 'game-container',
        backgroundColor: '#cfe8cf',
        scene: { preload, create, update }
      };

      const game = new Phaser.Game(config);
      let scene;
      const flowers = [];
      let cake = null;

      function randomPos(size){
        return {
          x: Math.random() * (game.config.width - size),
          y: Math.random() * (game.config.height - size)
        };
      }

      function createFlower(){
        const size = 48;
        const pos = randomPos(size);
        const el = scene.add.text(pos.x, pos.y, '🌹', {fontSize: size + 'px'});
        el.setOrigin(0);
        const flower = {
          obj: el,
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
        if(cake){ cake.obj.destroy(); }
        const size = 40;
        const pos = randomPos(size);
        const el = scene.add.text(pos.x, pos.y, '🍰', {fontSize: size + 'px'});
        el.setOrigin(0);
        cake = {obj: el, x: pos.x, y: pos.y};
      }

      function showEffect(x, y, emoji){
        const txt = scene.add.text(x, y, emoji, {fontSize: '32px'});
        txt.setOrigin(0.5);
        scene.tweens.add({
          targets: txt,
          alpha: 0,
          duration: 1000,
          onComplete: () => txt.destroy()
        });
      }

      function handleCollision(f1,f2){
        if(f1.pause>0 || f2.pause>0) return;
        const midX = (f1.x+f2.x)/2;
        const midY = (f1.y+f2.y)/2 - 20;
        if(f1.love+f2.love >= f1.anger+f2.anger){
          showEffect(midX, midY, '❤️');
        }else{
          showEffect(midX, midY, '⚡');
        }
        f1.pause = f2.pause = 20;
        f1.dx = -f1.dx; f1.dy = -f1.dy;
        f2.dx = -f2.dx; f2.dy = -f2.dy;
      }

      function distance(a,b){
        const dx=a.x-b.x; const dy=a.y-b.y; return Math.sqrt(dx*dx+dy*dy);
      }

      function preload(){ }

      function create(){
        scene = this;
        createFlower();
        createFlower();
        document.getElementById('addFlower').addEventListener('click', createFlower);
        document.getElementById('addCake').addEventListener('click', spawnCake);
      }

      function update(){
        const w = game.config.width;
        const h = game.config.height;
        flowers.forEach(f => {
          if(f.pause>0){ f.pause--; return; }
          if(cake){
            const dx=cake.x-f.x; const dy=cake.y-f.y;
            const d=Math.sqrt(dx*dx+dy*dy);
            f.dx=(dx/d)*2; f.dy=(dy/d)*2;
            if(d<30){
              f.hunger+=1;
              cake.obj.destroy();
              cake=null;
            }
          }
          f.x += f.dx; f.y += f.dy;
          if(f.x<0||f.x>w-48) f.dx=-f.dx;
          if(f.y<0||f.y>h-48) f.dy=-f.dy;
          f.x=Math.max(0,Math.min(w-48,f.x));
          f.y=Math.max(0,Math.min(h-48,f.y));
          f.obj.setPosition(f.x, f.y);
        });

        for(let i=0;i<flowers.length;i++){
          for(let j=i+1;j<flowers.length;j++){
            if(distance(flowers[i],flowers[j])<48){
              handleCollision(flowers[i],flowers[j]);
            }
          }
        }
      }
    });
  </script>
</head>
<body>
  <div id="game-container"></div>
  <div id="controls">
    <button id="addFlower">Ajouter une rose</button>
    <button id="addCake">Gâteau</button>
  </div>
</body>
</html>
