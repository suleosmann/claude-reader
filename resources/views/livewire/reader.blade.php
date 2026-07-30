<div class="cr-app" x-data="readerUI()" :class="{ 'cr-dark': dark }" @paste.window="onGlobalPaste($event)">
<style>
  .cr-app {
    --bg:#f4f5f7; --panel:#fff; --ink:#1f2328; --muted:#6b7280; --border:#e2e4e9;
    --accent:#c15f3c; --accent-soft:#f6ede8; --code-bg:#f6f8fa; --side:#eef0f3;
    color:var(--ink); display:flex; flex-direction:column;
    height:calc(100vh - 4rem); min-height:520px;
    font:15px/1.6 -apple-system,BlinkMacSystemFont,"Segoe UI",Roboto,Helvetica,Arial,sans-serif;
  }
  .cr-app.cr-dark {
    --bg:#0f1115; --panel:#171a21; --ink:#e6e8eb; --muted:#9aa1ab; --border:#262b34;
    --accent:#e08a63; --accent-soft:#2a1f19; --code-bg:#0c0e12; --side:#12151b;
  }
  .cr-app * { box-sizing:border-box; }

  .cr-bar { display:flex; align-items:center; gap:8px; padding:10px 14px; background:var(--panel); border-bottom:1px solid var(--border); }
  .cr-bar .logo { width:24px; height:24px; border-radius:6px; background:var(--accent); color:#fff; display:grid; place-items:center; font-weight:700; font-size:14px; }
  .cr-bar h2 { font-size:14px; margin:0; font-weight:650; }
  .cr-bar .spacer { flex:1; }
  .cr-btn { border:1px solid var(--border); background:var(--panel); color:var(--ink); padding:6px 11px; border-radius:8px; font-size:13px; cursor:pointer; white-space:nowrap; }
  .cr-btn:hover { border-color:var(--accent); }
  .cr-btn.primary { background:var(--accent); color:#fff; border-color:var(--accent); }

  .cr-body { flex:1; display:flex; min-height:0; }
  .cr-side { width:260px; flex:0 0 auto; background:var(--side); border-right:1px solid var(--border); display:flex; flex-direction:column; min-height:0; }
  .cr-side.hidden { display:none; }
  .cr-side-head { display:flex; align-items:center; padding:12px 12px 8px; font-size:12px; text-transform:uppercase; letter-spacing:.06em; color:var(--muted); }
  .cr-side-head .spacer { flex:1; }
  .cr-mini { border:1px solid var(--border); background:var(--panel); color:var(--ink); border-radius:7px; font-size:12px; padding:4px 8px; cursor:pointer; }
  .cr-mini:hover { border-color:var(--accent); }
  .cr-tree { overflow:auto; padding:4px 8px 16px; flex:1; }

  .cr-proj-row, .cr-chat-row { display:flex; align-items:center; gap:6px; border-radius:8px; padding:6px 8px; cursor:pointer; user-select:none; }
  .cr-proj-row:hover, .cr-chat-row:hover { background:rgba(127,127,127,.10); }
  .cr-caret { width:14px; color:var(--muted); font-size:11px; text-align:center; }
  .cr-pname { font-weight:650; font-size:13.5px; flex:1; overflow:hidden; text-overflow:ellipsis; white-space:nowrap; }
  .cr-tools { display:flex; gap:2px; opacity:0; }
  .cr-proj-row:hover .cr-tools, .cr-chat-row:hover .cr-tools { opacity:1; }
  .cr-rowbtn { border:0; background:transparent; color:var(--muted); cursor:pointer; width:22px; height:22px; border-radius:6px; font-size:13px; display:grid; place-items:center; }
  .cr-rowbtn:hover { background:var(--accent); color:#fff; }
  .cr-chats { margin:2px 0 6px 10px; padding-left:8px; border-left:1px solid var(--border); }
  .cr-chat-row { padding:5px 8px; font-size:13px; color:var(--muted); }
  .cr-cname { flex:1; overflow:hidden; text-overflow:ellipsis; white-space:nowrap; }
  .cr-chat-row.active { background:var(--accent-soft); color:var(--ink); font-weight:600; }
  .cr-empty-hint { color:var(--muted); font-size:12px; padding:4px 10px 8px; font-style:italic; }

  .cr-main { flex:1; display:flex; flex-direction:column; min-width:0; min-height:0; }
  .cr-main-head { padding:8px 16px; font-size:12px; text-transform:uppercase; letter-spacing:.06em; color:var(--muted); background:var(--panel); border-bottom:1px solid var(--border); display:flex; align-items:center; gap:10px; }
  .cr-main-head .count { margin-left:auto; text-transform:none; letter-spacing:0; }
  .cr-output { flex:1; overflow:auto; padding:28px 34px; background:var(--panel); }
  .cr-placeholder { color:var(--muted); height:100%; display:grid; place-items:center; text-align:center; padding:40px; }
  .cr-placeholder .big { font-size:40px; opacity:.5; margin-bottom:10px; }

  .cr-output h1,.cr-output h2,.cr-output h3,.cr-output h4 { line-height:1.3; margin:1.6em 0 .6em; font-weight:680; }
  .cr-output h1 { font-size:1.7em; margin-top:0; }
  .cr-output h2 { font-size:1.35em; padding-bottom:.3em; border-bottom:1px solid var(--border); }
  .cr-output h3 { font-size:1.12em; }
  .cr-output p { margin:.7em 0; }
  .cr-output ul,.cr-output ol { padding-left:1.4em; margin:.7em 0; }
  .cr-output a { color:var(--accent); }
  .cr-output blockquote { margin:.9em 0; padding:.2em 1em; border-left:3px solid var(--accent); color:var(--muted); background:var(--accent-soft); border-radius:0 6px 6px 0; }
  .cr-output hr { border:0; border-top:1px solid var(--border); margin:1.8em 0; }
  .cr-output table { border-collapse:collapse; margin:1em 0; width:100%; font-size:14px; display:block; overflow-x:auto; }
  .cr-output th,.cr-output td { border:1px solid var(--border); padding:8px 12px; text-align:left; vertical-align:top; }
  .cr-output thead th { background:var(--accent-soft); font-weight:650; }
  .cr-output tbody tr:nth-child(even) { background:rgba(127,127,127,.04); }
  .cr-output :not(pre) > code { background:var(--code-bg); border:1px solid var(--border); padding:.12em .4em; border-radius:5px; font-size:.88em; font-family:ui-monospace,SFMono-Regular,Menlo,Consolas,monospace; }
  .cr-output pre { position:relative; margin:1.1em 0; background:var(--code-bg); border:1px solid var(--border); border-radius:10px; padding:14px 16px; overflow-x:auto; }
  .cr-output pre code { font-family:ui-monospace,SFMono-Regular,"SF Mono",Menlo,Consolas,monospace; font-size:13px; line-height:1.5; white-space:pre; }
  .cr-copy { position:absolute; top:8px; right:8px; opacity:0; transition:opacity .15s; border:1px solid var(--border); background:var(--panel); color:var(--muted); font-size:11.5px; padding:4px 8px; border-radius:6px; cursor:pointer; }
  .cr-output pre:hover .cr-copy { opacity:1; }
  .cr-copy:hover { color:var(--ink); border-color:var(--accent); }

  /* highlight.php (github-ish) */
  .cr-output .hljs-comment,.cr-output .hljs-quote { color:#6a737d; font-style:italic; }
  .cr-output .hljs-keyword,.cr-output .hljs-selector-tag,.cr-output .hljs-literal,.cr-output .hljs-type { color:#d73a49; }
  .cr-output .hljs-string,.cr-output .hljs-meta-string,.cr-output .hljs-regexp { color:#032f62; }
  .cr-output .hljs-number,.cr-output .hljs-boolean { color:#005cc5; }
  .cr-output .hljs-title,.cr-output .hljs-name,.cr-output .hljs-section { color:#6f42c1; }
  .cr-output .hljs-attr,.cr-output .hljs-attribute,.cr-output .hljs-variable,.cr-output .hljs-template-variable { color:#e36209; }
  .cr-output .hljs-built_in,.cr-output .hljs-builtin-name { color:#005cc5; }
  .cr-output .hljs-symbol,.cr-output .hljs-bullet,.cr-output .hljs-link { color:#e36209; }
  .cr-app.cr-dark .cr-output .hljs-comment,.cr-app.cr-dark .cr-output .hljs-quote { color:#8b949e; }
  .cr-app.cr-dark .cr-output .hljs-keyword,.cr-app.cr-dark .cr-output .hljs-selector-tag,.cr-app.cr-dark .cr-output .hljs-literal,.cr-app.cr-dark .cr-output .hljs-type { color:#ff7b72; }
  .cr-app.cr-dark .cr-output .hljs-string,.cr-app.cr-dark .cr-output .hljs-regexp { color:#a5d6ff; }
  .cr-app.cr-dark .cr-output .hljs-number,.cr-app.cr-dark .cr-output .hljs-boolean { color:#79c0ff; }
  .cr-app.cr-dark .cr-output .hljs-title,.cr-app.cr-dark .cr-output .hljs-name,.cr-app.cr-dark .cr-output .hljs-section { color:#d2a8ff; }
  .cr-app.cr-dark .cr-output .hljs-attr,.cr-app.cr-dark .cr-output .hljs-attribute,.cr-app.cr-dark .cr-output .hljs-variable { color:#ffa657; }
  .cr-app.cr-dark .cr-output .hljs-built_in { color:#79c0ff; }

  .cr-modal { position:fixed; inset:0; background:rgba(0,0,0,.45); display:grid; place-items:center; z-index:50; }
  .cr-modal-box { width:min(680px,92vw); background:var(--panel); border:1px solid var(--border); border-radius:14px; padding:18px; }
  .cr-modal-box textarea { width:100%; height:240px; resize:vertical; border:1px solid var(--border); border-radius:10px; padding:12px; background:var(--code-bg); color:var(--ink); font:13px/1.5 ui-monospace,Menlo,Consolas,monospace; outline:none; }
  .cr-modal-box input[type=text] { width:100%; border:1px solid var(--border); border-radius:10px; padding:11px 12px; background:var(--bg); color:var(--ink); font-size:14px; outline:none; }
  .cr-modal-box input[type=text]:focus { border-color:var(--accent); }
  .cr-modal-title { font-weight:680; font-size:16px; }
  .cr-modal-sub { color:var(--muted); font-size:13px; margin:4px 0 12px; }
  .cr-modal-actions { display:flex; justify-content:flex-end; gap:8px; margin-top:12px; }
  .cr-btn.danger { background:#b3261e; color:#fff; border-color:#b3261e; }
  .cr-btn.danger:hover { filter:brightness(1.05); }

  @media print {
    body * { visibility:hidden !important; }
    .cr-output, .cr-output * { visibility:visible !important; }
    .cr-output { position:absolute; inset:0; overflow:visible !important; padding:0 !important; }
    .cr-copy { display:none !important; }
  }
</style>

  <!-- toolbar -->
  <div class="cr-bar">
    <button class="cr-btn" @click="sideHidden = !sideHidden" title="Show/hide projects">☰</button>
    <div class="logo">C</div>
    <h2>Claude Reader</h2>
    <div class="spacer"></div>
    <button class="cr-btn primary" @click="doPaste()">📋 Paste</button>
    <button class="cr-btn" wire:click="$toggle('fixEncoding')">
        🩹 Fix encoding: {{ $fixEncoding ? 'On' : 'Off' }}
    </button>
    <button class="cr-btn" @click="dark = !dark; persistTheme()" x-text="dark ? '☀️ Light' : '🌙 Dark'"></button>
    <button class="cr-btn" onclick="window.print()">Save as PDF</button>
  </div>

  <div class="cr-body">
    <!-- sidebar -->
    <aside class="cr-side" :class="{ hidden: sideHidden }">
      <div class="cr-side-head">
        <span>Projects</span><span class="spacer"></span>
        <button class="cr-mini" @click="openNewProject()">+ Project</button>
      </div>
      <div class="cr-tree">
        @foreach ($projects as $project)
          <div class="cr-proj">
            <div class="cr-proj-row" wire:click="toggleCollapse({{ $project->id }})">
              <span class="cr-caret">{{ $project->collapsed ? '▸' : '▾' }}</span>
              <span class="cr-pname"
                    @dblclick.stop="openRenameProject({{ $project->id }}, @js($project->name))">{{ $project->name }}</span>
              <span class="cr-tools">
                <button class="cr-rowbtn" title="New chat"
                        @click.stop="$wire.addChat({{ $project->id }})">+</button>
                <button class="cr-rowbtn" title="Rename project"
                        @click.stop="openRenameProject({{ $project->id }}, @js($project->name))">✎</button>
                @if ($projects->count() > 1)
                  <button class="cr-rowbtn" title="Delete project"
                          @click.stop="openConfirmDeleteProject({{ $project->id }}, @js($project->name))">🗑</button>
                @endif
              </span>
            </div>

            @unless ($project->collapsed)
              <div class="cr-chats">
                @forelse ($project->chats as $chat)
                  <div class="cr-chat-row {{ $chat->id === $active?->id ? 'active' : '' }}"
                       wire:key="chat-{{ $chat->id }}"
                       wire:click="selectChat({{ $chat->id }})">
                    <span class="cr-cname">{{ $chat->displayTitle() }}</span>
                    <span class="cr-tools">
                      <button class="cr-rowbtn" title="Rename chat"
                              @click.stop="openRenameChat({{ $chat->id }}, @js($chat->title ?? $chat->displayTitle()))">✎</button>
                      <button class="cr-rowbtn" title="Delete chat"
                              @click.stop="openConfirmDeleteChat({{ $chat->id }})">×</button>
                    </span>
                  </div>
                @empty
                  <div class="cr-empty-hint">No chats yet — press +</div>
                @endforelse
              </div>
            @endunless
          </div>
        @endforeach
      </div>
    </aside>

    <!-- reading pane -->
    <main class="cr-main">
      <div class="cr-main-head">
        <span>{{ $active ? $active->displayTitle() : 'Reading view' }}</span>
        <span class="count">{{ number_format(mb_strlen($active->content ?? '')) }} chars</span>
      </div>
      <div class="cr-output" x-ref="out">
        @if ($active && trim((string) $active->content) !== '')
          {!! $renderedHtml !!}
        @else
          <div class="cr-placeholder"><div>
            <div class="big">📋</div>
            Press <b>Paste</b> (or ⌘V / Ctrl+V) to drop in your Claude output.
          </div></div>
        @endif
      </div>
    </main>
  </div>

  <!-- new project modal -->
  <div class="cr-modal" x-show="newProjectOpen" x-cloak @click.self="newProjectOpen = false"
       @keydown.escape.window="newProjectOpen = false" style="display:none">
    <div class="cr-modal-box">
      <div class="cr-modal-title">New project</div>
      <div class="cr-modal-sub">Give your project a name.</div>
      <input type="text" x-model="newProjectName" x-ref="newProjectInput"
             placeholder="e.g. eTIMS onboarding" @keydown.enter.prevent="submitNewProject()">
      <div class="cr-modal-actions">
        <button class="cr-btn" @click="newProjectOpen = false">Cancel</button>
        <button class="cr-btn primary" @click="submitNewProject()">Create project</button>
      </div>
    </div>
  </div>

  <!-- rename modal (project or chat) -->
  <div class="cr-modal" x-show="renameOpen" x-cloak @click.self="renameOpen = false"
       @keydown.escape.window="renameOpen = false" style="display:none">
    <div class="cr-modal-box">
      <div class="cr-modal-title" x-text="renameTitle"></div>
      <div class="cr-modal-sub" x-text="renameSub"></div>
      <input type="text" x-model="renameName" x-ref="renameInput"
             placeholder="Name" @keydown.enter.prevent="submitRename()">
      <div class="cr-modal-actions">
        <button class="cr-btn" @click="renameOpen = false">Cancel</button>
        <button class="cr-btn primary" @click="submitRename()">Save</button>
      </div>
    </div>
  </div>

  <!-- confirm delete modal (project or chat) -->
  <div class="cr-modal" x-show="confirmOpen" x-cloak @click.self="confirmOpen = false"
       @keydown.escape.window="confirmOpen = false" style="display:none">
    <div class="cr-modal-box">
      <div class="cr-modal-title" x-text="confirmTitle"></div>
      <div class="cr-modal-sub" x-text="confirmMessage"></div>
      <div class="cr-modal-actions">
        <button class="cr-btn" @click="confirmOpen = false">Cancel</button>
        <button class="cr-btn danger" @click="runConfirm()">Delete</button>
      </div>
    </div>
  </div>

  <!-- fallback paste modal -->
  <div class="cr-modal" x-show="modalOpen" x-cloak @click.self="modalOpen = false" style="display:none">
    <div class="cr-modal-box">
      <div style="font-weight:680; font-size:16px;">Paste your content</div>
      <div style="color:var(--muted); font-size:13px; margin:4px 0 12px;">Click the box and press ⌘V / Ctrl+V, then Add.</div>
      <textarea x-model="modalText" x-ref="modalArea" placeholder="Paste here…"></textarea>
      <div class="cr-modal-actions">
        <button class="cr-btn" @click="modalOpen = false">Cancel</button>
        <button class="cr-btn primary" @click="submitModal()">Add</button>
      </div>
    </div>
  </div>
</div>

<script>
  document.addEventListener('alpine:init', () => {
    Alpine.data('readerUI', () => ({
    dark: (localStorage.getItem('cr-theme') || (window.matchMedia && matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light')) === 'dark',
    sideHidden: false,
    modalOpen: false,
    modalText: '',
    newProjectOpen: false,
    newProjectName: '',
    renameOpen: false,
    renameKind: '',
    renameId: null,
    renameName: '',
    renameTitle: '',
    renameSub: '',
    confirmOpen: false,
    confirmKind: '',
    confirmId: null,
    confirmTitle: '',
    confirmMessage: '',

    init() {
      this.attachCopyButtons();
      // Re-add copy buttons whenever the reading pane re-renders.
      new MutationObserver(() => this.attachCopyButtons())
        .observe(this.$refs.out, { childList: true, subtree: true });
    },

    persistTheme() { localStorage.setItem('cr-theme', this.dark ? 'dark' : 'light'); },

    doPaste() {
      if (navigator.clipboard && navigator.clipboard.readText) {
        navigator.clipboard.readText()
          .then(t => { if (t && t.trim()) this.$wire.paste(t); else this.openModal(); })
          .catch(() => this.openModal());
      } else {
        this.openModal();
      }
    },
    onGlobalPaste(e) {
      if (this.modalOpen) return;
      const t = (e.clipboardData || window.clipboardData).getData('text');
      if (t && t.trim()) { e.preventDefault(); this.$wire.paste(t); }
    },
    openModal() { this.modalText = ''; this.modalOpen = true; this.$nextTick(() => this.$refs.modalArea && this.$refs.modalArea.focus()); },
    submitModal() { if (this.modalText.trim()) this.$wire.paste(this.modalText); this.modalOpen = false; },

    openNewProject() { this.newProjectName = ''; this.newProjectOpen = true; this.$nextTick(() => this.$refs.newProjectInput && this.$refs.newProjectInput.focus()); },
    submitNewProject() { this.$wire.addProject(this.newProjectName.trim()); this.newProjectOpen = false; },

    openRenameProject(id, current) {
      this.renameKind = 'project';
      this.renameId = id;
      this.renameName = current || '';
      this.renameTitle = 'Rename project';
      this.renameSub = 'Choose a new name for this project.';
      this.renameOpen = true;
      this.$nextTick(() => this.$refs.renameInput && this.$refs.renameInput.focus());
    },
    openRenameChat(id, current) {
      this.renameKind = 'chat';
      this.renameId = id;
      this.renameName = current || '';
      this.renameTitle = 'Rename chat';
      this.renameSub = 'Leave blank to auto-name from the content.';
      this.renameOpen = true;
      this.$nextTick(() => this.$refs.renameInput && this.$refs.renameInput.focus());
    },
    submitRename() {
      const n = this.renameName.trim();
      if (this.renameKind === 'project') this.$wire.renameProject(this.renameId, n);
      else if (this.renameKind === 'chat') this.$wire.renameChat(this.renameId, n);
      this.renameOpen = false;
    },

    openConfirmDeleteProject(id, name) {
      this.confirmKind = 'project';
      this.confirmId = id;
      this.confirmTitle = 'Delete project';
      this.confirmMessage = 'Delete “' + name + '” and all its chats? This cannot be undone.';
      this.confirmOpen = true;
    },
    openConfirmDeleteChat(id) {
      this.confirmKind = 'chat';
      this.confirmId = id;
      this.confirmTitle = 'Delete chat';
      this.confirmMessage = 'Delete this chat? This cannot be undone.';
      this.confirmOpen = true;
    },
    runConfirm() {
      if (this.confirmKind === 'project') this.$wire.deleteProject(this.confirmId);
      else if (this.confirmKind === 'chat') this.$wire.deleteChat(this.confirmId);
      this.confirmOpen = false;
    },

    attachCopyButtons() {
      this.$refs.out.querySelectorAll('pre').forEach(pre => {
        if (pre.dataset.crReady) return;
        pre.dataset.crReady = '1';
        const btn = document.createElement('button');
        btn.className = 'cr-copy';
        btn.textContent = 'Copy';
        btn.addEventListener('click', () => {
          const code = pre.querySelector('code') || pre;
          navigator.clipboard.writeText(code.innerText).then(() => {
            btn.textContent = 'Copied!';
            setTimeout(() => (btn.textContent = 'Copy'), 1400);
          });
        });
        pre.appendChild(btn);
      });
    },
    }));
  });
</script>
