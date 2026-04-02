export const players = [
    {
        name: "Fish",
        health: 125,
        maxHealth: 125,
        mana: 10,
        maxMana: 10,
        healthElement: document.getElementById("player1-health"),
        manaElement: document.getElementById("player1-mana"),
        attackButton: document.getElementById("attack-button-player1"),
        movesButton: document.getElementById("Move-selector1"),
        whirlpoolButton: document.getElementById("whirlpool1"),
        comboButton: document.getElementById("combo1"),
        itemButton: document.getElementById("items1"),
        defendButton: document.getElementById("defend-button-player1"),
        hasFainted: false,
        sprite: document.getElementById("fish-sprite"), 
        tempDefense: 0,
        attack: function() {
            return Math.floor(Math.random() * 10) + 10; 
        },
        whirlpool: function() {
            return Math.floor(Math.random() * 10) + 18; 
        },
        combo: function() {
            return Math.floor(Math.random() * 10) + 25; 
        },
        defend: function() {
            this.tempDefense = 0.5; 
            this.mana += 3; 
            this.mana = Math.min(this.mana, 10); 
            this.manaElement.textContent = this.mana;
        }
    },
    {
        name: "Salmon",
        health: 150,
        maxHealth: 150,
        mana: 12,
        maxMana: 12,
        healthElement: document.getElementById("player2-health"),
        manaElement: document.getElementById("player2-mana"),
        attackButton: document.getElementById("attack-button-player2"),
        movesButton: document.getElementById("Move-selector2"),
        whirlpoolButton: document.getElementById("whirlpool2"),
        comboButton: document.getElementById("combo2"),
        itemButton: document.getElementById("items2"),
        defendButton: document.getElementById("defend-button-player2"),
        hasFainted: false,
        sprite: document.getElementById("salmon-sprite"),
        tempDefense: 0,
        attack: function() {
            return Math.floor(Math.random() * 10) + 10;
        },
        whirlpool: function() {
            return Math.floor(Math.random() * 10) + 18; 
        },
        combo: function() {
            return Math.floor(Math.random() * 10) + 25;         },
        defend: function() {
            this.tempDefense = 0.5; 
            this.mana += 3; 
            this.mana = Math.min(this.mana, 12); 
            this.manaElement.textContent = this.mana;
        }
    }
];

export const enemies = {
    enemy1: {
        name: "Shark",
        health: 250,
        attack: 20, 
        healthElement: document.getElementById("enemy-health"),
        isStunned: false,
        stunnedTurns: 0,
        sprite: "sprites/SHARK.jpeg",
    },
    enemy2: {
        name: "Whale",
        health: 350,
        attack: 15,
        healthElement: document.getElementById("enemy-health"),
        isStunned: false,
        stunnedTurns: 0,
        sprite: "sprites/WHALE.jpeg",
    },
    enemy3: {
        name: "Swordfish",
        health: 200,
        attack: 1, 
        healthElement: document.getElementById("enemy-health"),
        isStunned: false,
        stunnedTurns: 0,
        sprite: "sprites/SWORDFISH.jpeg",
    },
    enemy4: {
        name: "Crab",
        health: 250,
        attack: 15,
        healthElement: document.getElementById("enemy-health"),
        isStunned: false,
        stunnedTurns: 0,
        sprite: "sprites/CRAB.jpeg",
        crabGoldModeActivated: false,
       
    },
};

export let enemy;

export function setEnemy(enemyName) {
    enemy = enemies[enemyName];
    
    
}

export function redrawEnemySprite() {
    const canvas = document.getElementById('game-canvas');
    const ctx = canvas.getContext('2d');
    ctx.clearRect(0, 0, canvas.width, canvas.height);

    const player1Image = new Image();
    player1Image.src = 'sprites/FISH.jpeg';
    player1Image.onload = () => {
        ctx.drawImage(player1Image, 150, 100, 125, 125); 
    };

    const player2Image = new Image();
    player2Image.src = 'sprites/salmon.jpeg';
    player2Image.onload = () => {
        ctx.drawImage(player2Image, 150, 290, 130, 100); 
    };

    const enemyImage = new Image();
    if (enemy.name === "Crab" && enemy.health <= 125) {
        enemy.sprite = 'sprites/goldencrab.jpeg';
       
    }
    enemyImage.src = enemy.sprite;
    enemyImage.onload = () => {
        if (enemy.sprite === 'sprites/SHARK.jpeg') {
            ctx.save();
            ctx.scale(-1, 1);
            ctx.drawImage(enemyImage, -1225, 170, 225, 225);
            ctx.restore();
        } else if (enemy.sprite === 'sprites/SWORDFISH.jpeg') {
            ctx.drawImage(enemyImage, 1000, 210, 225, 100); 
        } else if (enemy.sprite === 'sprites/goldencrab.jpeg'){
            ctx.drawImage(enemyImage, 1025, 200, 200, 150);
        }
        else {
            ctx.drawImage(enemyImage, 1000, 170, 225, 225);
        }
        
    };
}