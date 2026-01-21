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
<html lang="en" class="antialiased">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title><?= $config['BRAND_NAME'] ?> // Component Library</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
      tailwind.config = {
        darkMode: 'class',
        theme: {
          extend: {
            colors: {
              primary: {
                50: '#f0f9ff',
                100: '#e0f2fe',
                200: '#bae6fd',
                300: '#7dd3fc',
                400: '#38bdf8',
                500: '#0ea5e9',
                600: '#0284c7',
                700: '#0369a1',
                800: '#075985',
                900: '#0c4a6e',
                950: '#082f49',
              },
            },
            fontFamily: {
                sans: ['Inter', 'system-ui', 'sans-serif'],
            }
          },
        },
      };
    </script>
    <style>
      .bg-dots { background-image: radial-gradient(#e5e7eb 1px, transparent 1px); background-size: 20px 20px; }
      .dark .bg-dots { background-image: radial-gradient(#334155 1px, transparent 1px); }
      .sidebar-scroll::-webkit-scrollbar { width: 4px; }
      .sidebar-scroll::-webkit-scrollbar-track { background: transparent; }
      .sidebar-scroll::-webkit-scrollbar-thumb { background-color: #cbd5e1; border-radius: 20px; }
    </style>
  </head>
  <body class="bg-white dark:bg-slate-950 text-slate-900 dark:text-slate-100 min-h-screen flex flex-col transition-colors duration-300">
    
    <div class="flex flex-1 w-full relative">
        <aside class="w-64 bg-slate-50 dark:bg-slate-900 border-r border-slate-200 dark:border-slate-800 flex-shrink-0 flex flex-col sticky top-0 h-screen overflow-hidden z-20">
            <div class="p-6 border-b border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 flex-shrink-0">
                <div>
                    <h1 class="text-xl font-bold tracking-tight text-slate-900 dark:text-white"><?= $config['BRAND_NAME'] ?></h1>
                    <p class="text-xs text-slate-500 mt-1">Component Library <span class="px-2 py-0.5 rounded-full bg-slate-200 dark:bg-slate-800 text-slate-600 dark:text-slate-400 text-[10px] font-mono">v<?= $config['APP_VERSION'] ?></span></p>
                </div>
            </div>
            
            <nav class="flex-1 overflow-y-auto p-4 space-y-6 sidebar-scroll" id="nav-container">
                <?php foreach ($categories as $categoryName => $items): ?>
                <div>
                    <h3 class="text-[11px] font-bold text-slate-400 uppercase tracking-widest mb-3 px-3"><?= htmlspecialchars($categoryName) ?></h3>
                    <div class="space-y-1">
                        <?php foreach ($items as $item): ?>
                        <?php $isActive = ($activeId === $item['id']); ?>
                        <a href="?id=<?= $item['id'] ?>" class="group flex items-center justify-between px-3 py-2 text-sm rounded-md transition-all <?= $isActive ? 'bg-primary-600 text-white shadow-md shadow-primary-200 dark:shadow-none' : 'text-slate-600 dark:text-slate-400 hover:bg-slate-200 dark:hover:bg-slate-800' ?>">
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

        <main class="flex-1 flex flex-col bg-white dark:bg-slate-950 min-w-0">
            <?php if ($activeComponent): ?>
                <header class="h-20 border-b border-slate-200 dark:border-slate-800 px-8 flex justify-between items-center bg-white/90 dark:bg-slate-950/90 backdrop-blur-md sticky top-0 z-40">
                    <div>
                        <h2 class="text-xl font-bold text-slate-800 dark:text-slate-100" id="component-title"><?= htmlspecialchars($activeComponent['name']) ?></h2>
                        <p class="text-xs text-slate-400 font-mono" id="component-id"><?= htmlspecialchars($activeComponent['id']) ?></p>
                    </div>
                    <button onclick="copyCode()" class="flex items-center gap-2 px-4 py-2 bg-slate-900 dark:bg-slate-800 text-white text-sm font-medium rounded-lg hover:bg-slate-800 dark:hover:bg-slate-700 transition-all">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M8 5H6a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2v-1M8 5a2 2 0 002 2h2a2 2 0 002-2M8 5a2 2 0 012-2h2a2 2 0 012 2m0 0h2a2 2 0 012 2v3" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                        Copy Snippet
                    </button>
                </header>

                <div class="p-8 space-y-8">
                    <section>
                        <div class="flex items-center gap-2 mb-4">
                            <div class="h-1 w-1 bg-primary-500 rounded-full"></div>
                            <h3 class="text-xs font-bold uppercase tracking-tighter text-slate-500">Live Preview</h3>
                        </div>
                        <div id="preview-container" class="relative border border-slate-200 dark:border-slate-800 rounded-2xl min-h-[400px] z-10 <?= $isFullWidth ? 'w-full bg-white dark:bg-slate-900' : 'bg-dots p-12 flex justify-center items-center bg-slate-50 dark:bg-slate-900/50' ?>">
                            <?= $componentHtml ?>
                        </div>
                    </section>

                    <section id="code-block-wrapper">
                        <div class="flex items-center gap-2 mb-4">
                            <div class="h-1 w-1 bg-primary-500 rounded-full"></div>
                            <h3 class="text-xs font-bold uppercase tracking-tighter text-slate-500">Source Code</h3>
                        </div>
                        <div class="bg-slate-950 rounded-2xl shadow-2xl border border-slate-800 overflow-hidden">
                            <pre class="p-6 overflow-x-auto text-sm leading-relaxed"><code id="code-container" class="text-primary-300 font-mono">Loading code...</code></pre>
                        </div>
                    </section>
                </div>
            
            <?php else: ?>
                <div class="flex-1 flex flex-col items-center justify-center bg-dots py-20">
                    <div class="text-center space-y-4">
                        <div class="w-20 h-20 bg-primary-100 dark:bg-primary-900/30 rounded-3xl flex items-center justify-center mx-auto text-primary-600 dark:text-primary-400 shadow-inner">
                        <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path></svg>
                        </div>
                        <h2 class="text-3xl font-black text-slate-900 dark:text-white tracking-tight">mtex.dev Library</h2>
                        <p class="text-slate-500 max-w-sm mx-auto">Browse the side menu to explore the internal Tailwind CSS component ecosystem for mtex projects.</p>
                    </div>
                </div>
            <?php endif; ?>
        </main>
    </div>

    <footer class="w-full bg-slate-50 dark:bg-slate-900 border-t border-slate-200 dark:border-slate-800 px-6 py-4 z-50">
      <div class="mx-auto flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        
        <div class="flex items-center gap-4">
            <div class="relative flex items-center bg-slate-200 dark:bg-slate-800 rounded-full p-1 h-9">
                <div id="theme-slider" class="absolute h-7 w-7 bg-white dark:bg-slate-600 rounded-full shadow-sm transition-transform duration-200 ease-out left-1"></div>
                
                <button onclick="setTheme('system')" class="relative z-10 w-7 h-7 flex items-center justify-center text-slate-500 hover:text-slate-900 dark:hover:text-white transition-colors" title="System">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-4">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 17.25v1.007a3 3 0 0 1-.879 2.122L7.5 21h9l-.621-.621A3 3 0 0 1 15 18.257V17.25m6-12V15a2.25 2.25 0 0 1-2.25 2.25H5.25A2.25 2.25 0 0 1 3 15V5.25m18 0A2.25 2.25 0 0 0 18.75 3H5.25A2.25 2.25 0 0 0 3 5.25m18 0V12a2.25 2.25 0 0 1-2.25 2.25H5.25A2.25 2.25 0 0 1 3 12V5.25" />
                    </svg>
                </button>
                <button onclick="setTheme('light')" class="relative z-10 w-7 h-7 flex items-center justify-center text-slate-500 hover:text-slate-900 dark:hover:text-white transition-colors" title="Light">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-4">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 18v-5.25m0 0a6.01 6.01 0 0 0 1.5-.189m-1.5.189a6.01 6.01 0 0 1-1.5-.189m3.75 7.478a12.06 12.06 0 0 1-4.5 0m3.75 2.383a14.406 14.406 0 0 1-3 0M14.25 18v-.192c0-.983.658-1.823 1.508-2.316a7.5 7.5 0 1 0-7.517 0c.85.493 1.509 1.333 1.509 2.316V18" />
                    </svg>
                </button>
                <button onclick="setTheme('dark')" class="relative z-10 w-7 h-7 flex items-center justify-center text-slate-500 hover:text-slate-900 dark:hover:text-white transition-colors" title="Dark">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-4">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M21.752 15.002A9.72 9.72 0 0 1 18 15.75c-5.385 0-9.75-4.365-9.75-9.75 0-1.33.266-2.597.748-3.752A9.753 9.753 0 0 0 3 11.25C3 16.635 7.365 21 12.75 21a9.753 9.753 0 0 0 9.002-5.998Z" />
                    </svg>
                </button>
            </div>
            <div class="h-3 w-px bg-slate-200 dark:bg-slate-700"></div>
            <span class="text-xs font-medium tracking-tight text-slate-900 dark:text-slate-400">
                tw.MTEX.dev
            </span>
        </div>

        <div class="flex items-center gap-6">
          <a href="https://fabianternis.dev" target="_blank" class="group flex flex-col">
            <span class="text-[10px] uppercase tracking-wider text-slate-400 transition-colors group-hover:text-primary-500">
              Webmaster
            </span>
            <span class="text-xs font-medium text-slate-600 dark:text-slate-300">fabianternis.dev</span>
          </a>
          <a href="https://mtex.dev" target="_blank" class="group flex flex-col">
            <span class="text-[10px] uppercase tracking-wider text-slate-400 transition-colors group-hover:text-primary-500">
              Imprint
            </span>
            <span class="text-xs font-medium text-slate-600 dark:text-slate-300">mtex.dev</span>
          </a>
        </div>
      </div>
    </footer>
    
    <script>
        window.COMPONENT_DATA = <?= json_encode($componentHtml) ?>;
    </script>
    <script src="assets/script.js"></script>
  </body>
</html>