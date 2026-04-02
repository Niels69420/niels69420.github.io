import { enemy } from './characters.js';
import { logMessage } from './log.js';
import { nextTurn } from './turns.js';
import { endGame } from './log.js';

let itemUses = 3;
export function manaWhirlpool(player) {
    player.mana -= 4;
    player.mana = Math.max(player.mana, 0);
    player.manaElement.textContent = player.mana;
}

export function manaCombo(player) {
    player.mana -= 5;
    player.mana = Math.max(player.mana, 0);
    player.manaElement.textContent = player.mana;
}


export function handleWhirlpool(player) {
    let damage = player.whirlpool();
    if (enemy.isStunned) {
        damage = Math.floor(damage * 1.35); 
    }
    enemy.health -= damage;
    enemy.health = Math.max(enemy.health, 0);
    enemy.healthElement.textContent = enemy.health;
    manaWhirlpool(player);

    logMessage(`${player.name} uses Whirlpool and deals ${damage} damage to ${enemy.name}!`);

    
    if (Math.random() < 0.25) {
        enemy.isStunned = true;
        enemy.stunnedTurns = 2;
        logMessage(`${enemy.name} is stunned by Whirlpool!`);
    }

    player.attackButton.disabled = true;
    player.movesButton.disabled = true;
    player.defendButton.disabled = true;
    player.whirlpoolButton.disabled = true;
    player.comboButton.disabled = true;
    player.itemButton.disabled = true;
    player.whirlpoolButton.classList.toggle("hidden");
    player.comboButton.classList.toggle("hidden");
    
    if (enemy.health <= 0) {
        endGame("Players");
    } else {
        nextTurn();
    }
}

export function handleCombo(player) {
    let damage = player.combo();
    if (enemy.isStunned) {
        damage = Math.floor(damage * 1.35);
    }
    enemy.health -= damage;
    enemy.health = Math.max(enemy.health, 0);
    enemy.healthElement.textContent = enemy.health;
    manaCombo(player);

    logMessage(`${player.name} uses Combo and deals ${damage} damage to ${enemy.name}!`);
    player.attackButton.disabled = true;
    player.movesButton.disabled = true;
    player.defendButton.disabled = true;
    player.whirlpoolButton.disabled = true;
    player.comboButton.disabled = true;
    player.itemButton.disabled = true;
    player.whirlpoolButton.classList.toggle("hidden");
    player.comboButton.classList.toggle("hidden");
    
    if (enemy.health <= 0) {
        endGame("Players");
    } else {
        nextTurn();
    }
}

export function handleItem(player) {
    if (itemUses > 0) {
        player.health += 50;
        player.health = Math.min(player.health, player.maxHealth);
        player.healthElement.textContent = player.health;

        const maxMana = player.name === "Salmon" ? 12 : 10;
        player.mana += 7;
        player.mana = Math.min(player.mana, maxMana);
        player.manaElement.textContent = player.mana;
        player.tempDefense = 0.25;
        itemUses--;
        document.querySelectorAll(`#items`).forEach(itemSpan => {
            itemSpan.textContent = itemUses;
        });

        logMessage(`${player.name} used an item! Health and bubbles restored.`);
        nextTurn();
    player.attackButton.disabled = true;
    player.movesButton.disabled = true;
    player.defendButton.disabled = true;
    player.whirlpoolButton.disabled = true;
    player.comboButton.disabled = true;
    player.itemButton.disabled = true;

        if (itemUses === 0) {
            player.itemButton.disabled = true;
            logMessage(`No more items left!`);
        }
    } else {
        logMessage(`No more items can be used!`);
    }
}



