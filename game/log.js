import { turnCount } from './turns.js';
import { enemy } from './characters.js';

function setCookie(name, value, days) {
    const date = new Date();
    date.setTime(date.getTime() + (days * 24 * 60 * 60 * 1000));
    const expires = "expires=" + date.toUTCString();
    document.cookie = name + "=" + value + ";" + expires + ";path=/";
}

function getCookie(name) {
    const value = `; ${document.cookie}`;
    const parts = value.split(`; ${name}=`);
    if (parts.length === 2) return parts.pop().split(';').shift();
}

export function updateLowestTurns() {
    document.getElementById('lowest-turns1').textContent = getCookie('turns_to_beat_Shark') || 'No record yet';
    document.getElementById('lowest-turns2').textContent = getCookie('turns_to_beat_Whale') || 'No record yet';
    document.getElementById('lowest-turns3').textContent = getCookie('turns_to_beat_Swordfish') || 'No record yet';
    document.getElementById('lowest-turns4').textContent = getCookie('turns_to_beat_Crab') || 'No record yet';
}

export function logMessage(message) {
    const combatLog = document.getElementById("combat-log");
    const logEntry = document.createElement("div");
    logEntry.textContent = message;
    combatLog.appendChild(logEntry);
    combatLog.scrollTop = combatLog.scrollHeight;
}

import { players } from './characters.js';

export function endGame(winner) {
    logMessage(`${winner} win!`);
    alert(`${winner} win! It took ${turnCount} turns to beat ${enemy.name}.`);
    
    if (winner === "Players") {
        const currentTurns = parseInt(getCookie(`turns_to_beat_${enemy.name}`)) || Infinity;
        if (turnCount < currentTurns) {
            setCookie(`turns_to_beat_${enemy.name}`, turnCount, 365);
        }
    }

    updateLowestTurns();
    players.forEach(player => {
        player.attackButton.disabled = true;
        player.movesButton.disabled = true;
        player.defendButton.disabled = true;
        player.whirlpoolButton.disabled = true;
        player.comboButton.disabled = true;
    });
    location.reload();
}
export function endGameEnemy(){
    alert(`You lost!`);
    players.forEach(player => {
        player.attackButton.disabled = true;
        player.movesButton.disabled = true;
        player.defendButton.disabled = true;
        player.whirlpoolButton.disabled = true;
        player.comboButton.disabled = true;
    });
    location.reload();
}