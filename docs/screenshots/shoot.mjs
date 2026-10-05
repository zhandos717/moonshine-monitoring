// Снимает скриншоты страницы мониторинга для README в docs/.
//
// Нужен запущенный Laravel + MoonShine с этим пакетом и историей замеров, плюс пользователь MoonShine:
//   cd docs/screenshots && npm install
//   MS_URL=http://127.0.0.1:8000/admin MS_USER=admin@example.com MS_PASSWORD=secret npm run shoot
//
// CHROME_PATH — путь к Chrome, если он не в стандартном месте macOS.
import { fileURLToPath } from 'node:url';
import path from 'node:path';
import puppeteer from 'puppeteer-core';

const base = (process.env.MS_URL ?? 'http://127.0.0.1:8000/admin').replace(/\/$/, '');
const { MS_USER: user, MS_PASSWORD: password } = process.env;
const chrome = process.env.CHROME_PATH ?? '/Applications/Google Chrome.app/Contents/MacOS/Google Chrome';
const out = path.resolve(path.dirname(fileURLToPath(import.meta.url)), '..');
const pause = (ms) => new Promise((resolve) => setTimeout(resolve, ms));

if (!user || !password) {
    console.error('Set MS_USER and MS_PASSWORD');
    process.exit(1);
}

const browser = await puppeteer.launch({ executablePath: chrome, headless: true });
const page = await browser.newPage();
// 1280 CSS px × 1.25 = 1600 px: раскладка как на обычном ноутбуке, картинка чёткая на Retina
await page.setViewport({ width: 1280, height: 900, deviceScaleFactor: 1.25 });

await page.goto(`${base}/login`);
await page.type('input[name=username]', user);
await page.type('input[name=password]', password);
await Promise.all([page.waitForNavigation(), page.keyboard.press('Enter')]);

async function open(theme) {
    await page.goto(`${base}/page/monitoring-page`, { waitUntil: 'networkidle0' });
    const isDark = await page.evaluate(() => document.documentElement.classList.contains('dark'));
    if ((theme === 'dark') !== isDark) {
        // Переключатель темы MoonShine слушает это событие
        await page.evaluate(() => window.dispatchEvent(new CustomEvent('darkMode:toggle')));
        await pause(400);
    }
    await page.evaluate(() => window.scrollTo(0, 0));
    await pause(300);
}

for (const theme of ['light', 'dark']) {
    await open(theme);
    await page.screenshot({ path: `${out}/dashboard-${theme}.png` });
}

await open('light');
const spikes = await page.$('.msm-card:has(table) h3');
if (spikes) {
    const card = await spikes.evaluateHandle((h) => h.closest('.msm-card'));
    await card.screenshot({ path: `${out}/memory-spikes.png` });
}

const widget = await page.goto(`${base}`, { waitUntil: 'networkidle0' }).then(() => page.$('.msm'));
if (widget) {
    await widget.screenshot({ path: `${out}/dashboard-widget.png` });
}

await page.setViewport({ width: 390, height: 844, deviceScaleFactor: 3, isMobile: true, hasTouch: true });
await open('light');
await page.screenshot({ path: `${out}/dashboard-mobile.png` });
await page.evaluate(() => document.querySelector('[data-chart="memory"]').closest('.msm-card').scrollIntoView({ block: 'start' }));
await pause(300);
await page.screenshot({ path: `${out}/dashboard-mobile-chart.png` });

await browser.close();
console.log(`Screenshots saved to ${out}`);
