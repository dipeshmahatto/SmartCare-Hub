(function () {
  const wizard = document.querySelector("[data-booking-wizard]");
  if (!wizard) return;

  const form = document.getElementById("booking-wizard-form");
  const panels = [...wizard.querySelectorAll("[data-panel]")];
  const progressItems = [...wizard.querySelectorAll("[data-progress-step]")];
  const progressFill = document.getElementById("booking-progress-fill");
  const summaryProgressFill = document.getElementById("summary-progress-fill");
  const summaryProgressText = document.getElementById("summary-progress-text");

  const inputs = {
    category: document.getElementById("booking-category"),
    doctor: document.getElementById("booking-doctor"),
    day: document.getElementById("booking-day"),
    time: document.getElementById("booking-time"),
  };

  const summary = {
    category: document.getElementById("summary-category"),
    doctor: document.getElementById("summary-doctor"),
    day: document.getElementById("summary-day"),
    time: document.getElementById("summary-time"),
  };

  const doctorGrid = document.getElementById("doctor-choice-grid");
  const doctorCards = [...doctorGrid.querySelectorAll(".doctor-choice")];
  const doctorEmpty = document.getElementById("doctor-empty");
  const doctorSpecialtyChip = document.getElementById("doctor-specialty-chip");
  const timeGrid = document.getElementById("time-slot-grid");
  const timeLoading = document.getElementById("time-loading");
  const timeEmpty = document.getElementById("time-empty");
  const timeError = document.getElementById("time-error");
  const retryTimes = document.getElementById("retry-times");
  const confirmButton = document.getElementById("booking-confirm");

  let currentStep = 1;
  let selectedDoctorCard = null;
  let timeRequestToken = 0;

  function niceDay(day) {
    if (!day) return "";
    return day.charAt(0) + day.slice(1).toLowerCase();
  }

  function setSelected(buttons, selected) {
    buttons.forEach((button) =>
      button.classList.toggle("is-selected", button === selected),
    );
  }

  function updateSummary() {
    const values = {
      category: inputs.category.value,
      doctor: inputs.doctor.value,
      day: niceDay(inputs.day.value),
      time: inputs.time.value,
    };

    Object.keys(values).forEach((key) => {
      summary[key].textContent = values[key] || "Not selected";
      const row = document.querySelector(`[data-summary-row="${key}"]`);
      if (row) row.classList.toggle("is-complete", Boolean(values[key]));
    });

    const completeCount = [
      inputs.category.value,
      inputs.doctor.value,
      inputs.day.value,
      inputs.time.value,
    ].filter(Boolean).length;
    const percent = completeCount * 25;
    summaryProgressText.textContent = `${percent}%`;
    summaryProgressFill.style.width = `${percent}%`;
  }

  function updateStepUI() {
    panels.forEach((panel) => {
      const panelStep = Number(panel.dataset.panel);
      const active = panelStep === currentStep;
      panel.classList.toggle("is-active", active);
      panel.hidden = !active;
    });

    progressItems.forEach((item) => {
      const step = Number(item.dataset.progressStep);
      item.classList.toggle("is-active", step === currentStep);
      item.classList.toggle("is-complete", step < currentStep);
    });

    const mainPercent = ((currentStep - 1) / (progressItems.length - 1)) * 100;
    progressFill.style.width = `${mainPercent}%`;
    updateNextButton();

    const activePanel = panels.find(
      (panel) => Number(panel.dataset.panel) === currentStep,
    );
    if (activePanel) {
      activePanel.setAttribute("tabindex", "-1");
      window.requestAnimationFrame(() => {
        activePanel.focus({ preventScroll: true });
        const rect = wizard.getBoundingClientRect();
        if (rect.top < 0 || rect.top > window.innerHeight * 0.35) {
          wizard.scrollIntoView({ behavior: "smooth", block: "start" });
        }
      });
    }
  }

  function stepIsValid(step) {
    if (step === 1) return Boolean(inputs.category.value);
    if (step === 2) return Boolean(inputs.doctor.value);
    if (step === 3) return Boolean(inputs.day.value);
    if (step === 4) return Boolean(inputs.time.value);
    return true;
  }

  function updateNextButton() {
    const panel = panels.find(
      (item) => Number(item.dataset.panel) === currentStep,
    );
    if (!panel) return;
    const next = panel.querySelector("[data-next]");
    if (next) next.disabled = !stepIsValid(currentStep);
  }

  function filterDoctors(specialty) {
    const selectedSpecialty = specialty.trim().toLowerCase();

    doctorCards.forEach((card) => {
      const doctorSpecialty = (card.dataset.specialty || "")
        .trim()
        .toLowerCase();

      const matches = doctorSpecialty === selectedSpecialty;

      card.hidden = !matches;
    });

    doctorSpecialtyChip.textContent = specialty || "Specialty";
  }

  function clearAfter(field) {
    if (field === "category") {
      inputs.doctor.value = "";
      inputs.day.value = "";
      inputs.time.value = "";
      selectedDoctorCard = null;
      setSelected(doctorCards, null);
      setSelected([...document.querySelectorAll(".weekday-choice")], null);
      timeGrid.innerHTML = "";
    } else if (field === "doctor") {
      inputs.day.value = "";
      inputs.time.value = "";
      setSelected([...document.querySelectorAll(".weekday-choice")], null);
      timeGrid.innerHTML = "";
    } else if (field === "day") {
      inputs.time.value = "";
      timeGrid.innerHTML = "";
    }
  }

  async function loadTimes() {
    const doctor = inputs.doctor.value;
    const day = inputs.day.value;
    if (!doctor || !day) return;

    const requestToken = ++timeRequestToken;
    timeGrid.innerHTML = "";
    timeEmpty.hidden = true;
    timeError.hidden = true;
    timeLoading.hidden = false;
    inputs.time.value = "";
    updateSummary();
    updateNextButton();

    document.getElementById("time-context-doctor").textContent = doctor;
    document.getElementById("time-context-day").textContent = niceDay(day);

    const body = new URLSearchParams({ doctor, day });

    try {
      const response = await fetch("query/get_times.php", {
        method: "POST",
        headers: {
          "Content-Type": "application/x-www-form-urlencoded; charset=UTF-8",
        },
        body: body.toString(),
      });

      if (!response.ok) throw new Error("Availability request failed");
      const html = await response.text();
      if (requestToken !== timeRequestToken) return;

      const parser = new DOMParser();
      const doc = parser.parseFromString(
        `<select>${html}</select>`,
        "text/html",
      );
      const options = [...doc.querySelectorAll("option")]
        .map((option) => ({
          value: option.value.trim(),
          label: option.textContent.trim(),
        }))
        .filter((option) => option.value !== "");

      if (!options.length) {
        timeEmpty.hidden = false;
        return;
      }

      options.forEach((option) => {
        const button = document.createElement("button");
        button.type = "button";
        button.className = "time-slot-choice";
        button.dataset.time = option.value;
        button.innerHTML = `<span><i class="fa-regular fa-clock"></i></span><strong>${escapeHtml(option.label)}</strong><small>Available</small><em><i class="fa-solid fa-check"></i></em>`;
        button.addEventListener("click", () => {
          setSelected(
            [...timeGrid.querySelectorAll(".time-slot-choice")],
            button,
          );
          inputs.time.value = option.value;
          updateSummary();
          updateNextButton();
        });
        timeGrid.appendChild(button);
      });
    } catch (error) {
      if (requestToken === timeRequestToken) timeError.hidden = false;
    } finally {
      if (requestToken === timeRequestToken) timeLoading.hidden = true;
    }
  }

  function escapeHtml(value) {
    const div = document.createElement("div");
    div.textContent = value;
    return div.innerHTML;
  }

  function updateReview() {
    document.getElementById("review-specialty").textContent =
      inputs.category.value || "Specialty";
    document.getElementById("review-doctor").textContent =
      inputs.doctor.value || "Doctor";
    document.getElementById("review-day").textContent =
      niceDay(inputs.day.value) || "—";
    document.getElementById("review-time").textContent =
      inputs.time.value || "—";

    const meta = selectedDoctorCard
      ? [
          selectedDoctorCard.dataset.qualification,
          selectedDoctorCard.dataset.address,
        ]
          .filter(Boolean)
          .join(" · ")
      : "Doctor details";
    document.getElementById("review-doctor-meta").textContent = meta;

    const avatarTarget = document.getElementById("review-doctor-avatar");
    const sourceImage = selectedDoctorCard
      ? selectedDoctorCard.querySelector("img")
      : null;
    const sourceFallback = selectedDoctorCard
      ? selectedDoctorCard.querySelector(".doctor-choice__fallback")
      : null;
    avatarTarget.innerHTML = "";
    if (sourceImage) {
      const image = document.createElement("img");
      image.src = sourceImage.src;
      image.alt = inputs.doctor.value;
      avatarTarget.appendChild(image);
    } else {
      avatarTarget.textContent = sourceFallback
        ? sourceFallback.textContent.trim()
        : "DR";
    }
  }

  document.querySelectorAll(".specialty-choice").forEach((button) => {
    button.addEventListener("click", () => {
      const specialty = button.dataset.specialty;
      const changed = inputs.category.value !== specialty;
      setSelected([...document.querySelectorAll(".specialty-choice")], button);
      inputs.category.value = specialty;
      if (changed) clearAfter("category");
      filterDoctors(specialty);
      updateSummary();
      updateNextButton();
    });
  });

  doctorCards.forEach((button) => {
    button.addEventListener("click", () => {
      const doctor = button.dataset.doctorName;
      const changed = inputs.doctor.value !== doctor;
      setSelected(doctorCards, button);
      selectedDoctorCard = button;
      inputs.doctor.value = doctor;
      if (changed) clearAfter("doctor");
      selectedDoctorCard = button;
      button.classList.add("is-selected");
      updateSummary();
      updateNextButton();
    });
  });

  document.querySelectorAll(".weekday-choice").forEach((button) => {
    button.addEventListener("click", () => {
      const day = button.dataset.day;
      const changed = inputs.day.value !== day;
      setSelected([...document.querySelectorAll(".weekday-choice")], button);
      inputs.day.value = day;
      if (changed) clearAfter("day");
      button.classList.add("is-selected");
      updateSummary();
      updateNextButton();
    });
  });

  wizard.querySelectorAll("[data-next]").forEach((button) => {
    button.addEventListener("click", async () => {
      if (!stepIsValid(currentStep) || currentStep >= 5) return;
      currentStep += 1;
      if (currentStep === 4) await loadTimes();
      if (currentStep === 5) updateReview();
      updateStepUI();
    });
  });

  wizard.querySelectorAll("[data-back]").forEach((button) => {
    button.addEventListener("click", () => {
      if (currentStep <= 1) return;
      currentStep -= 1;
      updateStepUI();
    });
  });

  retryTimes.addEventListener("click", loadTimes);

  progressItems.forEach((item) => {
    item.addEventListener("click", () => {
      const target = Number(item.dataset.progressStep);
      if (target < currentStep) {
        currentStep = target;
        updateStepUI();
      }
    });
  });

  form.addEventListener("submit", (event) => {
    const complete = Object.values(inputs).every((input) =>
      Boolean(input.value),
    );
    if (!complete) {
      event.preventDefault();
      return;
    }
    confirmButton.disabled = true;
    confirmButton.classList.add("is-submitting");
    confirmButton.querySelector("span").textContent = "Sending request…";
  });

  updateSummary();
  updateStepUI();
})();
