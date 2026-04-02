document.addEventListener("DOMContentLoaded", function() {
    const boot = document.getElementById("boot");
    const startPosition = -650;
    const endPosition = 1900; // Change this to your desired end position
    let currentPosition = startPosition;
    const speed = 4; // Change this to your desired speed

    function moveBoot() {
        currentPosition += speed;
        if (currentPosition >= endPosition) {
            currentPosition = startPosition;
        }
        boot.style.left = currentPosition + "px";
        requestAnimationFrame(moveBoot);
    }

    moveBoot();
});