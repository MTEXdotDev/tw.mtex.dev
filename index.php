<?php
$config = require 'config.php';
$jsonData = file_get_contents('data/components.json');
$components = json_decode($jsonData, true);

$activeId = $_GET['id'] ?? null;
$activeComponent = null;
$componentHtml = "";

if ($activeId) {
    foreach ($components as $c) {
        if ($c['id'] === $activeId) {
            $activeComponent = $c;
            if (file_exists($c['path'])) {
                $componentHtml = file_get_contents($c['path']);
            }
            break;
        }
    }
}

$categories = [];
foreach ($components as $c) {
    $categories[$c['category']][] = $c;
}

$isFullWidth = isset($activeComponent['is_full']) && $activeComponent['is_full'] === true;
?>
<!doctype html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title><?= $config['BRAND_NAME'] ?> // Component Library</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
      body { font-family: 'Inter', system-ui, -apple-system, sans-serif; }
      .bg-dots { background-image: radial-gradient(#e5e7eb 1px, transparent 1px); background-size: 20px 20px; }
      .sidebar-scroll::-webkit-scrollbar { width: 4px; }
      .sidebar-scroll::-webkit-scrollbar-track { background: transparent; }
      .sidebar-scroll::-webkit-scrollbar-thumb { background-color: #cbd5e1; border-radius: 20px; }
    </style>
  </head>
  <body class="bg-white text-slate-900 min-h-screen flex flex-col">
    
    <div class="flex flex-1 w-full relative">
        
        <aside class="w-64 bg-slate-50 border-r border-slate-200 flex-shrink-0 flex flex-col sticky top-0 h-screen overflow-hidden z-20">
            <div class="p-6 border-b border-slate-200 bg-white flex-shrink-0">
                <div>
                <h1 class="text-xl font-bold tracking-tight text-slate-900">
                    <?= $config['BRAND_NAME'] ?>
                </h1>
                <p class="text-xs text-slate-500 mt-1">Component Library <span class="px-2 py-0.5 rounded-full bg-slate-200 text-slate-600 text-[10px] font-mono">v<?= $config['APP_VERSION'] ?></span></p>
                </div>
                </div>
            
            <nav class="flex-1 overflow-y-auto p-4 space-y-6 sidebar-scroll">
                <?php foreach ($categories as $categoryName => $items): ?>
                <div>
                    <h3 class="text-[11px] font-bold text-slate-400 uppercase tracking-widest mb-3 px-3">
                    <?= htmlspecialchars($categoryName) ?>
                    </h3>
                    <div class="space-y-1">
                        <?php foreach ($items as $item): ?>
                        <?php $isActive = ($activeId === $item['id']); ?>
                        <a href="?id=<?= $item['id'] ?>" class="group flex items-center justify-between px-3 py-2 text-sm rounded-md transition-all <?= $isActive ? 'bg-indigo-600 text-white shadow-md shadow-indigo-200' : 'text-slate-600 hover:bg-slate-200' ?>">
                            <?= htmlspecialchars($item['name']) ?>
                            <?php if($isActive): ?>
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M9 5l7 7-7 7" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
                            <?php endif; ?>
                        </a>
                        <?php endforeach; ?>
                    </div>
                </div>
                <?php endforeach; ?>
            </nav>
        </aside>

        <main class="flex-1 flex flex-col bg-white min-w-0">
            <?php if ($activeComponent): ?>
                <header class="h-20 border-b border-slate-200 px-8 flex justify-between items-center bg-white/90 backdrop-blur-md sticky top-0 z-40">
                    <div>
                        <h2 class="text-xl font-bold text-slate-800"><?= htmlspecialchars($activeComponent['name']) ?></h2>
                        <p class="text-xs text-slate-400 font-mono"><?= htmlspecialchars($activeComponent['id']) ?></p>
                    </div>
                    <button onclick="copyCode()" class="flex items-center gap-2 px-4 py-2 bg-slate-900 text-white text-sm font-medium rounded-lg hover:bg-slate-800 transition-all">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M8 5H6a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2v-1M8 5a2 2 0 002 2h2a2 2 0 002-2M8 5a2 2 0 012-2h2a2 2 0 012 2m0 0h2a2 2 0 012 2v3" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                        Copy Snippet
                    </button>
                </header>

                <div class="p-8 space-y-8">
                    <section>
                        <div class="flex items-center gap-2 mb-4">
                            <div class="h-1 w-1 bg-indigo-500 rounded-full"></div>
                            <h3 class="text-xs font-bold uppercase tracking-tighter text-slate-500">Live Preview</h3>
                        </div>
                        <div class="border border-slate-200 rounded-2xl overflow-hidden min-h-[300px] <?= $isFullWidth ? 'bg-white' : 'bg-dots p-12 flex justify-center items-center' ?>">
                            <?= $componentHtml ?>
                        </div>
                    </section>

                    <section>
                        <div class="flex items-center gap-2 mb-4">
                            <div class="h-1 w-1 bg-indigo-500 rounded-full"></div>
                            <h3 class="text-xs font-bold uppercase tracking-tighter text-slate-500">Source Code</h3>
                        </div>
                        <div class="bg-slate-950 rounded-2xl shadow-2xl border border-slate-800 overflow-hidden">
                        <pre class="p-6 overflow-x-auto text-sm leading-relaxed"><code id="code-block" class="text-indigo-300"><?= htmlspecialchars($componentHtml) ?></code></pre>
                        </div>
                    </section>
                </div>
            
            <?php else: ?>
                <div class="flex-1 flex flex-col items-center justify-center bg-dots py-20">
                    <div class="text-center space-y-4">
                        <div class="w-20 h-20 bg-indigo-100 rounded-3xl flex items-center justify-center mx-auto text-indigo-600 shadow-inner">
                        <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path></svg>
                        </div>
                        <h2 class="text-3xl font-black text-slate-900 tracking-tight">mtex.dev Library</h2>
                        <p class="text-slate-500 max-w-sm mx-auto">Browse the side menu to explore the internal Tailwind CSS component ecosystem for mtex projects.</p>
                    </div>
                </div>
            <?php endif; ?>
        </main>
    </div>

    <footer class="w-full bg-slate-50 border-t border-slate-200 px-6 py-4 z-50">
      <div class="mx-auto flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div class="flex items-center gap-4">
          <span class="text-xs font-medium tracking-tight text-slate-900">
            tw.MTEX.dev
          </span>
          <div class="h-3 w-px bg-slate-200"></div>
          <a href="https://github.com/MTEX-dev" target="_blank" class="text-slate-400 hover:text-slate-900 transition-colors" aria-label="GitHub">
            <svg class="h-4 w-4" fill="currentColor" viewBox="0 0 24 24">
              <path d="M12 0c-6.626 0-12 5.373-12 12 0 5.302 3.438 9.8 8.207 11.387.599.111.793-.261.793-.577v-2.234c-3.338.726-4.033-1.416-4.033-1.416-.546-1.387-1.333-1.756-1.333-1.756-1.089-.745.083-.729.083-.729 1.205.084 1.839 1.237 1.839 1.237 1.07 1.834 2.807 1.304 3.492.997.107-.775.418-1.305.762-1.604-2.665-.305-5.467-1.334-5.467-5.931 0-1.311.469-2.381 1.236-3.221-.124-.303-.535-1.524.117-3.176 0 0 1.008-.322 3.301 1.23.957-.266 1.983-.399 3.003-.404 1.02.005 2.047.138 3.006.404 2.291-1.552 3.297-1.23 3.297-1.23.653 1.653.242 2.874.118 3.176.77.84 1.235 1.911 1.235 3.221 0 4.609-2.807 5.624-5.479 5.921.43.372.823 1.102.823 2.222v3.293c0 .319.192.694.801.576 4.765-1.589 8.199-6.086 8.199-11.386 0-6.627-5.373-12-12-12z" />
            </svg>
          </a>
        </div>

        <div class="flex items-center gap-6">
          <a href="https://fabianternis.dev" target="_blank" class="group flex flex-col">
            <span class="text-[10px] uppercase tracking-wider text-slate-400 transition-colors group-hover:text-indigo-500">
              Webmaster
            </span>
            <span class="text-xs font-medium text-slate-600">fabianternis.dev</span>
          </a>
          <a href="https://mtex.dev" target="_blank" class="group flex flex-col">
            <span class="text-[10px] uppercase tracking-wider text-slate-400 transition-colors group-hover:text-indigo-500">
              Imprint
            </span>
            <span class="text-xs font-medium text-slate-600">mtex.dev</span>
          </a>
        </div>
      </div>
    </footer>

    <script>
      function copyCode() {
        const code = document.getElementById('code-block').textContent;
        navigator.clipboard.writeText(code).then(() => {
          alert('Snippet copied to clipboard');
        });
      }
    </script>
  </body>
</html>