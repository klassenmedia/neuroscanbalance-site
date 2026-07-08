#!/usr/bin/env node
/**
 * Blog-Planer: veröffentlicht fällige Entwürfe.
 *
 * Liest blog/drafts/schedule.json. Für jeden Eintrag, dessen "publish"-Datum
 * <= heute (Zeitzone Europe/Berlin) ist:
 *   1. verschiebt blog/drafts/<slug>.html  ->  blog/<slug>.html
 *   2. fügt die Beitrags-Karte oben in blog/index.html ein (neuester zuerst)
 *   3. ergänzt die URL in sitemap.xml
 *   4. entfernt den Eintrag aus schedule.json
 *
 * Gibt für GitHub Actions aus, ob etwas veröffentlicht wurde (published=true/false).
 * Läuft ohne externe Abhängigkeiten (nur Node-Standardbibliothek).
 */
import { readFileSync, writeFileSync, existsSync, renameSync, appendFileSync } from 'node:fs';

const ROOT = new URL('..', import.meta.url).pathname;
const SCHEDULE = ROOT + 'blog/drafts/schedule.json';
const INDEX = ROOT + 'blog/index.html';
const SITEMAP = ROOT + 'sitemap.xml';

const esc = (s) => String(s).replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;');

// Heutiges Datum in Europe/Berlin als YYYY-MM-DD
const today = new Intl.DateTimeFormat('en-CA', {
  timeZone: 'Europe/Berlin', year: 'numeric', month: '2-digit', day: '2-digit',
}).format(new Date());

const data = JSON.parse(readFileSync(SCHEDULE, 'utf8'));
const posts = Array.isArray(data.posts) ? data.posts : [];

const due = posts
  .filter((p) => p.publish && p.publish <= today)
  .sort((a, b) => a.publish.localeCompare(b.publish));

if (due.length === 0) {
  console.log(`Nichts fällig (heute ${today}). Geplante Beiträge: ${posts.length}.`);
  if (process.env.GITHUB_OUTPUT) appendFileSync(process.env.GITHUB_OUTPUT, 'published=false\n');
  process.exit(0);
}

let index = readFileSync(INDEX, 'utf8');
let sitemap = readFileSync(SITEMAP, 'utf8');
const publishedSlugs = [];

for (const p of due) {
  const src = `${ROOT}blog/drafts/${p.slug}.html`;
  const dest = `${ROOT}blog/${p.slug}.html`;

  if (!existsSync(src)) {
    console.error(`⚠ Entwurf fehlt: blog/drafts/${p.slug}.html – übersprungen.`);
    continue;
  }
  renameSync(src, dest);

  const card =
`  <a class="blog-card" href="${p.slug}.html">
    <img class="blog-card-img" src="../assets/img/hero/${p.image}" alt="${esc(p.imageAlt || p.title)}" width="1600" height="900" loading="lazy">
    <div class="blog-card-body">
      <span class="blog-card-cat">${esc(p.category)}</span>
      <h2>${esc(p.title)}</h2>
      <p>${esc(p.excerpt)}</p>
      <span class="blog-card-meta">${esc(p.dateLabel)} · ${p.readMin || 4} Min.</span>
    </div>
  </a>
`;
  index = index.replace('<!-- BLOG_CARDS_START -->', `<!-- BLOG_CARDS_START -->\n${card}`);

  const url =
`  <url>
    <loc>https://neuroscanbalance-badessen.de/blog/${p.slug}.html</loc>
    <lastmod>${p.publish}</lastmod>
    <changefreq>monthly</changefreq>
    <priority>0.7</priority>
  </url>
`;
  sitemap = sitemap.replace('<!-- BLOG_SITEMAP_START -->', `<!-- BLOG_SITEMAP_START -->\n${url}`);

  publishedSlugs.push(p.slug);
  console.log(`✓ veröffentlicht: ${p.slug} (geplant für ${p.publish})`);
}

if (publishedSlugs.length === 0) {
  if (process.env.GITHUB_OUTPUT) appendFileSync(process.env.GITHUB_OUTPUT, 'published=false\n');
  process.exit(0);
}

// Blog-Übersicht: lastmod in der Sitemap auf heute ziehen
sitemap = sitemap.replace(
  /(<loc>https:\/\/neuroscanbalance-badessen\.de\/blog\/<\/loc>\s*<lastmod>)[^<]*(<\/lastmod>)/,
  `$1${today}$2`
);

data.posts = posts.filter((p) => !publishedSlugs.includes(p.slug));

writeFileSync(INDEX, index);
writeFileSync(SITEMAP, sitemap);
writeFileSync(SCHEDULE, JSON.stringify(data, null, 2) + '\n');

console.log(`Fertig: ${publishedSlugs.length} Beitrag/Beiträge veröffentlicht.`);
if (process.env.GITHUB_OUTPUT) {
  appendFileSync(process.env.GITHUB_OUTPUT, 'published=true\n');
  appendFileSync(process.env.GITHUB_OUTPUT, `slugs=${publishedSlugs.join(',')}\n`);
}
