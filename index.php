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
      background: #f5f5f5;
      color: #333;
      font-family: sans-serif;
    }

    #wrapper {
      display: flex;
      align-items: center;
      height: 100%;
    }

    #game-wrapper {
      position: relative;
      width: 1080px;
      height: 1920px;
      max-height: 90vh;
      max-width: calc(90vh * (1080 / 1920));
      border-radius: 12px;
      overflow: hidden;
      background-color: #cfe8cf;
    }

    #game-container {
      width: 100%;
      height: 100%;
    }

    #overlays {
      position: absolute;
      top: 0;
      left: 0;
      width: 100%;
      height: 100%;
      pointer-events: none;
      font-size: 12px;
    }

    #controls {
      display: flex;
      flex-direction: column;
      margin-left: 15px;
    }

    button {
      margin: 5px 0;
      padding: 10px 15px;
      font-size: 20px;
      border-radius: 8px;
      border: none;
      background-color: #f9a825;
      color: white;
      cursor: pointer;
    }

    .panel {
      position: absolute;
      background: rgba(255,255,255,0.8);
      padding: 4px 8px;
      border-radius: 4px;
      width: 210px;
      font-size: 20px;
      line-height: 1.2;
    }

    .bar {
      width: 180px;
      height: 15px;
      background: #ddd;
      margin-top: 6px;
    }

    .bar div {
      height: 100%;
    }

    .bar.hunger div {
      background: #76c043;
    }

    .bar.love div {
      background: #e91e63;
    }

    .bar.anger div {
      background: #ff5722;
    }
  </style>
  <script src="https://cdn.jsdelivr.net/npm/phaser@3/dist/phaser.js"></script>
  <script>
    window.addEventListener('load', () => {
      const config = {
        type: Phaser.AUTO,
        width: 1080,
        height: 1920,
        parent: 'game-container',
        backgroundColor: '#cfe8cf',
        scale: {
          mode: Phaser.Scale.FIT,
          autoCenter: Phaser.Scale.CENTER_BOTH
        },
        scene: { preload, create, update }
      };

      const game = new Phaser.Game(config);
      let scene;
      const FLOWER_SIZE = 144;
      const CAKE_SIZE = 80;
      const PANEL_OFFSET = FLOWER_SIZE + 4;
      const BAR_WIDTH = 180;
      const HUNGER_FACTOR = BAR_WIDTH / 100;
      const flowers = [];
      const overlay = document.getElementById('overlays');
      overlay.style.transformOrigin = 'top left';
      resize();
      const names = ['Alice','Bob','Chlo\u00e9','Damien','Emma','Felix','Gaston','H\u00e9l\u00e8ne','Iris','Julien','Karim','Laura','M\u00e9lanie','Nina','Oscar','Paul','Quentin','Rita','Sophie','Tom','Ulysse','Val\u00e9rie','William','Xavier','Yasmine','Zo\u00e9'];
      const cakes = [];

      function randomName(){
        return names[Math.floor(Math.random()*names.length)];
      }

      function resize(){
        const ratio = 1080/1920;
        const h = window.innerHeight * 0.9;
        const w = h * ratio;
        const wrapper = document.getElementById('game-wrapper');
        wrapper.style.height = h + 'px';
        wrapper.style.width = w + 'px';
        overlay.style.transform = 'scale(' + (h/1920) + ')';
      }
      window.addEventListener('resize', resize);

      function randomPos(size){
        return {
          x: Math.random() * (game.config.width - size),
          y: Math.random() * (game.config.height - size)
        };
      }

      function createFlower(){
        const size = FLOWER_SIZE;
        const pos = randomPos(size);
        const el = scene.add.text(pos.x, pos.y, '🌹', {fontSize: size + 'px'});
        el.setOrigin(0);
        const panel = document.createElement('div');
        panel.className = 'panel';
        const name = randomName();
        const love = Math.random();
        const anger = Math.random();
        panel.innerHTML = `${name}
          <div class="bar love"><div style="width:${love*BAR_WIDTH}px;background:#e91e63"></div></div>
          <div class="bar anger"><div style="width:${anger*BAR_WIDTH}px;background:#ff5722"></div></div>
          <div class="bar hunger"><div></div></div>`;
        overlay.appendChild(panel);

        const flower = {
          obj: el,
          panel: panel,
          x: pos.x,
          y: pos.y,
          dx: (Math.random()*2-1)*2,
          dy: (Math.random()*2-1)*2,
          hunger: 100,
          anger: anger,
          love: love,
          cooldown: 0,
          pause: 0
        };
        flowers.push(flower);
        updateBars(flower);
      }

      function spawnCake(){
        const size = CAKE_SIZE;
        const pos = randomPos(size);
        const el = scene.add.text(pos.x, pos.y, '🍰', {fontSize: size + 'px'});
        el.setOrigin(0);
        cakes.push({obj: el, x: pos.x, y: pos.y});
      }

      function updateBars(f){
        const loveBar = f.panel.querySelector('.bar.love div');
        if(loveBar) loveBar.style.width = `${f.love * BAR_WIDTH}px`;
        const angerBar = f.panel.querySelector('.bar.anger div');
        if(angerBar) angerBar.style.width = `${f.anger * BAR_WIDTH}px`;
        const hungerBar = f.panel.querySelector('.bar.hunger div');
        if(hungerBar) hungerBar.style.width = `${f.hunger * HUNGER_FACTOR}px`;
      }

      function showEffect(x, y, emoji){
        const txt = scene.add.text(x, y, emoji, {fontSize: '64px'});
        txt.setOrigin(0.5);
        scene.tweens.add({
          targets: txt,
          alpha: 0,
          duration: 1000,
          onComplete: () => txt.destroy()
        });
      }

      function handleCollision(f1,f2){
        if(f1.cooldown>0 || f2.cooldown>0) return;
        const midX = (f1.x+f2.x)/2;
        const midY = (f1.y+f2.y)/2 - 20;
        const love = f1.love+f2.love >= f1.anger+f2.anger;
        const emoji = love ? '❤️' : '⚡';
        showEffect(midX, midY, emoji);
        setTimeout(() => showEffect(midX, midY, emoji), 300);
        if(love){
          f1.love = Math.min(1, f1.love + 0.05);
          f2.love = Math.min(1, f2.love + 0.05);
        }else{
          f1.anger = Math.min(1, f1.anger + 0.05);
          f2.anger = Math.min(1, f2.anger + 0.05);
        }
        updateBars(f1);
        updateBars(f2);
        const dx = f1.x - f2.x;
        const dy = f1.y - f2.y;
        const dist = Math.sqrt(dx*dx + dy*dy) || 1;
        f1.dx = (dx/dist)*2;
        f1.dy = (dy/dist)*2;
        f2.dx = (-dx/dist)*2;
        f2.dy = (-dy/dist)*2;
        f1.pause = f2.pause = 20;
        f1.cooldown = f2.cooldown = 40;
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
          if(f.cooldown>0) f.cooldown--;
          if(f.pause>0){
            f.pause--;
          }
          f.hunger = Math.max(0, f.hunger - 0.05);

          let moveX = f.pause>0 ? 0 : f.dx;
          let moveY = f.pause>0 ? 0 : f.dy;

          let nearCake = null;
          let nearDist = Infinity;
          cakes.forEach(c => {
            const d = distance(f, c);
            if(d < nearDist){ nearDist = d; nearCake = c; }
          });

          if(nearCake && nearDist < 150 && f.pause===0){
            const dx = nearCake.x - f.x; const dy = nearCake.y - f.y;
            moveX = (dx/nearDist)*2; moveY = (dy/nearDist)*2;
            f.dx = moveX; f.dy = moveY;
            if(nearDist < 30){
              f.hunger = Math.min(100, f.hunger + 50);
              nearCake.obj.destroy();
              cakes.splice(cakes.indexOf(nearCake),1);
              nearCake = null;
            }
          }

          if(f.hunger <= 0 && !(nearCake && nearDist < 150)){
            moveX = 0; moveY = 0;
          }

          f.x += moveX; f.y += moveY;
          if(f.x<0||f.x>w-FLOWER_SIZE) f.dx=-f.dx;
          if(f.y<0||f.y>h-FLOWER_SIZE) f.dy=-f.dy;
          f.x=Math.max(0,Math.min(w-FLOWER_SIZE,f.x));
          f.y=Math.max(0,Math.min(h-FLOWER_SIZE,f.y));
          f.obj.setPosition(f.x, f.y);
          f.panel.style.transform = `translate(${f.x + PANEL_OFFSET}px, ${f.y}px)`;
          updateBars(f);
        });

        for(let i=0;i<flowers.length;i++){
          for(let j=i+1;j<flowers.length;j++){
            if(distance(flowers[i],flowers[j])<FLOWER_SIZE){
              handleCollision(flowers[i],flowers[j]);
            }
          }
        }
      }
    });
  </script>
</head>
<body>
  <div id="wrapper">
    <div id="game-wrapper">
      <div id="game-container"></div>
      <div id="overlays"></div>
    </div>
    <div id="controls">
      <button id="addFlower">Ajouter une rose</button>
      <button id="addCake">Gâteau</button>
    </div>
  </div>
</body>
</html>
