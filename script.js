//Scriptul corespunzator interfetei grafice si butonului de login

// Selectare elemente din meniu
let menu = document.querySelector('#menu-bars');
let navbar = document.querySelector('.navbar');

// Activare/dezactivare meniu
menu.onclick = () => {
    menu.classList.toggle('fa-times');
    navbar.classList.toggle('active');
}

// Toggler pentru temă
let themeToggler = document.querySelector('.theme-toggler');
let toggleBtn = document.querySelector('.toggle-btn');

toggleBtn.onclick = () => {
    themeToggler.classList.toggle('active');
}

// Resetare clase la scroll
window.onscroll = () => {
    menu.classList.remove('fa-times');
    navbar.classList.remove('active');
    themeToggler.classList.remove('active');
}

// Schimbare culoare temă
document.querySelectorAll('.theme-toggler .theme-btn').forEach(btn => {
    btn.onclick = () => {
        let color = btn.style.background;
        document.querySelector(':root').style.setProperty('--main-color', color);
    }
});

// Configurare Swiper pentru slider-ul principal
var swiper = new Swiper(".home-slider", {
    effect: "coverflow",
    grabCursor: true,
    centeredSlides: true,
    slidesPerView: "auto",
    coverflowEffect: {
        rotate: 0,
        stretch: 0,
        depth: 100,
        modifier: 2,
        slideShadows: true,
    },
    loop: true,
    autoplay: {
        delay: 3000,
        disableOnInteraction: false,
    }
});

// Configurare Swiper pentru slider-ul de recenzii
var swiper = new Swiper(".review-slider", {
    slidesPerView: 1,
    grabCursor: true,
    loop: true,
    spaceBetween: 10,
    breakpoints: {
        0: {
            slidesPerView: 1,
        },
        700: {
            slidesPerView: 2,
        },
        1050: {
            slidesPerView: 3,
        },
    },
    autoplay: {
        delay: 5000,
        disableOnInteraction: false,
    }
});

const openLoginModal = document.getElementById("open-login-modal");
const closeLoginModal = document.getElementById("close-login-modal");
const modal = document.getElementById("login-modal");
const loginForm = document.getElementById("login-form");
const errorMessage = document.getElementById("error-message");
const createAccount = document.getElementById("create-account");
const registerForm = document.getElementById("register-form");
const loginBtn = document.getElementById("login-btn");
const registerBtn = document.getElementById("register-btn");


// Open Modal
openLoginModal.addEventListener("click", () => {
    modal.style.display = "flex";
    registerForm.style.display = "none";  // Ascunde formularul de register când e deschis loginul
});

// Close Modal
closeLoginModal.addEventListener("click", () => {
    modal.style.display = "none";
});

// Close Modal on Click Outside
window.addEventListener("click", (event) => {
    if (event.target === modal) {
        modal.style.display = "none";
    }
});

// Switch între login și register
createAccount.addEventListener("click", (e) => {
    e.preventDefault();
    registerForm.style.display = "flex";  // Arată formularul de register
    loginForm.style.display = "none";     // Ascunde formularul de login
});

registerBtn.addEventListener("click", (e) => {
    e.preventDefault();
    const email = document.getElementById("register-email").value;
    const password = document.getElementById("register-password").value;

    if (email && password) {
        // Salvare cont nou în localStorage
        localStorage.setItem("savedEmail", email);
        localStorage.setItem("savedPassword", password);
        alert("Account created successfully!");
        modal.style.display = "none";  // Închide modalul după înregistrare
    } else {
        alert("Please fill in all fields!");
    }
});

// Validare login
loginForm.addEventListener("submit", (e) => {
    e.preventDefault();
    const email = document.getElementById("email").value;
    const password = document.getElementById("password").value;

    const savedEmail = localStorage.getItem("savedEmail");
    const savedPassword = localStorage.getItem("savedPassword");

    if (savedEmail && savedPassword) {
        if (email === savedEmail && password === savedPassword) {
            alert("Login successful!");
            modal.style.display = "none";
        } else {
            errorMessage.textContent = "Invalid email or password. Please try again!";
        }
    } else {
        errorMessage.textContent = "No account found. Please create an account first!";
    }
});