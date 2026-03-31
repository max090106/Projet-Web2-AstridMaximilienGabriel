const track = document.querySelector('.track');
const slides = document.querySelectorAll('.slide-resp');
const prevBtn = document.getElementById('prev');
const nextBtn = document.getElementById('btn-nexts');

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
   goTo(current === 3 ? current - 3 : current + 1);
});
