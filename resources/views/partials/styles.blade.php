@once
<style>
    .msm {
        /* Цвета берутся из темы MoonShine: 4.x — --ms-cm-*, 3.x — RGB-тройки вида --primary: 120, 67, 233 */
        --msm-blue: var(--ms-cm-primary, rgb(var(--primary, 0, 122, 255)));
        --msm-on-blue: var(--ms-cm-primary-text, #fff);
        --msm-green: var(--ms-cm-success, rgb(var(--success-bg, 52, 199, 89)));
        --msm-orange: var(--ms-cm-warning, rgb(var(--warning-bg, 255, 159, 10)));
        --msm-red: var(--ms-cm-error, rgb(var(--error, 255, 59, 48)));
        --msm-muted: rgb(120 120 128 / 0.95); --msm-line: rgb(120 120 128 / 0.18);
        --msm-track: rgb(120 120 128 / 0.16); --msm-card: rgb(120 120 128 / 0.06);
        --msm-tip: rgb(255 255 255 / 0.96); --msm-tip-ink: #1c1c1e;
        display: flex; flex-direction: column; gap: 1rem;
        font-family: -apple-system, BlinkMacSystemFont, "SF Pro Text", Inter, system-ui, sans-serif;
        font-variant-numeric: tabular-nums;
    }
    .dark .msm { --msm-tip: rgb(44 44 46 / 0.96); --msm-tip-ink: #f2f2f7; }
    .msm-head { display: flex; flex-wrap: wrap; align-items: center; justify-content: space-between; gap: .75rem; }
    .msm-segmented { display: inline-flex; padding: 2px; border-radius: 999px; background: var(--msm-track); }
    .msm-segmented a { padding: .35rem .9rem; border-radius: 999px; font-size: .8125rem; font-weight: 500; color: var(--msm-muted); transition: background .15s, color .15s; }
    .msm-segmented a[aria-current="page"] { background: var(--msm-tip); color: var(--msm-tip-ink); box-shadow: 0 1px 3px rgb(0 0 0 / .12); }
    .msm-pill { display: inline-flex; align-items: center; gap: .4rem; padding: .4rem .9rem; border-radius: 999px; font-size: .8125rem; font-weight: 500; background: var(--msm-blue); color: var(--msm-on-blue); }
    .msm-pill .icon-wrapper, .msm-pill svg { width: 1rem; height: 1rem; }
    .msm-tiles { display: grid; gap: 1rem; grid-template-columns: repeat(auto-fit, minmax(10.5rem, 1fr)); }
    .msm-card { border-radius: 1.25rem; padding: 1.1rem 1.25rem; background: var(--msm-card); border: 1px solid var(--msm-line); min-width: 0; }
    .msm-tile-top { display: flex; align-items: center; justify-content: space-between; font-size: .8125rem; color: var(--msm-muted); }
    .msm-status { display: inline-flex; align-items: center; gap: .35rem; font-weight: 500; color: currentColor; }
    .msm-tile-top .msm-status { color: inherit; }
    .msm-status i { width: .5rem; height: .5rem; border-radius: 50%; background: var(--c); }
    .msm-value { margin-top: .35rem; font-size: 2.25rem; line-height: 1.1; font-weight: 600; letter-spacing: -.02em; }
    .msm-value small { font-size: 1.125rem; font-weight: 500; color: var(--msm-muted); }
    .msm-sub { margin-top: .15rem; font-size: .8125rem; color: var(--msm-muted); min-height: 1.2em; }
    .msm-bar { margin-top: .85rem; height: .375rem; border-radius: 999px; background: var(--msm-track); overflow: hidden; }
    .msm-bar span { display: block; height: 100%; border-radius: 999px; background: var(--c); }
    .msm-card-head { display: flex; flex-wrap: wrap; align-items: baseline; justify-content: space-between; gap: .5rem; margin-bottom: .5rem; }
    .msm-card-head h3 { font-size: 1rem; font-weight: 600; }
    .msm-legend { display: flex; flex-wrap: wrap; gap: .9rem; font-size: .75rem; color: var(--msm-muted); }
    .msm-legend span { display: inline-flex; align-items: center; gap: .35rem; }
    .msm-key-line { width: 14px; height: 2px; border-radius: 2px; background: var(--msm-blue); }
    .msm-key-dash { width: 14px; border-top: 2px dashed var(--msm-orange); }
    .msm-key-dot { width: 9px; height: 9px; border-radius: 50%; background: var(--msm-red); }
    .msm-chart { position: relative; }
    .msm-chart svg { display: block; width: 100%; overflow: visible; }
    .msm-chart .grid line { stroke: var(--msm-line); }
    .msm-chart text { fill: var(--msm-muted); font-size: 11px; }
    .msm-tip { position: absolute; z-index: 5; pointer-events: none; padding: .45rem .65rem; border-radius: .75rem; font-size: .75rem; line-height: 1.35;
        background: var(--msm-tip); color: var(--msm-tip-ink); box-shadow: 0 6px 20px rgb(0 0 0 / .18); backdrop-filter: blur(12px); white-space: nowrap; transform: translate(-50%, calc(-100% - 12px)); }
    .msm-tip b { font-size: .875rem; }
    .msm-pair { display: grid; gap: 1rem; grid-template-columns: repeat(auto-fit, minmax(18rem, 1fr)); }
    .msm-scroll { overflow-x: auto; }
    .msm-table { width: 100%; font-size: .8125rem; border-collapse: collapse; }
    .msm-table th { text-align: left; font-weight: 500; color: var(--msm-muted); padding: .4rem .5rem; border-bottom: 1px solid var(--msm-line); white-space: nowrap; }
    .msm-table td { padding: .55rem .5rem; border-bottom: 1px solid var(--msm-line); white-space: nowrap; }
    .msm-table tr:last-child td { border-bottom: 0; }
    .msm-table .num { text-align: right; }
    .msm-hint { font-size: .75rem; color: var(--msm-muted); }
    .msm-empty { padding: 2rem 1rem; text-align: center; color: var(--msm-muted); }
    .msm-select { appearance: none; padding: .4rem 2rem .4rem .9rem; border-radius: 999px; font-size: .8125rem; font-weight: 500; border: 0;
        background: var(--msm-track) url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='10' height='6'%3E%3Cpath d='M1 1l4 4 4-4' stroke='%238e8e93' fill='none' stroke-width='1.5'/%3E%3C/svg%3E") no-repeat right .8rem center; color: inherit; }
    .msm-toggle { display: inline-flex; align-items: center; gap: .45rem; font-size: .8125rem; color: var(--msm-muted); cursor: pointer; user-select: none; }
    .msm-toggle input { appearance: none; width: 2.1rem; height: 1.25rem; border-radius: 999px; background: var(--msm-track); position: relative; transition: background .2s; cursor: pointer; }
    .msm-toggle input::after { content: ""; position: absolute; top: 2px; left: 2px; width: calc(1.25rem - 4px); height: calc(1.25rem - 4px); border-radius: 50%; background: #fff; box-shadow: 0 1px 2px rgb(0 0 0 / .25); transition: transform .2s; }
    .msm-toggle input:checked { background: var(--msm-green); }
    .msm-toggle input:checked::after { transform: translateX(.85rem); }
    .msm-controls { display: flex; flex-wrap: wrap; align-items: center; gap: .6rem; }
    .msm-widget-link { font-size: .8125rem; font-weight: 500; color: var(--msm-blue); }
    .msm-empty code { display: inline-block; margin-top: .5rem; padding: .35rem .6rem; border-radius: .5rem; background: var(--msm-track); }
</style>
@endonce
