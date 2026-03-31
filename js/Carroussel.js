document.querySelectorAll('.carousel-hid').forEach((carousel) => {
  const track = carousel.querySelector('.track');
  const slides = carousel.querySelectorAll('.slide-resp');
  const prevBtn = carousel.parentElement.querySelector('.btn-prev');
  const nextBtn = carousel.parentElement.querySelector('.btn-next');

  let current = 0;
  const total = slides.length;
  const visible = 2;

  function goTo(index) {
    const remainingImg = total - visible;
    if (index < 0) index = remainingImg;
    if (index > remainingImg) index = 0;
    current = index;
    track.style.transform = `translateX(-${current * (100 / visible)}%)`;
  }

  prevBtn.addEventListener('click', () => {
    goTo(current !== 0 ? current - 1 : current);
  });

  nextBtn.addEventListener('click', () => {
    goTo(current === total - visible ? current - (total - visible) : current + 1);
  });
});