import { readFileSync } from 'node:fs';
import { dirname, join } from 'node:path';
import { fileURLToPath } from 'node:url';

import type { ScreenshotSetupContext } from '@verbb/craft-screenshots/types';

type PrismFixture = {
    entryEditRoute: string;
};

const supportDir = dirname(fileURLToPath(import.meta.url));
const seedScript = readFileSync(join(supportDir, 'seed', 'seed-prism-entry.php'), 'utf8');

/** Seed a PHP example in a real Prism field. */
export async function seedPrismFixture(context: ScreenshotSetupContext): Promise<PrismFixture> {
    const output = await context.runCraftScript(seedScript, { label: 'seed-prism-entry' });
    const fixture = JSON.parse(output.trim()) as PrismFixture;

    if (!fixture.entryEditRoute) {
        throw new Error(`Invalid Prism fixture payload: ${output}`);
    }

    return fixture;
}
