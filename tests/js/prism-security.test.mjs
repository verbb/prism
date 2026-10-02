import assert from 'node:assert/strict';
import { readFileSync } from 'node:fs';
import { dirname, resolve } from 'node:path';
import test from 'node:test';
import { fileURLToPath } from 'node:url';
import vm from 'node:vm';

const repositoryRoot = resolve(dirname(fileURLToPath(import.meta.url)), '../..');
const guardFiles = [
    'src/web/assets/field/src/prism.js',
    'src/web/assets/field/dist/prism.js',
];

function loadScript(context, path) {
    vm.runInContext(readFileSync(resolve(repositoryRoot, path), 'utf8'), context, {
        filename: path,
    });
}

function createRuntime(guardFile) {
    const jQuery = function() {};
    jQuery.fn = {};

    const context = vm.createContext({ jQuery });
    loadScript(context, 'src/web/assets/field/dist/js/prism/components/prism-core.min.js');
    loadScript(context, guardFile);
    loadScript(context, 'src/web/assets/field/dist/js/prism/components/prism-markup.min.js');
    loadScript(context, 'src/web/assets/field/dist/js/prism/components/prism-markdown.min.js');

    return context.Prism;
}

function highlightFence(Prism, language, content = 'hello') {
    const fence = '`'.repeat(3);
    const markdown = `${fence}${language}\n${content}\n${fence}`;

    return Prism.highlight(markdown, Prism.languages.markdown, 'markdown');
}

for (const guardFile of guardFiles) {
    test(`${guardFile} removes unsafe generated language classes`, () => {
        const Prism = createRuntime(guardFile);
        const unsafeLanguages = [
            '"onmouseover=alert(1)//',
            "x'onmouseover=alert(1)//",
            'x&quot;onmouseover=alert(1)//',
            'x:onmouseover=alert(1)//',
            'x/onmouseover=alert(1)//',
            'X"ONMOUSEOVER=alert(1)//',
        ];

        for (const language of unsafeLanguages) {
            const output = highlightFence(Prism, language);

            assert.match(output, /<span class="token code-block">hello<\/span>/);
        }
    });

    test(`${guardFile} preserves supported generated language classes`, () => {
        const Prism = createRuntime(guardFile);

        for (const language of ['javascript', 'c-sharp', 'foo_bar']) {
            const output = highlightFence(Prism, language);

            assert.match(output, new RegExp(`<span class="token code-block language-${language}">hello<\\/span>`));
        }

        const output = highlightFence(Prism, 'javascript title=Example');
        assert.match(output, /<span class="token code-block language-javascript">hello<\/span>/);
    });

    test(`${guardFile} filters every language alias without changing other token classes`, () => {
        const Prism = createRuntime(guardFile);
        const token = new Prism.Token('code-block', 'hello', [
            'language-javascript',
            'language-"onmouseover=alert(1)//',
            'custom:alias',
        ]);
        const output = Prism.Token.stringify(Prism.util.encode(token), 'markdown');

        assert.equal(output, '<span class="token code-block language-javascript custom:alias">hello</span>');
    });
}
