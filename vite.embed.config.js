import { defineConfig } from 'vite';

export default defineConfig({
    publicDir: false,
    build: {
        outDir: 'public/embed/v1',
        lib: {
            entry: 'resources/js/embed-widget.js',
            name: 'EazyAutomotive',
            fileName: () => 'widget.js',
            formats: ['iife'],
        },
        emptyOutDir: false,
        minify: true,
    },
});
