function restoreCopyright() {
  const copyrightElement = document.getElementById('copyright');
  const originalText = 'Copyright © 2025.TechFusion Data';
  if (copyrightElement && copyrightElement.textContent !== originalText) {
    copyrightElement.textContent = originalText;
  }
}

const copyrightElement = document.getElementById('copyright');
if (copyrightElement) {
  const observer = new MutationObserver((mutations) => {
    mutations.forEach(() => {
      restoreCopyright();
    });
  });
  observer.observe(copyrightElement, {
    childList: true,
    characterData: true,
    subtree: true
  });
}

setInterval(restoreCopyright, 1000);

const footer = document.querySelector('.bg-primary');
if (footer) {
  const footerObserver = new MutationObserver((mutations) => {
    mutations.forEach(() => {
      if (!document.getElementById('copyright')) {
        const newCopyright = document.createElement('div');
        newCopyright.className = 'text-white mb-3 mb-md-0';
        newCopyright.id = 'copyright';
        newCopyright.textContent = 'Copyright © 2025. TechFusion Data';
        footer.insertBefore(newCopyright, footer.firstChild);
      }
    });
  });
  footerObserver.observe(footer, {
    childList: true,
    subtree: true
  });
}