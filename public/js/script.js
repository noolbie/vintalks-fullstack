// SECTION NAVIGATION BAR
const burger = document.querySelector(".nav-burger");
const navMenu = document.querySelector(".nav-menu");

burger.addEventListener("click", (e) => {
    e.stopPropagation();
    navMenu.classList.toggle("active");
});

document.querySelectorAll(".nav-links a").forEach(link => {
    link.addEventListener("click", () => {
        navMenu.classList.remove("active");
    });
});

document.addEventListener("click", (e) => {
    if (
        navMenu.classList.contains("active") &&
        !navMenu.contains(e.target) &&
        !burger.contains(e.target)
    ) {
        navMenu.classList.remove("active");
    }
});

// SECTION MENTORS
const track = document.querySelector(".mentor-track");
const nextBtn = document.querySelector(".next");
const prevBtn = document.querySelector(".prev");

let cardWidth = document.querySelector(".mentor-card").offsetWidth + 25;
let index = 0;

nextBtn.addEventListener("click", () => {
    index++;
    slide();
});

prevBtn.addEventListener("click", () => {
    index--;
    slide();
});

function slide() {
    const cards = document.querySelectorAll(".mentor-card").length;

    if (index < 0) index = cards - 1;
    if (index >= cards) index = 0;

    track.style.transform = `translateX(${-index * cardWidth}px)`;
}

window.addEventListener("resize", () => {
    cardWidth = document.querySelector(".mentor-card").offsetWidth + 25;
    slide();
});


// SECTION PROGRAMS
const cards = document.querySelectorAll('.card');

cards.forEach((card) => {
    card.addEventListener('mouseenter', () => {
        card.style.transform = "translateY(-30px)";
    });

    card.addEventListener('mouseleave', () => {
        if (card.classList.contains('popular')) {
            card.style.transform = "translateY(-15px)";
        } else {
            card.style.transform = "translateY(0)";
        }
    });
});

// SECTION TESTIMONIAL
const testiSlider = document.getElementById("slider");
const testiLeft = document.getElementById("testi-left");
const testiRight = document.getElementById("testi-right");

let testiCurrent = 0;

function updateTestiSlider() {
    const width = testiSlider.children[0].offsetWidth + 20;
    testiSlider.style.transform = `translateX(-${testiCurrent * width}px)`;
}

testiRight.onclick = () => {
    const visible = 1;
    const max = testiSlider.children.length - visible;
    if (testiCurrent < max) testiCurrent++;
    updateTestiSlider();
};

testiLeft.onclick = () => {
    if (testiCurrent > 0) testiCurrent--;
    updateTestiSlider();
};

// SECTION FAQ
const faqItems = document.querySelectorAll(".faq-item");

faqItems.forEach(item => {
    const header = item.querySelector(".faq-header");

    header.addEventListener("click", () => {
        faqItems.forEach(i => {
            if (i !== item) {
                i.classList.remove("active");
                i.querySelector(".faq-content").style.maxHeight = null;
            }
        });

        item.classList.toggle("active");
        const content = item.querySelector(".faq-content");

        if (item.classList.contains("active")) {
            content.style.maxHeight = content.scrollHeight + "px";
        } else {
            content.style.maxHeight = null;
        }
    });
});