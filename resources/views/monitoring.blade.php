@php
    $t = fn (string $key, array $replace = []) => __('moonshine-monitoring::ui.' . $key, $replace);
    $bytes = static function (?int $value): string {
        if (!$value) {
            return '—';
        }
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        $power = min((int) floor(log($value, 1024)), count($units) - 1);

        return round($value / 1024 ** $power, 1) . ' ' . $units[$power];
    };
    $status = static fn (float $value): string => match (true) {
        $value >= $thresholds['critical'] => 'critical',
        $value >= $thresholds['warning'] => 'warning',
        default => 'ok',
    };
    $url = static fn (array $query): string => '?' . http_build_query(array_merge(['range' => $range, 'instance' => $instance], $query));
    $chartData = [
        'points' => $history,
        'spikes' => $spikes->map(fn ($s) => ['t' => $s['peak_at'], 'value' => $s['peak']])->values(),
        'warning' => $thresholds['warning'],
        'labels' => ['memory' => $t('memory'), 'cpu' => $t('cpu'), 'disk' => $t('disk'), 'spike' => $t('spike')],
    ];
@endphp

<div class="msm" id="msm-root" data-auto-refresh="{{ $autoRefresh }}">
    @include('moonshine-monitoring::partials.styles')

    <div class="msm-head">
        <nav class="msm-segmented" aria-label="{{ $t('timestamp') }}">
            @foreach($ranges as $item)
                <a href="{{ $url(['range' => $item]) }}" @if($item === $range) aria-current="page" @endif>{{ $t('range_' . $item) }}</a>
            @endforeach
        </nav>
        <div class="msm-controls">
            @if($instances->count() > 1)
                <select class="msm-select" aria-label="{{ $t('server') }}" data-msm-instance>
                    @foreach($instances as $item)
                        <option value="{{ $url(['instance' => $item]) }}" @selected($item === $instance)>{{ $item }}</option>
                    @endforeach
                </select>
            @endif
            @if($autoRefresh > 0)
                <label class="msm-toggle" title="{{ $t('updated', ['time' => now()->format('H:i:s')]) }}">
                    <input type="checkbox" data-msm-auto>
                    {{ $t('auto_refresh') }}
                </label>
            @endif
            <a href="{{ $url([]) }}" class="msm-pill">
                <x-moonshine::icon icon="arrow-path" path="moonshine::icons" />
                {{ $t('refresh') }}
            </a>
        </div>
    </div>

    @include('moonshine-monitoring::partials.tiles')

    @if($history->isEmpty())
        <div class="msm-card msm-empty">
            <div style="font-weight: 600">{{ $t('no_data') }}</div>
            <div style="margin-top: .25rem">{{ $t('no_data_hint') }}</div>
            <code>Schedule::command('moonshine-monitoring:record')->everyMinute();</code>
        </div>
    @else
        <div class="msm-card">
            <div class="msm-card-head">
                <h3>{{ $t('memory_chart') }}</h3>
                <div class="msm-legend">
                    <span><i class="msm-key-line"></i>{{ $t('usage') }}</span>
                    <span><i class="msm-key-dash"></i>{{ $t('threshold', ['value' => $thresholds['warning']]) }}</span>
                    <span><i class="msm-key-dot"></i>{{ $t('spike') }}</span>
                </div>
            </div>
            <div class="msm-chart" data-chart="memory" data-height="260"></div>
        </div>

        <div class="msm-pair">
            <div class="msm-card">
                <div class="msm-card-head"><h3>{{ $t('cpu_chart') }}</h3></div>
                <div class="msm-chart" data-chart="cpu" data-height="150"></div>
            </div>
            <div class="msm-card">
                <div class="msm-card-head"><h3>{{ $t('disk_chart') }}</h3></div>
                <div class="msm-chart" data-chart="disk" data-height="150"></div>
            </div>
        </div>

        <div class="msm-card">
            <div class="msm-card-head">
                <h3>{{ $t('spikes') }}</h3>
                <span class="msm-hint">{{ $t('spikes_hint', ['rise' => config('monitoring.memory_spikes.min_rise', 8), 'warning' => $thresholds['warning']]) }}</span>
            </div>
            @if($spikes->isEmpty())
                <div class="msm-empty">{{ $t('no_spikes') }}</div>
            @else
                <div class="msm-scroll">
                    <table class="msm-table">
                        <thead>
                            <tr>
                                <th>{{ $t('peak') }}</th>
                                <th>{{ $t('when') }}</th>
                                <th class="num">{{ $t('duration') }}</th>
                                <th class="num">{{ $t('rise') }}</th>
                                <th class="num">{{ $t('used') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($spikes->take(8) as $spike)
                                @php
                                    $start = \Illuminate\Support\Carbon::parse($spike['start']);
                                    $end = \Illuminate\Support\Carbon::parse($spike['end']);
                                    $minutes = max(1, (int) $start->diffInMinutes($end) + 1);
                                @endphp
                                <tr>
                                    <td style="--c: {{ $spike['peak'] >= $thresholds['warning'] ? 'var(--msm-red)' : 'var(--msm-orange)' }}">
                                        <span class="msm-status"><i></i><b>{{ number_format($spike['peak'], 1) }}%</b></span>
                                    </td>
                                    <td>{{ $start->format('d.m H:i') }}@if($spike['end'] !== $spike['start']) – {{ $end->format('H:i') }}@endif</td>
                                    <td class="num">{{ $t('minutes', ['count' => $minutes]) }}</td>
                                    <td class="num">+{{ number_format($spike['rise'], 1) }} <span class="msm-hint">{{ $t('from_baseline', ['value' => number_format($spike['baseline'], 1)]) }}</span></td>
                                    <td class="num">{{ $bytes($spike['peak_bytes']) }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>

        <div class="msm-card">
            <div class="msm-card-head"><h3>{{ $t('recent') }}</h3></div>
            <div class="msm-scroll">
                <table class="msm-table">
                    <thead>
                        <tr>
                            <th>{{ $t('timestamp') }}</th>
                            <th class="num">{{ $t('cpu') }}</th>
                            <th class="num">{{ $t('memory') }}</th>
                            <th class="num">{{ $t('disk') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($recent as $row)
                            <tr>
                                <td>{{ \Illuminate\Support\Carbon::parse($row['t'])->format('d.m H:i') }}</td>
                                <td class="num">{{ $row['cpu'] !== null ? number_format($row['cpu'], 1) . '%' : '—' }}</td>
                                <td class="num">{{ $row['memory'] !== null ? number_format($row['memory'], 1) . '%' : '—' }}</td>
                                <td class="num">{{ $row['disk'] !== null ? number_format($row['disk'], 1) . '%' : '—' }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @endif

    <script type="application/json" id="msm-data">@json($chartData)</script>
    @once
    <script>
        window.msmInit = (root) => {
            const auto = root.querySelector('[data-msm-auto]');
            const schedule = () => {
                if (!auto) return;
                clearTimeout(window.msmTimer);
                window.msmTimer = setTimeout(async () => {
                    if (!document.contains(root)) return;
                    if (auto.checked && !document.hidden) {
                        try {
                            const html = await (await fetch(window.location.href, { headers: { Accept: 'text/html' } })).text();
                            const fresh = new DOMParser().parseFromString(html, 'text/html').getElementById('msm-root');
                            if (fresh) {
                                root.replaceWith(fresh);
                                window.msmInit(fresh);
                                return;
                            }
                        } catch (e) {}
                    }
                    schedule();
                }, (+root.dataset.autoRefresh || 60) * 1000);
            };
            if (root.dataset.ready) return;
            root.dataset.ready = '1';
            const data = JSON.parse(root.querySelector('#msm-data').textContent);
            const NS = 'http://www.w3.org/2000/svg';
            const el = (name, attrs = {}) => {
                const node = document.createElementNS(NS, name);
                Object.entries(attrs).forEach(([k, v]) => node.setAttribute(k, v));
                return node;
            };
            const fmtTime = (t) => `${t.slice(8, 10)}.${t.slice(5, 7)} ${t.slice(11, 16)}`;

            function draw(container) {
                const key = container.dataset.chart;
                const points = data.points.filter((p) => p[key] !== null);
                container.innerHTML = '';
                if (!points.length) return;

                const width = container.clientWidth;
                const height = +container.dataset.height;
                const pad = { top: 18, right: 8, bottom: 22, left: 30 };
                const w = width - pad.left - pad.right;
                const h = height - pad.top - pad.bottom;
                const x = (i) => pad.left + (points.length === 1 ? w / 2 : (i / (points.length - 1)) * w);
                const y = (v) => pad.top + h - (Math.min(100, Math.max(0, v)) / 100) * h;
                const svg = el('svg', { viewBox: `0 0 ${width} ${height}`, height, role: 'img', 'aria-label': data.labels[key] });

                const grid = el('g', { class: 'grid' });
                [0, 25, 50, 75, 100].forEach((v) => {
                    grid.append(el('line', { x1: pad.left, x2: width - pad.right, y1: y(v), y2: y(v) }));
                    const label = el('text', { x: pad.left - 8, y: y(v) + 4, 'text-anchor': 'end' });
                    label.textContent = v;
                    grid.append(label);
                });
                const ticks = Math.min(width < 480 ? 3 : 5, points.length);
                for (let k = 0; k < ticks; k++) {
                    const i = ticks === 1 ? 0 : Math.round((k / (ticks - 1)) * (points.length - 1));
                    const anchor = k === 0 ? 'start' : (k === ticks - 1 ? 'end' : 'middle');
                    const label = el('text', { x: x(i), y: height - 4, 'text-anchor': anchor });
                    label.textContent = fmtTime(points[i].t);
                    grid.append(label);
                }
                svg.append(grid);

                const line = points.map((p, i) => `${i ? 'L' : 'M'}${x(i).toFixed(1)},${y(p[key]).toFixed(1)}`).join('');
                const area = `${line}L${x(points.length - 1).toFixed(1)},${y(0)}L${x(0).toFixed(1)},${y(0)}Z`;
                const gradientId = `msm-g-${key}`;
                const defs = el('defs');
                const gradient = el('linearGradient', { id: gradientId, x1: 0, x2: 0, y1: 0, y2: 1 });
                gradient.append(el('stop', { offset: '0%', 'stop-color': 'var(--msm-blue)', 'stop-opacity': key === 'memory' ? 0.22 : 0.14 }));
                gradient.append(el('stop', { offset: '100%', 'stop-color': 'var(--msm-blue)', 'stop-opacity': 0 }));
                defs.append(gradient);
                svg.append(defs, el('path', { d: area, fill: `url(#${gradientId})` }));

                if (key === 'memory') {
                    svg.append(el('line', {
                        x1: pad.left, x2: width - pad.right, y1: y(data.warning), y2: y(data.warning),
                        stroke: 'var(--msm-orange)', 'stroke-width': 1.5, 'stroke-dasharray': '5 4',
                    }));
                }
                svg.append(el('path', { d: line, fill: 'none', stroke: 'var(--msm-blue)', 'stroke-width': 2, 'stroke-linejoin': 'round', 'stroke-linecap': 'round' }));

                const spikeAt = new Set();
                if (key === 'memory') {
                    [...data.spikes].sort((a, b) => b.value - a.value).forEach((spike, rank) => {
                        const i = points.findIndex((p) => p.t === spike.t);
                        if (i < 0) return;
                        spikeAt.add(spike.t);
                        const color = spike.value >= data.warning ? 'var(--msm-red)' : 'var(--msm-orange)';
                        svg.append(el('circle', { cx: x(i), cy: y(spike.value), r: 5, fill: color, stroke: 'var(--msm-tip)', 'stroke-width': 2 }));
                        if (rank < 3) {
                            const label = el('text', { x: x(i), y: y(spike.value) - 10, 'text-anchor': 'middle', style: 'fill: currentColor; font-weight: 600' });
                            label.textContent = `${Math.round(spike.value)}%`;
                            svg.append(label);
                        }
                    });
                }

                const cross = el('line', { y1: pad.top, y2: pad.top + h, stroke: 'var(--msm-muted)', 'stroke-width': 1, visibility: 'hidden' });
                const dot = el('circle', { r: 4, fill: 'var(--msm-blue)', stroke: 'var(--msm-tip)', 'stroke-width': 2, visibility: 'hidden' });
                const hit = el('rect', { x: pad.left, y: 0, width: w, height, fill: 'transparent' });
                svg.append(cross, dot, hit);
                container.append(svg);

                const tip = document.createElement('div');
                tip.className = 'msm-tip';
                tip.hidden = true;
                container.append(tip);

                hit.addEventListener('pointermove', (event) => {
                    const box = svg.getBoundingClientRect();
                    const px = ((event.clientX - box.left) / box.width) * width;
                    const idx = Math.max(0, Math.min(points.length - 1, Math.round(((px - pad.left) / w) * (points.length - 1))));
                    const p = points[idx];
                    [cross, dot].forEach((n) => n.setAttribute('visibility', 'visible'));
                    cross.setAttribute('x1', x(idx));
                    cross.setAttribute('x2', x(idx));
                    dot.setAttribute('cx', x(idx));
                    dot.setAttribute('cy', y(p[key]));
                    const gb = key === 'memory' && p.memory_total_bytes ? ` · ${(p.memory_total_bytes * p.memory / 100 / 1024 ** 3).toFixed(1)} GB` : '';
                    const mark = spikeAt.has(p.t) ? ` · ${data.labels.spike}` : '';
                    tip.innerHTML = `${fmtTime(p.t)}<br><b>${p[key].toFixed(1)}%</b>${gb}${mark}`;
                    tip.style.left = `${Math.min(Math.max(x(idx), 70), width - 70)}px`;
                    tip.style.top = `${y(p[key])}px`;
                    tip.hidden = false;
                });
                hit.addEventListener('pointerleave', () => {
                    tip.hidden = true;
                    [cross, dot].forEach((n) => n.setAttribute('visibility', 'hidden'));
                });
            }

            const charts = root.querySelectorAll('[data-chart]');
            let lastWidth = 0;
            new ResizeObserver(() => {
                if (root.clientWidth === lastWidth) return;
                lastWidth = root.clientWidth;
                charts.forEach(draw);
            }).observe(root);

            root.querySelector('[data-msm-instance]')?.addEventListener('change', (event) => {
                window.location.search = event.target.value;
            });

            if (auto) {
                try { auto.checked = localStorage.getItem('msm-auto-refresh') !== '0'; } catch (e) { auto.checked = true; }
                auto.addEventListener('change', () => {
                    try { localStorage.setItem('msm-auto-refresh', auto.checked ? '1' : '0'); } catch (e) {}
                });
            }
            schedule();
        };
    </script>
    @endonce
    <script>window.msmInit(document.currentScript.closest('.msm'));</script>
</div>
