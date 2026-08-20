import './bootstrap';
import { createApp } from 'vue';
import 'element-plus/dist/index.css';
import 'element-plus/theme-chalk/dark/css-vars.css';

// Only the Element Plus components this application actually uses, imported from
// their own modules. Named imports from the package root do NOT tree shake — the
// barrel pulls in every component and the bundle stays at ~920 kB either way.
// The list has to cover the Blade templates too, since no bundler plugin scans
// those; a missing entry shows up as a Vue "failed to resolve component" warning.
import { ElAlert } from 'element-plus/es/components/alert/index';
import { ElAvatar } from 'element-plus/es/components/avatar/index';
import { ElButton } from 'element-plus/es/components/button/index';
import { ElCheckbox } from 'element-plus/es/components/checkbox/index';
import { ElCol } from 'element-plus/es/components/col/index';
import { ElDatePicker } from 'element-plus/es/components/date-picker/index';
import { ElDialog } from 'element-plus/es/components/dialog/index';
import { ElDropdown, ElDropdownItem, ElDropdownMenu } from 'element-plus/es/components/dropdown/index';
import { ElEmpty } from 'element-plus/es/components/empty/index';
import { ElForm, ElFormItem } from 'element-plus/es/components/form/index';
import { ElIcon } from 'element-plus/es/components/icon/index';
import { ElInput } from 'element-plus/es/components/input/index';
import { ElInputNumber } from 'element-plus/es/components/input-number/index';
import { ElLink } from 'element-plus/es/components/link/index';
import { ElMenu, ElMenuItem, ElSubMenu } from 'element-plus/es/components/menu/index';
import { ElRow } from 'element-plus/es/components/row/index';
import { ElOption, ElSelect } from 'element-plus/es/components/select/index';
import { ElSpace } from 'element-plus/es/components/space/index';
import { ElStatistic } from 'element-plus/es/components/statistic/index';
import { ElTable, ElTableColumn } from 'element-plus/es/components/table/index';
import { ElTag } from 'element-plus/es/components/tag/index';
import { ElTooltip } from 'element-plus/es/components/tooltip/index';

// Likewise only the icons in use, instead of the full set of roughly a thousand.
import {
    Avatar,
    CopyDocument,
    Edit,
    Files,
    Medal,
    Moon,
    Notebook,
    Plus,
    Refresh,
    Setting,
    Sort,
    Sunny,
    Tools,
    Trophy,
    Upload,
    UserFilled,
} from '@element-plus/icons-vue';

const elementComponents = [
    ElAlert, ElAvatar, ElButton, ElCheckbox, ElCol, ElDatePicker, ElDialog,
    ElDropdown, ElDropdownItem, ElDropdownMenu, ElEmpty, ElForm, ElFormItem,
    ElIcon, ElInput, ElInputNumber, ElLink, ElMenu, ElMenuItem, ElOption,
    ElRow, ElSelect, ElSpace, ElStatistic, ElSubMenu, ElTable, ElTableColumn,
    ElTag, ElTooltip,
];

const icons = {
    Avatar, CopyDocument, Edit, Files, Medal, Moon, Notebook, Plus,
    Refresh, Setting, Sort, Sunny, Tools, Trophy, Upload, UserFilled,
};

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

for (const component of elementComponents) {
    app.use(component);
}

for (const [name, component] of Object.entries(icons)) {
    app.component(name, component);
}

// Auto-register every component in ./components
const modules = import.meta.glob('./components/*.vue', { eager: true });
for (const path in modules) {
    const mod = modules[path];
    const name = path.split('/').pop().replace('.vue', '');
    app.component(name, mod.default || mod);
}

app.mount('#app');
