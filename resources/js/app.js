import './bootstrap';

document.addEventListener('DOMContentLoaded', () => {
	const yearTarget = document.querySelector('[data-year]');
	if (yearTarget) yearTarget.textContent = String(new Date().getFullYear());

	const spinner = document.querySelector('[data-spinner]');
	if (spinner) {
		window.setTimeout(() => spinner.classList.add('is-hidden'), 550);
	}

	const menuToggle = document.querySelector('[data-menu-toggle]');
	const mainNav = document.querySelector('[data-main-nav]');
	if (menuToggle && mainNav) {
		menuToggle.addEventListener('click', () => {
			const isOpen = mainNav.classList.toggle('is-open');
			menuToggle.setAttribute('aria-expanded', String(isOpen));
		});

		mainNav.querySelectorAll('a').forEach((link) => {
			link.addEventListener('click', () => {
				mainNav.classList.remove('is-open');
				menuToggle.setAttribute('aria-expanded', 'false');
			});
		});
	}

	const slides = Array.from(document.querySelectorAll('[data-slide]'));
	const prevBtn = document.querySelector('[data-slide-prev]');
	const nextBtn = document.querySelector('[data-slide-next]');

	if (slides.length > 1) {
		let activeIndex = 0;

		const showSlide = (index) => {
			slides[activeIndex].classList.remove('is-active');
			activeIndex = (index + slides.length) % slides.length;
			slides[activeIndex].classList.add('is-active');
		};

		prevBtn?.addEventListener('click', () => showSlide(activeIndex - 1));
		nextBtn?.addEventListener('click', () => showSlide(activeIndex + 1));

		window.setInterval(() => showSlide(activeIndex + 1), 6000);
	}

	const revealElements = document.querySelectorAll('.reveal');
	if ('IntersectionObserver' in window) {
		const observer = new IntersectionObserver(
			(entries, ref) => {
				entries.forEach((entry) => {
					if (entry.isIntersecting) {
						entry.target.classList.add('is-visible');
						ref.unobserve(entry.target);
					}
				});
			},
			{ threshold: 0.15 },
		);
		revealElements.forEach((element) => observer.observe(element));
	} else {
		revealElements.forEach((element) => element.classList.add('is-visible'));
	}
});
