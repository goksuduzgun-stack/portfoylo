const menuToggle = document.querySelector(".menu-toggle");
const mainNav = document.querySelector(".main-nav");
const contactEmail = "";

if (menuToggle && mainNav) {
  menuToggle.addEventListener("click", () => {
    const isExpanded = menuToggle.getAttribute("aria-expanded") === "true";
    menuToggle.setAttribute("aria-expanded", String(!isExpanded));
    mainNav.classList.toggle("is-open", !isExpanded);
  });

  mainNav.addEventListener("click", (event) => {
    if (event.target instanceof Element
      && event.target.closest("a")
      && window.matchMedia("(max-width: 700px)").matches) {
      menuToggle.setAttribute("aria-expanded", "false");
      mainNav.classList.remove("is-open");
    }
  });
}

const experienceVisual = document.querySelector("[data-experience-visual]");
const experienceSteps = document.querySelectorAll("[data-experience-step]");
const reducedMotion = window.matchMedia("(prefers-reduced-motion: reduce)").matches;

if (experienceVisual instanceof HTMLElement && experienceSteps.length > 0 && "IntersectionObserver" in window) {
  const stepObserver = new IntersectionObserver((entries) => {
    for (const entry of entries) {
      if (!entry.isIntersecting) continue;
      const step = entry.target;
      if (!(step instanceof HTMLElement)) continue;
      const stage = step.dataset.experienceStep;
      if (!stage) continue;

      experienceVisual.dataset.stage = stage;
      experienceSteps.forEach((item) => {
        item.classList.toggle("is-active", item === step);
      });
    }
  }, {
    rootMargin: reducedMotion ? "-35% 0px -35% 0px" : "-42% 0px -42% 0px",
    threshold: 0
  });

  experienceSteps.forEach((step) => stepObserver.observe(step));
}

const contactForm = document.querySelector("[data-contact-form]");

if (contactForm instanceof HTMLFormElement) {
  contactForm.addEventListener("submit", (event) => {
    event.preventDefault();
    if (!contactForm.reportValidity()) return;

    const formData = new FormData(contactForm);
    const name = String(formData.get("name") ?? "").trim();
    const email = String(formData.get("email") ?? "").trim();
    const isTurkish = document.documentElement.lang === "tr";
    const subject = String(formData.get("subject") ?? "").trim()
      || (isTurkish ? "Portfolyo sitesi mesajı" : "Portfolio website message");
    const message = String(formData.get("message") ?? "").trim();
    const body = `${message}\n\nFrom: ${name}\nEmail: ${email}`;
    const feedback = contactForm.querySelector("[data-form-feedback]");

    if (!(feedback instanceof HTMLElement)) {
      throw new Error("Contact form feedback element is missing.");
    }

    const recipient = contactEmail.trim();
    if (!recipient) {
      feedback.textContent = isTurkish
        ? "Formun e-posta gönderebilmesi için js/script.js içindeki contactEmail değerine kendi e-posta adresini ekle."
        : "To enable this form, add your email address to the contactEmail value in js/script.js.";
      feedback.classList.add("is-error");
      return;
    }

    feedback.textContent = isTurkish
      ? "E-posta uygulamanız, gönderime hazır iletiyle açılmalı."
      : "Your email app should open with the message ready to send.";
    feedback.classList.add("is-success");
    window.location.href = `mailto:${recipient}?subject=${encodeURIComponent(subject)}&body=${encodeURIComponent(body)}`;
  });
}
