/**
 * patch-abcjs.js
 * ==============
 * Copies our custom abcjs tablature files from the abcjs-fork/ git submodule
 * over the npm-installed versions in node_modules/abcjs. This runs automatically
 * after npm install via the "postinstall" script in package.json.
 *
 * The submodule points to our fork: https://github.com/1unascii/abcjs
 * To edit the patched files, modify them directly in abcjs-fork/src/tablatures/,
 * then commit and push from within that directory.
 *
 * Our patches add:
 *   - Banjo tablature with strOrder (physical string remapping)
 *   - maxFrets (drone string fret restriction)
 *   - Custom and customBanjo instruments for user-defined tunings
 */
import { copyFileSync, existsSync } from 'fs';
import { join, dirname } from 'path';
import { fileURLToPath } from 'url';

const __dirname = dirname(fileURLToPath(import.meta.url));
const forkDir = join(__dirname, '..', 'abcjs-fork', 'src', 'tablatures');
const npmDir = join(__dirname, '..', 'node_modules', 'abcjs', 'src', 'tablatures');

const files = [
    'abc_tablatures.js',
    join('instruments', 'string-patterns.js'),
    join('instruments', 'tab-string.js'),
    join('render', 'tab-absolute-elements.js'),
];

if (!existsSync(forkDir)) {
    console.log('⚠ abcjs-fork submodule not initialized — run: git submodule update --init');
    process.exit(0);
}

if (!existsSync(npmDir)) {
    console.log('⚠ node_modules/abcjs not found — skipping patch.');
    process.exit(0);
}

for (const file of files) {
    const src = join(forkDir, file);
    const dest = join(npmDir, file);
    if (existsSync(src)) {
        copyFileSync(src, dest);
        console.log(`✓ Patched abcjs: ${file}`);
    } else {
        console.log(`⚠ Missing fork file: ${file}`);
    }
}
