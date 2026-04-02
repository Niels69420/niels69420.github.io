import { players, enemy } from './characters.js';
import { logMessage, endGameEnemy } from './log.js';
import { turn, setTurn } from './state.js';
import { redrawEnemySprite } from './characters.js';






export let turnCount = 0; 

export function nextTurn() {
    setTurn((turn + 1) % 4);
    if (turn === 0) {
        turnCount++; 
        document.getElementById("turn-counter").textContent = turnCount; 
    }
    if (turn === 0 && players[0].health > 0) {
        players[0].attackButton.disabled = false;
        players[0].movesButton.disabled = false;
        players[0].whirlpoolButton.disabled = players[0].mana < 4;
        players[0].comboButton.disabled = players[0].mana < 5;
        players[0].itemButton.disabled = false;
        players[0].defendButton.disabled = false;
    } else if (turn === 1) {
        if (enemy.isStunned) {
            logMessage(`Enemy is stunned and skips their turn!`);
            enemy.stunnedTurns -= 1; 
            if (enemy.stunnedTurns <= 0) {
                enemy.isStunned = false; 
            }
            nextTurn(); 
        } else {
            enemyTurn(players[0]); 
        }
    } else if (turn === 2 && players[1].health > 0) {
        players[1].attackButton.disabled = false;
        players[1].movesButton.disabled = false;
        players[1].whirlpoolButton.disabled = players[1].mana < 4;
        players[1].comboButton.disabled = players[1].mana < 5;
        players[1].itemButton.disabled = false;
        players[1].defendButton.disabled = false;
    } else if (turn === 3) {
        if (enemy.isStunned) {
            logMessage(`Enemy is stunned and skips their turn!`);
            enemy.stunnedTurns -= 1; 
            if (enemy.stunnedTurns <= 0) {
                enemy.isStunned = false;
            }
            nextTurn(); 
        } else {
            enemyTurn(players[1]); 
        }
    } else {
        nextTurn(); 
    }
}

export function enemyTurn(targetPlayer) {
    setTimeout(() => {
        let baseDamage;
        if (enemy.name == "Swordfish") {
            baseDamage = Math.floor(Math.random() * 100) + enemy.attack;
        } else if (enemy.name == "Crab" && enemy.health <= 125) {
            baseDamage = Math.floor(Math.random() * 10) + enemy.attack * 2;
            
            if (!enemy.crabGoldModeActivated) {
                redrawEnemySprite("sprites/goldencrab.jpeg");
                logMessage(`Crab has turned golden!`);
                logMessage(`Crab's damage has increased!`);
                enemy.crabGoldModeActivated = true;
            }
        } else {
            baseDamage = Math.floor(Math.random() * 10) + enemy.attack;
        }  
        const damage = Math.ceil(baseDamage * (1 - targetPlayer.tempDefense)); 
        targetPlayer.health -= damage;
        
        targetPlayer.health = Math.max(targetPlayer.health, 0);
        targetPlayer.healthElement.textContent = targetPlayer.health;

        logMessage(`${enemy.name} attacks ${targetPlayer.name} and deals ${damage} damage!`);

        if (targetPlayer.health <= 0) {
            logMessage(`${targetPlayer.name} has fainted!`);
            targetPlayer.hasFainted = true;
        }

        if (players.every(player => player.health <= 0)) {
            endGameEnemy();
        } else {
            nextTurn();
        }

        targetPlayer.tempDefense = 0; 
    }, 1000);
}
