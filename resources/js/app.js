import './bootstrap';
import { createApp } from 'vue';
import ElementPlus from 'element-plus';
import 'element-plus/dist/index.css';
import 'element-plus/theme-chalk/dark/css-vars.css';
import * as ElementPlusIconsVue from '@element-plus/icons-vue';
import en from 'element-plus/dist/locale/en.mjs';

const THEME_COOKIE = 'theme';

function storeTheme(theme) {
    document.cookie = `${THEME_COOKIE}=${theme}; path=/; max-age=31536000; SameSite=Lax`;
}

const app = createApp({
    data() {
        return {
            mobileMenuOpen: false,
            theme: document.documentElement.classList.contains('dark') ? 'dark' : 'light',
        };
    },
    methods: {
        toggleTheme() {
            this.theme = this.theme === 'dark' ? 'light' : 'dark';
            document.documentElement.classList.toggle('dark', this.theme === 'dark');
            storeTheme(this.theme);
        },
    },
});

// Register every Element Plus icon globally, as the bbc application does.
for (const [key, component] of Object.entries(ElementPlusIconsVue)) {
    app.component(key, component);
}

app.use(ElementPlus, { locale: en });

// Auto-register every component in ./components
const modules = import.meta.glob('./components/*.vue', { eager: true });
for (const path in modules) {
    const mod = modules[path];
    const name = path.split('/').pop().replace('.vue', '');
    app.component(name, mod.default || mod);
}

app.mount('#app');
