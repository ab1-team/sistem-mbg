<x-app-layout title="Dashboard">

    {{-- PAGE HEADER --}}
    <div class="flex items-start justify-between mb-6">
        <div>
            <h1 class="text-[28px] font-extrabold text-slate-900 tracking-tight leading-none">Dashboard</h1>
            <p class="text-[13px] text-slate-400 mt-1.5">Plan, prioritize, and accomplish your tasks with ease.</p>
        </div>
        <div class="flex items-center gap-2.5">
            <a href="{{ route('dapurs.create') }}"
               class="flex items-center gap-1.5 bg-green-900 text-white text-[12px] font-bold px-4 py-2.5 rounded-xl hover:bg-green-800 transition-colors">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="3" viewBox="0 0 24 24">
                    <line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/>
                </svg>
                Add Project
            </a>
            <button class="text-[12px] font-semibold text-slate-700 px-4 py-2.5 rounded-xl border border-slate-200 bg-white hover:bg-slate-50 transition-colors">
                Import Data
            </button>
        </div>
    </div>

    {{-- STATS GRID --}}
    <div class="grid grid-cols-4 gap-4 mb-5">

        {{-- Hero Card: green --}}
        <div class="bg-green-900 rounded-[20px] p-6 relative overflow-hidden">
            <button class="absolute top-4 right-4 w-7 h-7 rounded-full border border-white/20 flex items-center justify-center text-white hover:bg-white/10 transition-colors">
                <svg class="w-3 h-3" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                    <line x1="7" y1="17" x2="17" y2="7"/><polyline points="7 7 17 7 17 17"/>
                </svg>
            </button>
            <p class="text-[11px] font-semibold text-green-300 uppercase tracking-wide mb-3">Total Projects</p>
            <span class="text-[46px] font-black text-white leading-none tracking-tight">24</span>
            <div class="flex items-center gap-1.5 mt-3">
                <svg class="w-3 h-3 text-green-300" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                    <polyline points="23 6 13.5 15.5 8.5 10.5 1 18"/>
                </svg>
                <span class="text-[11px] text-green-300 font-medium">Increased from last month</span>
            </div>
        </div>

        {{-- Ended Projects --}}
        <div class="bg-white rounded-[20px] p-6 relative border border-slate-100">
            <button class="absolute top-4 right-4 w-7 h-7 rounded-full border border-slate-100 flex items-center justify-center text-slate-400 hover:bg-slate-50 transition-colors">
                <svg class="w-3 h-3" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                    <line x1="7" y1="17" x2="17" y2="7"/><polyline points="7 7 17 7 17 17"/>
                </svg>
            </button>
            <p class="text-[11px] font-semibold text-slate-400 uppercase tracking-wide mb-3">Ended Projects</p>
            <span class="text-[46px] font-black text-slate-900 leading-none tracking-tight">10</span>
            <div class="flex items-center gap-1.5 mt-3">
                <svg class="w-3 h-3 text-green-500" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                    <polyline points="23 6 13.5 15.5 8.5 10.5 1 18"/>
                </svg>
                <span class="text-[11px] text-slate-400 font-medium">Increased from last month</span>
            </div>
        </div>

        {{-- Running Projects --}}
        <div class="bg-white rounded-[20px] p-6 relative border border-slate-100">
            <button class="absolute top-4 right-4 w-7 h-7 rounded-full border border-slate-100 flex items-center justify-center text-slate-400 hover:bg-slate-50 transition-colors">
                <svg class="w-3 h-3" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                    <line x1="7" y1="17" x2="17" y2="7"/><polyline points="7 7 17 7 17 17"/>
                </svg>
            </button>
            <p class="text-[11px] font-semibold text-slate-400 uppercase tracking-wide mb-3">Running Projects</p>
            <span class="text-[46px] font-black text-slate-900 leading-none tracking-tight">12</span>
            <div class="flex items-center gap-1.5 mt-3">
                <svg class="w-3 h-3 text-green-500" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                    <polyline points="23 6 13.5 15.5 8.5 10.5 1 18"/>
                </svg>
                <span class="text-[11px] text-slate-400 font-medium">Increased from last month</span>
            </div>
        </div>

        {{-- Pending Project --}}
        <div class="bg-white rounded-[20px] p-6 relative border border-slate-100">
            <button class="absolute top-4 right-4 w-7 h-7 rounded-full border border-slate-100 flex items-center justify-center text-slate-400 hover:bg-slate-50 transition-colors">
                <svg class="w-3 h-3" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                    <line x1="7" y1="17" x2="17" y2="7"/><polyline points="7 7 17 7 17 17"/>
                </svg>
            </button>
            <p class="text-[11px] font-semibold text-slate-400 uppercase tracking-wide mb-3">Pending Project</p>
            <span class="text-[46px] font-black text-slate-900 leading-none tracking-tight">2</span>
            <div class="mt-3">
                <span class="text-[11px] text-slate-400 font-medium">On Discuss</span>
            </div>
        </div>
    </div>

    {{-- CONTENT ROW --}}
    <div class="grid grid-cols-[1fr_260px_210px] gap-4 mb-5">

        {{-- Project Analytics --}}
        <div class="bg-white rounded-[20px] border border-slate-100 overflow-hidden">
            <div class="flex items-center justify-between px-5 pt-5 pb-3">
                <h3 class="text-[14px] font-bold text-slate-900">Project Analytics</h3>
            </div>
            <div class="flex items-end gap-2.5 px-5 pb-5" style="height:140px;">
                @php
                    $bars = [
                        ['h' => '40%',  'type' => 'stripe', 'day' => 'S'],
                        ['h' => '72%',  'type' => 'solid',  'day' => 'M'],
                        ['h' => '55%',  'type' => 'stripe', 'day' => 'T', 'tip' => '74%'],
                        ['h' => '88%',  'type' => 'solid',  'day' => 'W'],
                        ['h' => '60%',  'type' => 'light',  'day' => 'T'],
                        ['h' => '48%',  'type' => 'stripe', 'day' => 'F'],
                        ['h' => '35%',  'type' => 'stripe', 'day' => 'S'],
                    ];
                @endphp
                @foreach($bars as $bar)
                    <div class="flex flex-col items-center gap-1.5 flex-1">
                        <div class="w-full flex items-end" style="height:100px;">
                            <div class="w-full rounded-t-xl relative
                                {{ $bar['type'] === 'solid'  ? 'bg-green-900' : '' }}
                                {{ $bar['type'] === 'light'  ? 'bg-green-300' : '' }}
                                {{ $bar['type'] === 'stripe' ? 'border border-green-200' : '' }}"
                                 style="height: {{ $bar['h'] }};
                                 {{ $bar['type'] === 'stripe' ? 'background: repeating-linear-gradient(-45deg, #dcfce7, #dcfce7 3px, #f0fdf4 3px, #f0fdf4 8px);' : '' }}">
                                @if(isset($bar['tip']))
                                    <div class="absolute -top-6 left-1/2 -translate-x-1/2 bg-slate-800 text-white text-[9px] font-bold px-1.5 py-0.5 rounded whitespace-nowrap">{{ $bar['tip'] }}</div>
                                @endif
                            </div>
                        </div>
                        <span class="text-[10px] text-slate-400">{{ $bar['day'] }}</span>
                    </div>
                @endforeach
            </div>
        </div>

        {{-- Reminders --}}
        <div class="bg-white rounded-[20px] border border-slate-100 overflow-hidden">
            <div class="px-5 pt-5 pb-3">
                <h3 class="text-[14px] font-bold text-slate-900">Reminders</h3>
            </div>
            <div class="px-5 pb-5">
                <p class="text-[15px] font-bold text-slate-900 leading-snug">Meeting with Arc Company</p>
                <p class="text-[11px] text-slate-400 mt-1">Time : 02.00 pm – 04.00 pm</p>
                <button class="mt-4 flex items-center gap-2 bg-green-900 text-white text-[12px] font-bold px-4 py-2.5 rounded-xl hover:bg-green-800 transition-colors">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <polygon points="23 7 16 12 23 17 23 7"/><rect x="1" y="5" width="15" height="14" rx="2"/>
                    </svg>
                    Start Meeting
                </button>
            </div>
        </div>

        {{-- Project List --}}
        <div class="bg-white rounded-[20px] border border-slate-100 overflow-hidden">
            <div class="flex items-center justify-between px-4 pt-4 pb-2">
                <h3 class="text-[14px] font-bold text-slate-900">Project</h3>
                <a href="{{ route('dapurs.create') }}" class="flex items-center gap-1 text-[11px] font-semibold text-slate-500 bg-slate-50 border border-slate-100 px-2 py-1 rounded-lg hover:bg-slate-100 transition-colors">
                    <svg class="w-3 h-3" fill="none" stroke="currentColor" stroke-width="3" viewBox="0 0 24 24">
                        <line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/>
                    </svg>
                    New
                </a>
            </div>
            @php
                $projectList = [
                    ['name' => 'Develop API Endpoints', 'date' => 'Nov 26', 'color' => 'green'],
                    ['name' => 'Onboarding Flow',       'date' => 'Nov 28', 'color' => 'amber'],
                    ['name' => 'Build Dashboard',       'date' => 'Nov 30', 'color' => 'emerald'],
                    ['name' => 'Optimize Page Load',    'date' => 'Dec 5',  'color' => 'violet'],
                    ['name' => 'Cross-Browser Testing', 'date' => 'Dec 6',  'color' => 'pink'],
                ];
                $dotColors = [
                    'green'   => 'bg-green-700',
                    'amber'   => 'bg-amber-500',
                    'emerald' => 'bg-emerald-600',
                    'violet'  => 'bg-violet-600',
                    'pink'    => 'bg-pink-600',
                ];
                $bgColors = [
                    'green'   => 'bg-green-50',
                    'amber'   => 'bg-amber-50',
                    'emerald' => 'bg-emerald-50',
                    'violet'  => 'bg-violet-50',
                    'pink'    => 'bg-pink-50',
                ];
            @endphp
            @foreach($projectList as $p)
                <div class="flex items-center gap-2.5 px-4 py-2.5 border-t border-slate-50 hover:bg-slate-50 transition-colors">
                    <div class="w-7 h-7 rounded-lg flex items-center justify-center shrink-0 {{ $bgColors[$p['color']] }}">
                        <div class="w-2.5 h-2.5 rounded-sm {{ $dotColors[$p['color']] }}"></div>
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-[12px] font-semibold text-slate-800 leading-tight truncate">{{ $p['name'] }}</p>
                        <p class="text-[10px] text-slate-400">Due date: {{ $p['date'] }}</p>
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    {{-- BOTTOM ROW --}}
    <div class="grid grid-cols-[1fr_1fr_200px] gap-4">

        {{-- Team Collaboration --}}
        <div class="bg-white rounded-[20px] border border-slate-100 overflow-hidden">
            <div class="flex items-center justify-between px-5 pt-5 pb-3">
                <h3 class="text-[14px] font-bold text-slate-900">Team Collaboration</h3>
                <a href="#" class="flex items-center gap-1 text-[11px] font-semibold text-slate-500 bg-slate-50 border border-slate-100 px-2.5 py-1.5 rounded-lg hover:bg-slate-100 transition-colors">
                    <svg class="w-3 h-3" fill="none" stroke="currentColor" stroke-width="3" viewBox="0 0 24 24">
                        <line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/>
                    </svg>
                    Add Member
                </a>
            </div>
            @php
                $members = [
                    ['n' => 'Alexandra Deff',       't' => 'Github Project Repository',       's' => 'Completed',  'av' => 'AD', 'color' => 'amber'],
                    ['n' => 'Edwin Adenike',         't' => 'Integrate User Authentication',   's' => 'In Progress','av' => 'EA', 'color' => 'green'],
                    ['n' => 'Isaac Oluwatemilorun',  't' => 'Develop Search and Filter',       's' => 'Pending',    'av' => 'IO', 'color' => 'violet'],
                    ['n' => 'David Oshodi',          't' => 'Responsive Layout Homepage',      's' => 'In Progress','av' => 'DO', 'color' => 'pink'],
                ];
                $avatarBg   = ['amber' => 'bg-amber-50 text-amber-700',  'green' => 'bg-green-50 text-green-800', 'violet' => 'bg-violet-50 text-violet-700', 'pink' => 'bg-pink-50 text-pink-700'];
                $taskColor  = 'text-green-800';
                $badgeClass = [
                    'Completed'   => 'bg-green-50 text-green-700 border border-green-100',
                    'In Progress' => 'bg-amber-50 text-amber-700 border border-amber-100',
                    'Pending'     => 'bg-red-50 text-red-600 border border-red-100',
                ];
            @endphp
            @foreach($members as $m)
                <div class="flex items-center gap-3 px-5 py-3 border-t border-slate-50">
                    <div class="w-8 h-8 rounded-full flex items-center justify-center text-[11px] font-black shrink-0 {{ $avatarBg[$m['color']] }}">
                        {{ $m['av'] }}
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-[12px] font-semibold text-slate-900 leading-tight">{{ $m['n'] }}</p>
                        <p class="text-[10px] text-slate-400 truncate">Working on <span class="{{ $taskColor }} font-medium">{{ $m['t'] }}</span></p>
                    </div>
                    <span class="text-[9px] font-bold px-2 py-1 rounded-full shrink-0 {{ $badgeClass[$m['s']] }}">
                        {{ $m['s'] }}
                    </span>
                </div>
            @endforeach
        </div>

        {{-- Project Progress --}}
        <div class="bg-white rounded-[20px] border border-slate-100 overflow-hidden">
            <div class="px-5 pt-5 pb-3">
                <h3 class="text-[14px] font-bold text-slate-900">Project Progress</h3>
            </div>
            <div class="flex flex-col items-center pb-5 gap-4">
                <div class="relative w-36 h-36">
                    <svg class="w-full h-full -rotate-90" viewBox="0 0 120 120">
                        <circle cx="60" cy="60" r="46" fill="none" stroke="#f1f5f9" stroke-width="16"/>
                        <circle cx="60" cy="60" r="46" fill="none" stroke="#dcfce7" stroke-width="16"
                                stroke-dasharray="{{ 2*3.14159*46*0.30 }} {{ 2*3.14159*46*0.70 }}"
                                stroke-dashoffset="0" stroke-linecap="round"/>
                        <circle cx="60" cy="60" r="46" fill="none" stroke="#86efac" stroke-width="16"
                                stroke-dasharray="{{ 2*3.14159*46*0.28 }} {{ 2*3.14159*46*0.72 }}"
                                stroke-dashoffset="-{{ 2*3.14159*46*0.30 }}" stroke-linecap="round"/>
                        <circle cx="60" cy="60" r="46" fill="none" stroke="#166534" stroke-width="16"
                                stroke-dasharray="{{ 2*3.14159*46*0.42 }} {{ 2*3.14159*46*0.58 }}"
                                stroke-dashoffset="-{{ 2*3.14159*46*0.58 }}" stroke-linecap="round"/>
                    </svg>
                    <div class="absolute inset-0 flex flex-col items-center justify-center">
                        <span class="text-[26px] font-black text-slate-900 tracking-tight leading-none">41%</span>
                        <span class="text-[10px] text-slate-400 mt-0.5">Project Ended</span>
                    </div>
                </div>
                <div class="flex flex-wrap justify-center gap-x-4 gap-y-1">
                    <div class="flex items-center gap-1.5 text-[11px] text-slate-500">
                        <div class="w-2.5 h-2.5 rounded-full bg-green-800"></div> Completed
                    </div>
                    <div class="flex items-center gap-1.5 text-[11px] text-slate-500">
                        <div class="w-2.5 h-2.5 rounded-full bg-green-300"></div> In Progress
                    </div>
                    <div class="flex items-center gap-1.5 text-[11px] text-slate-500">
                        <div class="w-2.5 h-2.5 rounded-full bg-green-100 border border-green-200"></div> Pending
                    </div>
                </div>
            </div>
        </div>

        {{-- Time Tracker --}}
        <div class="bg-green-900 rounded-[20px] flex flex-col items-center justify-center p-6 text-center gap-4">
            <p class="text-[13px] font-semibold text-green-300">Time Tracker</p>
            <span id="tracker-time" class="text-[32px] font-black text-white tracking-tight leading-none">01:24:08</span>
            <div class="flex items-center gap-2.5">
                <button id="tracker-pause" onclick="toggleTimer()"
                        class="w-9 h-9 rounded-full bg-white flex items-center justify-center hover:scale-105 transition-transform">
                    <svg id="pause-icon" class="w-4 h-4 text-green-900" viewBox="0 0 24 24" fill="currentColor">
                        <rect x="6" y="4" width="4" height="16"/><rect x="14" y="4" width="4" height="16"/>
                    </svg>
                </button>
                <button onclick="stopTimer()"
                        class="w-9 h-9 rounded-full bg-red-500 flex items-center justify-center hover:scale-105 transition-transform">
                    <svg class="w-4 h-4 text-white" viewBox="0 0 24 24" fill="currentColor">
                        <rect x="4" y="4" width="16" height="16" rx="2"/>
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <script>
        let secs = 5048, running = true;
        const disp = document.getElementById('tracker-time');
        const icon = document.getElementById('pause-icon');
        setInterval(() => {
            if (!running) return;
            secs++;
            const h = String(Math.floor(secs/3600)).padStart(2,'0');
            const m = String(Math.floor((secs%3600)/60)).padStart(2,'0');
            const s = String(secs%60).padStart(2,'0');
            disp.textContent = `${h}:${m}:${s}`;
        }, 1000);
        function toggleTimer() {
            running = !running;
            icon.innerHTML = running
                ? '<rect x="6" y="4" width="4" height="16"/><rect x="14" y="4" width="4" height="16"/>'
                : '<polygon points="5,3 19,12 5,21"/>';
        }
        function stopTimer() {
            running = false; secs = 0;
            disp.textContent = '00:00:00';
            icon.innerHTML = '<polygon points="5,3 19,12 5,21"/>';
        }
    </script>

</x-app-layout>
