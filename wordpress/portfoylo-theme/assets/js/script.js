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

const projectDeck = document.querySelector("[data-project-deck]");

if (projectDeck instanceof HTMLElement) {
  const projectCards = Array.from(projectDeck.querySelectorAll("[data-project-card]"));
  const projectCount = projectDeck.querySelector("[data-project-count]");
  const projectOpen = projectDeck.querySelector("[data-project-open]");
  const previousProject = projectDeck.querySelector("[data-project-previous]");
  const nextProject = projectDeck.querySelector("[data-project-next]");
  const projectDialog = document.querySelector("[data-project-dialog]");
  const projectDialogTitle = projectDialog?.querySelector("[data-project-dialog-title]");
  const projectDialogContent = projectDialog?.querySelector("[data-project-dialog-content]");
  const closeProjectDialog = projectDialog?.querySelector("[data-project-dialog-close]");
  let activeProject = Number(projectDeck.dataset.active) || 0;

  const showProject = (index) => {
    activeProject = (index + projectCards.length) % projectCards.length;
    projectDeck.dataset.active = String(activeProject);

    projectCards.forEach((card, cardIndex) => {
      let distance = (cardIndex - activeProject + projectCards.length) % projectCards.length;
      if (distance > projectCards.length / 2) distance -= projectCards.length;
      card.dataset.position = distance === 0
        ? "active"
        : Math.abs(distance) === 1
          ? (distance < 0 ? "left" : "right")
          : (distance < 0 ? "far-left" : "far-right");
      card.setAttribute("aria-pressed", String(cardIndex === activeProject));
    });

    const selectedCard = projectCards[activeProject];
    if (projectCount) {
      projectCount.textContent = `${String(activeProject + 1).padStart(2, "0")} / ${String(projectCards.length).padStart(2, "0")}`;
    }
  };

  const openProjectDetails = () => {
    const selectedCard = projectCards[activeProject];
    if (!(projectDialog instanceof HTMLDialogElement)
      || !(projectDialogTitle instanceof HTMLElement)
      || !(projectDialogContent instanceof HTMLElement)
      || !(selectedCard instanceof HTMLElement)) return;

    const target = selectedCard.dataset.target;
    const source = target ? document.querySelector(target) : null;
    const sourceContent = target === "#vybee"
      ? source?.querySelector(".container")
      : source;

    if (!(sourceContent instanceof HTMLElement)) {
      console.error(`Project details could not be found for "${target || "unknown target"}".`);
      return;
    }

    const title = selectedCard.querySelector("strong")?.textContent?.trim();
    projectDialogTitle.textContent = title || "Project details";
    projectDialogContent.replaceChildren(sourceContent.cloneNode(true));
    projectDialogContent.querySelectorAll("[id]").forEach((element) => element.removeAttribute("id"));
    projectDialog.showModal();
  };

  projectOpen?.addEventListener("click", openProjectDetails);
  closeProjectDialog?.addEventListener("click", () => {
    if (projectDialog instanceof HTMLDialogElement) projectDialog.close();
  });
  projectDialog?.addEventListener("click", (event) => {
    if (event.target === projectDialog && projectDialog instanceof HTMLDialogElement) {
      projectDialog.close();
    }
  });
  projectDialog?.addEventListener("cancel", (event) => {
    event.preventDefault();
    if (projectDialog instanceof HTMLDialogElement) projectDialog.close();
  });
  projectDialog?.addEventListener("keydown", (event) => {
    if (event.key === "Escape" && projectDialog instanceof HTMLDialogElement && projectDialog.open) {
      event.preventDefault();
      projectDialog.close();
    }
  });

  projectCards.forEach((card, index) => {
    card.addEventListener("click", () => showProject(index));
  });
  previousProject?.addEventListener("click", () => showProject(activeProject - 1));
  nextProject?.addEventListener("click", () => showProject(activeProject + 1));
  projectDeck.addEventListener("keydown", (event) => {
    if (event.key === "ArrowLeft") {
      event.preventDefault();
      showProject(activeProject - 1);
    } else if (event.key === "ArrowRight") {
      event.preventDefault();
      showProject(activeProject + 1);
    }
  });

  let pointerStart = null;
  projectDeck.addEventListener("pointerdown", (event) => {
    pointerStart = event.clientX;
  }, { passive: true });
  projectDeck.addEventListener("pointerup", (event) => {
    if (pointerStart === null) return;
    const difference = event.clientX - pointerStart;
    pointerStart = null;
    if (Math.abs(difference) > 55) {
      showProject(activeProject + (difference < 0 ? 1 : -1));
    }
  }, { passive: true });
  projectDeck.addEventListener("pointercancel", () => {
    pointerStart = null;
  });

  showProject(activeProject);
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

const backgroundVideo = document.querySelector(".home-video");
const reduceMotionSetting = window.matchMedia("(prefers-reduced-motion: reduce)");

if (backgroundVideo instanceof HTMLVideoElement) {
  const homeLanding = backgroundVideo.closest(".home-landing");

  if (homeLanding instanceof HTMLElement) {
    const isTurkish = document.documentElement.lang === "tr";
    const labels = isTurkish
      ? { play: "Videoyu oynat", retry: "Video yüklenemedi, tekrar dene" }
      : { play: "Play background video", retry: "Video failed to load; try again" };
    const playButton = document.createElement("button");
    playButton.type = "button";
    playButton.className = "home-video-toggle";
    playButton.textContent = labels.play;
    playButton.hidden = true;
    homeLanding.append(playButton);

    const showPlayButton = () => {
      playButton.hidden = false;
    };

    backgroundVideo.addEventListener("playing", () => {
      homeLanding.classList.add("is-video-playing");
      playButton.hidden = true;
      playButton.textContent = labels.play;
    });
    backgroundVideo.addEventListener("error", () => {
      playButton.textContent = labels.retry;
      showPlayButton();
    });

    playButton.addEventListener("click", () => {
      backgroundVideo.play().catch(() => {
        playButton.textContent = labels.retry;
        showPlayButton();
      });
    });

    if (reduceMotionSetting.matches) {
      backgroundVideo.pause();
      showPlayButton();
    } else {
      backgroundVideo.play().catch(showPlayButton);
    }
  }
}
