import { players, setEnemy, enemy } from './characters.js';
import { handleWhirlpool, handleCombo, handleItem} from './attacks.js';
import { logMessage, endGame } from './log.js';
import { nextTurn } from './turns.js';
import { setTurn } from './state.js';
import { updateLowestTurns } from './log.js';


const canvas = document.getElementById('game-canvas');
const ctx = canvas.getContext('2d');

updateLowestTurns();

document.getElementById("start-game").addEventListener("click", () => {
    const selectedEnemy = document.getElementById("enemy-select").value;
    setEnemy(selectedEnemy);
    document.getElementById("enemy-health").textContent = enemy.health;
    document.getElementById("character-select").classList.add("hidden"); 
    document.getElementById("start-game").disabled = true; 
    startGame();
});


function startGame() {
    players.forEach(player => {
        player.attackButton.addEventListener("click", () => {
            let damage = player.attack();
            if (enemy.isStunned) {
                damage = Math.floor(damage * 1.35); 
            }
            enemy.health -= damage;
            enemy.health = Math.max(enemy.health, 0);
            enemy.healthElement.textContent = enemy.health;
            
            logMessage(`${player.name} attacks and deals ${damage} damage!`);
            player.attackButton.disabled = true;
            player.movesButton.disabled = true;
            player.itemButton.disabled = true;
            player.defendButton.disabled = true;

            if (enemy.health <= 0) {
                endGame("Players");
            } else {
                nextTurn();
            }
        });

        player.movesButton.addEventListener("click", () => {
            player.whirlpoolButton.classList.toggle("hidden");
            player.comboButton.classList.toggle("hidden");
            
            player.whirlpoolButton.disabled = player.mana < 4;
            player.comboButton.disabled = player.mana < 5;
            player.attackButton.disabled = !player.attackButton.disabled;
            player.defendButton.disabled = !player.defendButton.disabled;
            player.itemButton.disabled = !player.itemButton.disabled;
        });

        player.whirlpoolButton.addEventListener("click", () => {
            handleWhirlpool(player);
        });

        player.comboButton.addEventListener("click", () => {
            handleCombo(player);
        });
        player.itemButton.addEventListener("click", () => {
            handleItem(player);
        });

        player.defendButton.addEventListener("click", () => {
            player.defend();
            
            logMessage(`${player.name} raises their defense and gains 3 mana!`);
            player.attackButton.disabled = true;
            player.movesButton.disabled = true;
            player.whirlpoolButton.disabled = true;
            player.comboButton.disabled = true;
            player.defendButton.disabled = true;
            player.itemButton.disabled = true;
            nextTurn();
        });
    });

    setTurn(3); 
    nextTurn(); 
    drawCharacters();
     
}


function drawCharacters() {
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
    enemyImage.src = enemy.sprite;
    enemyImage.onload = () => {
        if (enemy.sprite === 'sprites/SHARK.jpeg') {
            ctx.save();
            ctx.scale(-1, 1);
            ctx.drawImage(enemyImage, -1225, 170, 225, 225);
            ctx.restore();
        } else if (enemy.sprite === 'sprites/SWORDFISH.jpeg') {
            ctx.drawImage(enemyImage, 1000, 210, 225, 100); 
        } else {
            ctx.drawImage(enemyImage, 1000, 170, 225, 225);
        }
    };
    
}
