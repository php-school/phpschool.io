import hljs from 'highlight.js/lib/core'
import php from 'highlight.js/lib/languages/php'
import shell from 'highlight.js/lib/languages/shell'
import javascript from 'highlight.js/lib/languages/javascript'
import markdown from 'highlight.js/lib/languages/markdown'
import json from 'highlight.js/lib/languages/json'

hljs.registerLanguage('php', php)
hljs.registerLanguage('shell', shell)
hljs.registerLanguage('shell', javascript)
hljs.registerLanguage('md', markdown)
hljs.registerLanguage('json', json)

export const highlightCode = (code, language) => {
    return hljs.highlight(code, { language: language }).value
}
