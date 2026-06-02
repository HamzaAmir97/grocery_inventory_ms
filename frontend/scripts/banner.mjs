#!/usr/bin/env node
/**
 * Inventory · unified startup banner (Frontend)
 *
 * Prints a modern, branded CLI banner before the Next.js dev/prod server boots.
 * Keep this visually in sync with the backend banner at:
 *   backend/grocery-inventory-backend/app/Console/Commands/InventoryServeCommand.php
 *
 * No external dependencies on purpose (raw ANSI) so it never touches the lockfile
 * and renders identically across the two stacks.
 */
import os from 'node:os';
import { readFileSync } from 'node:fs';
import { fileURLToPath } from 'node:url';
import path from 'node:path';

const mode = (process.argv[2] || 'dev').toLowerCase();
const isProd = mode === 'start' || mode === 'prod' || mode === 'production';

// ---------- palette (matches the project's concurrently colors) ----------
const C1 = [0x93, 0xc5, 0xfd]; // blue
const C2 = [0xc4, 0xb5, 0xfd]; // violet
const C3 = [0xfd, 0xba, 0x74]; // orange
const ACCENT = isProd ? [0x6e, 0xe7, 0xb7] : [0x86, 0xef, 0xac]; // green

const useColor = process.stdout.isTTY && process.env.NO_COLOR === undefined;
const RESET = useColor ? '\x1b[0m' : '';
const BOLD = useColor ? '\x1b[1m' : '';
const DIM = useColor ? '\x1b[2m' : '';
const fg = ([r, g, b]) => (useColor ? `\x1b[38;2;${r};${g};${b}m` : '');

const lerp = (a, b, t) => Math.round(a + (b - a) * t);
const grad3 = (t) => {
  if (t < 0.5) {
    const u = t / 0.5;
    return [lerp(C1[0], C2[0], u), lerp(C1[1], C2[1], u), lerp(C1[2], C2[2], u)];
  }
  const u = (t - 0.5) / 0.5;
  return [lerp(C2[0], C3[0], u), lerp(C2[1], C3[1], u), lerp(C2[2], C3[2], u)];
};
const gradient = (str) => {
  if (!useColor) return str;
  const n = Math.max(str.length - 1, 1);
  let out = '';
  for (let i = 0; i < str.length; i++) out += fg(grad3(i / n)) + str[i];
  return out + RESET;
};

// ---------- runtime facts ----------
const port = process.env.PORT || '3000';
const host = 'localhost';
let network = null;
for (const list of Object.values(os.networkInterfaces())) {
  for (const net of list || []) {
    if (net.family === 'IPv4' && !net.internal) { network = net.address; break; }
  }
  if (network) break;
}
const api =
  process.env.NEXT_PUBLIC_API_BASE_URL ||
  process.env.NEXT_PUBLIC_API_URL ||
  'http://localhost:8000/api';
let nextVersion = 'Next.js';
try {
  const pkgPath = path.resolve(path.dirname(fileURLToPath(import.meta.url)), '..', 'package.json');
  const pkg = JSON.parse(readFileSync(pkgPath, 'utf8'));
  const major = (pkg.dependencies?.next || '').replace(/[^\d.]/g, '').split('.')[0];
  if (major) nextVersion = `Next.js ${major}`;
} catch { /* keep default */ }

// ---------- box renderer ----------
const W = 60; // inner width between the borders
const plain = (s) => s.replace(/\x1b\[[0-9;]*m/g, '');
const border = (left, right) => {
  const dashes = '─'.repeat(W);
  return (useColor ? gradient(left + dashes + right) : left + dashes + right);
};
const row = (colored) => {
  const len = plain(colored).length;
  const padRight = Math.max(0, W - len);
  const edge = useColor ? fg(C2) + '│' + RESET : '│';
  return edge + colored + ' '.repeat(padRight) + edge;
};
const label = (l) => DIM + l.padEnd(12) + RESET;
const detail = (l, v, color = fg(C1)) =>
  row('   ' + DIM + '▸ ' + RESET + label(l) + color + v + RESET);

const lines = [
  '',
  border('╭', '╮'),
  row(''),
  row('   ' + gradient('╔═╗')),
  row('   ' + gradient('╠═╣') + '  ' + BOLD + gradient('I N V E N T O R Y') + RESET),
  row('   ' + gradient('╚═╝') + '  ' + DIM + 'Grocery Inventory Management System' + RESET),
  row(''),
  detail('Service', 'Frontend · ' + nextVersion, fg(C2)),
  detail('Environment', isProd ? 'production' : 'development', fg(C3)),
  detail('Local', `http://${host}:${port}`),
  ...(network ? [detail('Network', `http://${network}:${port}`)] : []),
  detail('API', api),
  row(''),
  row('   ' + fg(ACCENT) + '●' + RESET + ' ' + (isProd ? 'Production server starting' : 'Dev server starting') + DIM + ' — press Ctrl+C to stop' + RESET),
  row(''),
  border('╰', '╯'),
  '',
];

process.stdout.write(lines.join('\n') + '\n');
