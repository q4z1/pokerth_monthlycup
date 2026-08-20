import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import vue from '@vitejs/plugin-vue';

export default defineConfig({
    plugins: [
        laravel({
            input: ['resources/js/app.js', 'resources/sass/app.scss'],
            refresh: true,
        }),
        vue({
            template: {
                transformAssetUrls: {
                    base: null,
                    includeAbsolute: false,
                },
            },
        }),
    ],
    resolve: {
        alias: {
            vue: 'vue/dist/vue.esm-bundler.js',
        },
    },
    build: {
        // Element Plus barely changes between deploys, so it gets its own chunk
        // and stays in the browser cache when only application code changes.
        rollupOptions: {
            output: {
                // Assign by module path, not by package name: naming the package
                // would pull the whole library into the chunk and defeat the
                // tree shaking that the explicit imports in app.js enable.
                manualChunks(id) {
                    if (id.includes('node_modules/element-plus')
                        || id.includes('node_modules/@element-plus')) {
                        return 'element-plus';
                    }
                    if (id.includes('node_modules/@vue') || id.includes('node_modules/vue/')) {
                        return 'vue';
                    }
                },
            },
        },
        chunkSizeWarningLimit: 900,
    },
    css: {
        preprocessorOptions: {
            scss: {
                silenceDeprecations: ['color-functions', 'global-builtin', 'import'],
            },
        },
    },
});
