const menuToggle = document.querySelector(".menu-toggle");
const mainNav = document.querySelector(".main-nav");
const contactEmail = "goksuduzgun@gmail.com";

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

const reducedMotion = window.matchMedia("(prefers-reduced-motion: reduce)").matches;
const observerMargin = Math.round(window.innerHeight * (reducedMotion ? 0.35 : 0.42));

if ("IntersectionObserver" in window) {
  const showcases = [
    { visual: "[data-experience-visual]", steps: "[data-experience-step]", stageAttribute: "experienceStep" },
    { visual: "[data-project-visual]", steps: "[data-project-step]", stageAttribute: "projectStep" }
  ];

  for (const showcase of showcases) {
    const visual = document.querySelector(showcase.visual);
    const steps = document.querySelectorAll(showcase.steps);
    if (!(visual instanceof HTMLElement) || steps.length === 0) continue;

    const stepObserver = new IntersectionObserver((entries) => {
      for (const entry of entries) {
        if (!entry.isIntersecting) continue;
        const step = entry.target;
        if (!(step instanceof HTMLElement)) continue;
        const stage = step.dataset[showcase.stageAttribute];
        if (!stage) continue;

        visual.dataset.stage = stage;
        const windowTitle = visual.querySelector("[data-window-title]");
        if (windowTitle instanceof HTMLElement) {
          const titles = document.documentElement.lang === "tr"
            ? {
                vybee: "VYBEE · MOBİL",
                clinic: "HASTA TAKİP · PYTHON",
                library: "KÜTÜPHANE · PYTHON",
                market: "MARKET SATIŞ · C# / .NET",
                portfolio: "PORTFOLYO · WEB"
              }
            : {
                vybee: "VYBEE · MOBILE",
                clinic: "PATIENT TRACKING · PYTHON",
                library: "LIBRARY · PYTHON",
                market: "RETAIL SALES · C# / .NET",
                portfolio: "PORTFOLIO · WEB"
              };
          windowTitle.textContent = titles[stage] || `GÖKSU · ${stage.toUpperCase()}`;
        }
        steps.forEach((item) => {
          item.classList.toggle("is-active", item === step);
        });
      }
    }, {
      rootMargin: `-${observerMargin}px 0px -${observerMargin}px 0px`,
      threshold: 0
    });

    steps.forEach((step) => stepObserver.observe(step));
  }
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
        ? "İletişim e-posta adresi yapılandırılmamış. Lütfen doğrudan e-posta bağlantısını kullan."
        : "The contact email is not configured. Please use the direct email link.";
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
