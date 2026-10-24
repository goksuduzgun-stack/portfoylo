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
  contactForm.addEventListener("submit", async (event) => {
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

    const submitButton = contactForm.querySelector('button[type="submit"]');
    if (typeof window.gdPortfolioContact === "object" && window.gdPortfolioContact.url) {
      if (submitButton instanceof HTMLButtonElement) submitButton.disabled = true;
      feedback.classList.remove("is-error", "is-success");
      feedback.textContent = isTurkish ? "Mesaj gönderiliyor..." : "Sending your message...";

      try {
        const response = await fetch(window.gdPortfolioContact.url, {
          method: "POST",
          headers: { "Content-Type": "application/json" },
          body: JSON.stringify({
            name,
            email,
            subject,
            message,
            website: String(formData.get("website") ?? "")
          })
        });
        const result = await response.json().catch(() => null);

        if (!response.ok) {
          throw new Error(result?.message || (isTurkish
            ? "Mesaj gönderilemedi. Lütfen daha sonra tekrar deneyin."
            : "Your message could not be sent. Please try again later."));
        }

        feedback.textContent = isTurkish
          ? "Mesajınız e-posta olarak gönderildi."
          : "Your message has been sent by email.";
        feedback.classList.add("is-success");
        contactForm.reset();
      } catch (error) {
        feedback.textContent = error instanceof Error && error.name !== "TypeError"
          ? error.message
          : (isTurkish
            ? "Mesaj gönderilemedi. Lütfen doğrudan e-posta bağlantısını kullanın."
            : "Your message could not be sent. Please use the direct email link.");
        feedback.classList.add("is-error");
      } finally {
        if (submitButton instanceof HTMLButtonElement) submitButton.disabled = false;
      }
      return;
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

const ditherCanvas = document.querySelector(".home-dither-canvas");
const backgroundVideo = document.querySelector(".home-video");
const reduceMotionSetting = window.matchMedia("(prefers-reduced-motion: reduce)");

if (
  ditherCanvas instanceof HTMLCanvasElement
  && backgroundVideo instanceof HTMLVideoElement
  && !reduceMotionSetting.matches
) {
  const ditherContext = ditherCanvas.getContext("2d");
  const sampleCanvas = document.createElement("canvas");
  const sampleContext = sampleCanvas.getContext("2d", { willReadFrequently: true });
  const pointer = { x: 0.5, y: 0.5 };
  const bayer = [
    [0, 8, 2, 10],
    [12, 4, 14, 6],
    [3, 11, 1, 9],
    [15, 7, 13, 5]
  ];
  let lastFrame = 0;
  let animationFrame = 0;
  let isVisible = true;
  let hasReportedReadError = false;

  if (ditherContext && sampleContext) {
    window.addEventListener("pointermove", (event) => {
      pointer.x = event.clientX / Math.max(window.innerWidth, 1);
      pointer.y = event.clientY / Math.max(window.innerHeight, 1);
    }, { passive: true });

    const resizeDitherCanvas = () => {
      const bounds = ditherCanvas.getBoundingClientRect();
      const pixelRatio = Math.min(window.devicePixelRatio || 1, 1.5);
      ditherCanvas.width = Math.max(1, Math.round(bounds.width * pixelRatio));
      ditherCanvas.height = Math.max(1, Math.round(bounds.height * pixelRatio));
    };

    resizeDitherCanvas();
    window.addEventListener("resize", resizeDitherCanvas, { passive: true });

    if ("IntersectionObserver" in window) {
      const canvasObserver = new IntersectionObserver(([entry]) => {
        isVisible = entry.isIntersecting;
        if (isVisible && !animationFrame) {
          animationFrame = window.requestAnimationFrame(drawDitherFrame);
        }
      });
      canvasObserver.observe(ditherCanvas);
    }

    function drawDitherFrame(timestamp) {
      animationFrame = 0;
      if (!isVisible || document.hidden || reduceMotionSetting.matches) return;

      if (timestamp - lastFrame >= 66 && backgroundVideo.readyState >= 2) {
        lastFrame = timestamp;
        const bounds = ditherCanvas.getBoundingClientRect();
        const width = Math.max(1, Math.min(280, Math.ceil(bounds.width / 4)));
        const height = Math.max(1, Math.ceil(width * bounds.height / Math.max(bounds.width, 1)));

        if (sampleCanvas.width !== width || sampleCanvas.height !== height) {
          sampleCanvas.width = width;
          sampleCanvas.height = height;
        }

        sampleContext.drawImage(backgroundVideo, 0, 0, width, height);

        try {
          const pixels = sampleContext.getImageData(0, 0, width, height).data;
          const scaleX = ditherCanvas.width / width;
          const scaleY = ditherCanvas.height / height;
          const time = timestamp / 1000;

          ditherContext.clearRect(0, 0, ditherCanvas.width, ditherCanvas.height);
          for (let y = 0; y < height; y++) {
            for (let x = 0; x < width; x++) {
              const index = (y * width + x) * 4;
              const brightness = (
                pixels[index] * .2126
                + pixels[index + 1] * .7152
                + pixels[index + 2] * .0722
              ) / 255;
              const threshold = (bayer[y % 4][x % 4] + .5) / 16;
              const modulation = Math.sin(y * .19 + time * 2 + pointer.x * 4 + pointer.y * 2) * .07;

              if (brightness < threshold - modulation) continue;

              const magenta = x / width > .58 + (pointer.x - .5) * .12;
              ditherContext.fillStyle = magenta
                ? `rgba(255, 42, 133, ${.18 + brightness * .48})`
                : `rgba(0, 240, 255, ${.18 + brightness * .48})`;
              const size = Math.max(1, (.45 + brightness * .45) * scaleX);
              ditherContext.fillRect(x * scaleX, y * scaleY, size, size);
            }
          }
        } catch (error) {
          if (!hasReportedReadError) {
            console.error("Could not read a frame from the local hero video for the dither effect.", error);
            hasReportedReadError = true;
          }
          return;
        }
      }

      animationFrame = window.requestAnimationFrame(drawDitherFrame);
    }

    document.addEventListener("visibilitychange", () => {
      if (!document.hidden && !animationFrame) {
        animationFrame = window.requestAnimationFrame(drawDitherFrame);
      }
    });

    animationFrame = window.requestAnimationFrame(drawDitherFrame);
  }
}
