// === Section: Berita Alumni ===
document.addEventListener("DOMContentLoaded", function() {
  const articleLinks = document.querySelectorAll(".article-link");
  const modal = document.getElementById("modal");
  const modalTitle = document.getElementById("modalTitle");
  const modalDate = document.getElementById("modalDate");
  const modalImage = document.querySelector("#modalImage img");
  const modalContent = document.getElementById("modalContent");
  const modalClose = document.getElementById("modalClose");
  const cardContainer = document.getElementById("beritaCards");
  const leftArrow = document.getElementById("arrowLeft");
  const rightArrow = document.getElementById("arrowRight");

  if (!cardContainer) return;

  // Scroll Controls
  const scrollStep = 250;
  leftArrow.addEventListener("click", () => cardContainer.scrollLeft -= scrollStep);
  rightArrow.addEventListener("click", () => cardContainer.scrollLeft += scrollStep);

  // Modal Handling
  // pada event listener click (di landing.js)
articleLinks.forEach(link => {
  link.addEventListener("click", function(e) {
    e.preventDefault();
    const title = this.dataset.title;
    const date = this.dataset.date;
    const imageSrc = this.dataset.image;

    // ambil HTML dari elemen tersembunyi
    const contentEl = this.querySelector('.article-content');
    const contentHtml = contentEl ? contentEl.innerHTML : '';

    modalTitle.textContent = title;
    modalDate.textContent = date;
    modalImage.setAttribute('src', imageSrc);
    modalContent.innerHTML = contentHtml;
    modal.classList.remove('hidden');
  });
});

  modalClose.addEventListener("click", () => modal.classList.add("hidden"));
  modal.addEventListener("click", e => {
    if (e.target === modal) modal.classList.add("hidden");
  });
});
