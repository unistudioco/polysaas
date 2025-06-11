// global options
let
ENABLE_PAGE_PRELOADER = true, 
DEFAULT_DARK_MODE = false, 
USE_LOCAL_STORAGE = true, 
USE_SYSTEM_PREFERENCES = false, 
DEFAULT_BREAKPOINTS = { xs: 0, sm: 576, md: 768, lg: 992, xl: 1200, xxl: 1400 };

// add dom-ready class
document.addEventListener('DOMContentLoaded', () => {
    html.classList.add('dom-ready');
});

// body scroll width
const updateScrollWidth = () => document.documentElement.style.setProperty('--body-scroll-width', `${window.innerWidth - document.documentElement.clientWidth}px`);
window.addEventListener('resize', updateScrollWidth);
updateScrollWidth();

// default breakpoints classes
const html = document.documentElement, 
      setupBp = (bp, bpSize, type = 'min') => {
          const media = matchMedia(`(${type}-width: ${bpSize}px)`), 
                cls = `bp-${bp}${type === 'max' ? '-max' : ''}`, 
                update = () => html.classList.toggle(cls, media.matches);
          media.onchange = update;
          update();
      };
Object.entries(DEFAULT_BREAKPOINTS).forEach(([bp, bpSize]) => {
    setupBp(bp, bpSize, 'min');
    setupBp(bp, bpSize - 1, 'max');
});

// auto darkmode feature
const isDarkMode = () => html.classList.contains('uc-dark'), 
      setDarkMode = enableDark => {
          enableDark = !!enableDark;
          if (isDarkMode() === enableDark) return;
          html.classList.toggle('uc-dark', enableDark);
          window.dispatchEvent(new CustomEvent('darkmodechange'));
      }, 
      getInitialDarkMode = () => USE_LOCAL_STORAGE && localStorage.getItem('darkMode') !== null ? localStorage.getItem('darkMode') === '1' : USE_SYSTEM_PREFERENCES ? matchMedia('(prefers-color-scheme: dark)').matches : DEFAULT_DARK_MODE;
setDarkMode(getInitialDarkMode());

// darkmode feature by url parameters
const dark = new URLSearchParams(location.search).get('dark');
if (dark) html.classList.toggle('uc-dark', dark === '1');

// page preloader feature
if (ENABLE_PAGE_PRELOADER) {
    const style = document.createElement('style');
    style.textContent = `
        .uc-pageloader {
            position: fixed; top: 0; left: 0; bottom: 0; right: 0;
            display: flex; justify-content: center; align-items: center;
            z-index: 99999; background-color: white;
        }
        .uc-dark .uc-pageloader, .uc-pageloader:where(.uc-dark) {
            background-color: #131313;
        }
        .uc-pageloader>.loading {
            display: inline-block; position: relative; width: 40px; height: 40px;
        }
        .uc-pageloader>.loading>div {
            box-sizing: border-box; display: block; position: absolute;
            width: 40px; height: 40px; margin: 0;
            border: 4px solid transparent; border-radius: 50%;
            animation: uc-loading 1s cubic-bezier(0.5, 0, 0.5, 1) infinite;
            border-color: var(--color-primary) transparent transparent transparent;
        }
        .uc-pageloader>.loading>div:nth-child(1) { animation-delay: -0.1s; }
        .uc-pageloader>.loading>div:nth-child(2) { animation-delay: -0.2s; }
        .uc-pageloader>.loading>div:nth-child(3) { animation-delay: -0.3s; }
        @keyframes uc-loading { 0% { transform: rotate(0deg); } 100% { transform: rotate(360deg); } }
        html.show-preloader body { display: none; }
    `;
    document.head.append(style);

    const preloader = document.createElement('div');
    preloader.className = 'uc-pageloader';
    preloader.innerHTML = '<div class="loading"><div></div><div></div><div></div><div></div></div>';
    html.classList.add('show-preloader');
    html.append(preloader);

    (async () => {
        const t0 = Date.now();
        await new Promise(r => document.addEventListener('DOMContentLoaded', r));
        html.classList.remove('show-preloader');
        await new Promise(r => requestAnimationFrame(r));
        await new Promise(r => setTimeout(r, Math.max(0, 500 - (Date.now() - t0))));
        preloader.style.transition = `opacity 1.1s cubic-bezier(0.8, 0, 0.2, 1)`;
        preloader.style.opacity = 0;
        await new Promise(r => setTimeout(r, 1100));
        preloader.remove();
    })();
}

// START_DEV_ONLY
window.addEventListener('message', e => {
    if (e.data.type === 'scroll') window.scrollTo(e.data.x, e.data.y);
});
document.addEventListener('DOMContentLoaded', () => {
    html.scrollTop = +(localStorage.getItem('scrollY') || 0) || 0;
    window.addEventListener('scroll', () => localStorage.setItem('scrollY', html.scrollTop + ''));
});

if (new URLSearchParams(location.search).get('editor')) {
    import('./editor.js');
} else {
    const url = new URL(location.href);
    url.searchParams.set('editor', '1');
    console.log('%cYou can enable the editor mode by adding ?editor=1 to the url\nExample: ' + url.href, 'background-color: #222; color: #bada55; border: solid 1px #bada55; padding: 5px 20px;');
}
// END_DEV_ONLY