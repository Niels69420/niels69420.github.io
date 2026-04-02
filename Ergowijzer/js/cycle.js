document.addEventListener('DOMContentLoaded', function () {
    const imageLinks = [
        {
            img: 'images/rug.png',
            link: 'klachten/rugklachten(daan).html'
        },
        {
            img: 'images/pols.png',
            link: 'klachten/gewrichtklachten(colin).html'
        },
        {
            img: 'images/nek.png',
            link: 'klachten/nekklachten(thijn).html'
        },
        {
            img: 'images/schouder.png',
            link: 'klachten/bureau(niels).html'
        },
        {
            img: 'images/knie.png',
            link: 'klachten/gewicht(dave).html'
        },
        {
            img: 'images/elleboog.png',
            link: 'klachten/item(kevin).html'
        }
    ];

    let currentIndex = 0;
    let isAnimating = false;

    // Classes
    const carouselContainer = document.querySelector('.imgziektes');
    const imgContainers = document.querySelectorAll('.imgziektes a');
    const prevButton = document.querySelector('.orange button');
    const nextButton = document.querySelector('.white button');

    // Initialize visible images
    function setupInitialState() {
        imgContainers.forEach((container, index) => {
            if (index >= 3) {
                container.classList.add('hidden');
            } else {
                container.classList.remove('hidden');
            }
        });
    }

    // Update images with animation
    function updateImages(direction) {
        if (isAnimating) return;
        isAnimating = true;
        
        // Add animation class based on direction
        carouselContainer.classList.add('slide-animation');
        
        // Set animation direction
        if (direction === 'prev') {
            carouselContainer.style.animationName = 'slideFromLeft';
        } else {
            carouselContainer.style.animationName = 'slideFromRight';
        }
        
        setTimeout(() => {
            imgContainers.forEach((container, index) => {
                const img = container.querySelector('img');
                if (!img) return;

                if (index < 3) {
                    const currentImageData = imageLinks[(currentIndex + index) % imageLinks.length];
                    img.src = currentImageData.img;
                    container.href = currentImageData.link;
                    container.classList.remove('hidden');
                    img.classList.add('fade-in');
                } else {
                    container.classList.add('hidden');
                }
            });
            
            // Remove animation class after update
            setTimeout(() => {
                carouselContainer.classList.remove('slide-animation');
                isAnimating = false;
            }, 300);
            
        }, 250);
    }

    // Previous button
    prevButton.addEventListener('click', function () {
        currentIndex = (currentIndex - 1 + imageLinks.length) % imageLinks.length;
        updateImages('prev');
    });

    // Next button
    nextButton.addEventListener('click', function () {
        currentIndex = (currentIndex + 1) % imageLinks.length;
        updateImages('next');
    });

    // Initialize
    setupInitialState();
    updateImages('next');
});

//dat de pagina smooth laadt
document.addEventListener("DOMContentLoaded", () => {
    // Voeg de 'loaded' klasse toe aan de body zodra de pagina is geladen
    document.body.classList.add("loaded");

    // Verwijder smooth loading bij klikken op een <a href>
    const links = document.querySelectorAll("a[href]");
    links.forEach(link => {
        link.addEventListener("click", () => {
            document.body.classList.remove("loaded"); // Verwijder de 'loaded' klasse
        });
    });
});

 

function checkQuiz() {
    const answers = document.querySelectorAll('input[type="radio"]:checked');
    let score = 0;

    answers.forEach(answer => {
        if (answer.value === "correct") {
            score++;
        }
    });

    const resultDiv = document.getElementById("quizResult");
    resultDiv.innerHTML = `<p>You scored ${score} out of 5!</p>`;
}
