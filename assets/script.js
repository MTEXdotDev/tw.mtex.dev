document.addEventListener('DOMContentLoaded', async () => {
  const navContainer = document.getElementById('nav-container');
  const previewContainer = document.getElementById('preview-container');
  const codeContainer = document.getElementById('code-container');
  const titleEl = document.getElementById('component-title');
  const idEl = document.getElementById('component-id');
  const codeWrapper = document.getElementById('code-block-wrapper');

  try {
    const response = await fetch('data/components.json');
    const components = await response.json();
    renderNavigation(components);
  } catch (error) {
    console.error('Failed to load library:', error);
  }

  function renderNavigation(components) {
    const categories = {};

    components.forEach((c) => {
      if (!categories[c.category]) categories[c.category] = [];
      categories[c.category].push(c);
    });

    Object.keys(categories).forEach((cat) => {
      const group = document.createElement('div');
      group.className = 'mb-6';
      
      const title = document.createElement('h3');
      title.className =
        'text-xs font-semibold text-gray-400 uppercase tracking-wider mb-2 px-2';
      title.textContent = cat;
      group.appendChild(title);

      categories[cat].forEach((item) => {
        const btn = document.createElement('button');
        btn.className =
          'w-full text-left px-3 py-2 text-sm text-gray-600 rounded-lg hover:bg-gray-100 hover:text-gray-900 transition-colors mb-1';
        btn.textContent = item.name;
        btn.onclick = () => loadComponent(item);
        group.appendChild(btn);
      });

      navContainer.appendChild(group);
    });
  }

  async function loadComponent(item) {
    try {
      const response = await fetch(item.path);
      const html = await response.text();

      previewContainer.innerHTML = html;
      codeContainer.textContent = html;
      titleEl.textContent = item.name;
      idEl.textContent = item.id;
      codeWrapper.classList.remove('hidden');
    } catch (e) {
      previewContainer.innerHTML = '<span class="text-red-500">Error loading component</span>';
    }
  }
});

window.copyCode = () => {
  const code = document.getElementById('code-container').textContent;
  navigator.clipboard.writeText(code);
  alert('Copied to clipboard!');
};