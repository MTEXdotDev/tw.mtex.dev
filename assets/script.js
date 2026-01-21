function formatHTML(html) {
  if (!html) return '';
  
  let formatted = html.replace(/>\s+</g, '><').trim();
  
  formatted = formatted
    .replace(/(<(?!\/)(?!span|strong|b|i|em|small)[^>]+>)/g, '\n$1') 
    .replace(/(<\/(?!span|strong|b|i|em|small)[^>]+>)/g, '\n$1') 
    .replace(/\n\n/g, '\n'); 

  const lines = formatted.split('\n');
  let indentLevel = 0;
  const tab = '  ';
  let result = '';

  lines.forEach((line) => {
    line = line.trim();
    if (!line) return;

    const isClosing = line.match(/^<\//);
    const isSelfClosing = line.match(/\/>$/) || line.match(/^<(img|input|br|hr|meta|link)/);
    
    if (isClosing && indentLevel > 0) {
      indentLevel--;
    }

    result += tab.repeat(indentLevel) + line + '\n';

    if (!isClosing && !isSelfClosing) {
      indentLevel++;
    }
  });

  return result.trim();
}

window.copyCode = () => {
  const code = document.getElementById('code-container').textContent;
  navigator.clipboard.writeText(code).then(() => {
    alert('Snippet copied to clipboard');
  });
};

function initTheme() {
  const savedTheme = localStorage.getItem('theme') || 'system';
  setTheme(savedTheme, false);
}

window.setTheme = (mode, save = true) => {
  if (save) localStorage.setItem('theme', mode);

  const isDark =
    mode === 'dark' ||
    (mode === 'system' &&
      window.matchMedia('(prefers-color-scheme: dark)').matches);

  if (isDark) {
    document.documentElement.classList.add('dark');
  } else {
    document.documentElement.classList.remove('dark');
  }

  updateThemeUI(mode);
};

function updateThemeUI(mode) {
  const slider = document.getElementById('theme-slider');
  if (!slider) return;

  const positions = {
    'system': '0px', 
    'light': '28px',
    'dark': '56px'
  };

  slider.style.transform = `translateX(${positions[mode]})`;
}

document.addEventListener('DOMContentLoaded', () => {
  initTheme();
  
  const codeContainer = document.getElementById('code-container');
  
  if (window.COMPONENT_DATA && codeContainer) {
    codeContainer.textContent = formatHTML(window.COMPONENT_DATA);
  }
});

window.matchMedia('(prefers-color-scheme: dark)').addEventListener('change', e => {
  if (localStorage.getItem('theme') === 'system') {
    setTheme('system', false);
  }
});