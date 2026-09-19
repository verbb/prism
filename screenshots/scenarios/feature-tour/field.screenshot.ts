import { defineScreenshotScenario } from '@verbb/craft-screenshots/api';

import { seedPrismFixture } from '../../support/fixtures';

let entryEditRoute = '/admin/entries';

export default defineScreenshotScenario({
    id: 'prism-feature-tour-field',
    output: 'feature-tour/prism-field.png',
    route: () => entryEditRoute,
    viewport: { width: 1180, height: 840, deviceScaleFactor: 2 },
    async setup(context) {
        const fixture = await seedPrismFixture(context);
        entryEditRoute = fixture.entryEditRoute;
    },
    waitFor: [
        { type: 'selector', selector: '.js--prism-editor', state: 'visible', timeout: 30000 },
    ],
    preSteps: [
        {
            type: 'evaluate',
            expression: `
                (() => {
                    if (document.activeElement instanceof HTMLElement) {
                        document.activeElement.blur();
                    }
                })();
            `,
        },
        { type: 'wait', waitFor: { type: 'timeout', ms: 250 } },
    ],
    target: {
        type: 'selector',
        selector: '.field:has(.js--prism-editor)',
        padding: {
            top: 12,
            right: 0,
            bottom: 4,
            left: 0,
        },
    },
    caption: 'A PHP example being edited with line numbers and live syntax highlighting in Prism.',
    intent: 'Show the real Prism field as authors use it in a current Craft 5 entry.',
});
