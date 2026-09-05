<template>
    <div>
        <h1 class="page-title">Forum posts &mdash; {{ monthName }} Cup {{ year }}</h1>
        <p class="page-subtitle">BBCode for the pokerth.net announcement, seeding and results posts</p>

        <div class="panel">
            <div style="display:flex; gap:0.75rem; align-items:center; flex-wrap:wrap">
                <span>Month</span>
                <el-select v-model="selectedMonth" style="width:160px" @change="switchMonth">
                    <el-option v-for="m in months" :key="m.value" :label="m.label" :value="m.value" />
                </el-select>
            </div>
        </div>

        <div class="panel">
            <h2 class="section-title">Table admins</h2>
            <p style="color:var(--el-text-color-secondary)">
                One row per 1st round table, in table order. A name listed here is left out of the
                random seeding below, even if it also signed up as a player.
            </p>

            <el-form-item label="Number of tables">
                <el-input-number v-model="tableCount" :min="1" :max="20" :controls="false" @change="resizeAdmins" />
            </el-form-item>

            <el-row :gutter="16">
                <el-col :xs="24" :sm="12" :md="8" v-for="(admin, i) in form.admins" :key="i">
                    <el-form-item :label="`Table ${i + 1} admin`">
                        <el-input v-model="form.admins[i]" placeholder="playername" />
                    </el-form-item>
                </el-col>
            </el-row>

            <el-form-item label="Players per table">
                <el-input-number v-model="form.players_per_table" :min="1" :max="20" :controls="false" />
            </el-form-item>

            <el-form-item label="Admin subs (backup table admins, not seated)">
                <div style="width:100%">
                    <div v-for="(sub, i) in form.admin_subs" :key="i"
                         style="display:flex; gap:0.5rem; margin-bottom:0.5rem">
                        <el-input v-model="form.admin_subs[i]" placeholder="playername" />
                        <el-button @click="form.admin_subs.splice(i, 1)">✕</el-button>
                    </div>
                    <el-button @click="form.admin_subs.push('')">
                        <el-icon><Plus /></el-icon>&nbsp;Add
                    </el-button>
                </div>
            </el-form-item>

            <h2 class="section-title">Announcement text</h2>
            <el-row :gutter="16">
                <el-col :xs="24" :sm="12">
                    <el-form-item label="Cup date/time, as it should read in the post">
                        <el-input v-model="form.cup_date_label" placeholder="August 29th - 20:00 CEST" />
                    </el-form-item>
                </el-col>
                <el-col :xs="24" :sm="12">
                    <el-form-item label="Seeding time, as it should read in the post">
                        <el-input v-model="form.seeding_time_label" placeholder="August 29th - 18:30 CEST" />
                    </el-form-item>
                </el-col>
            </el-row>
            <el-form-item label="Theme image URL">
                <el-input v-model="form.theme_image" placeholder="https://.../mcup_theme.jpg" />
            </el-form-item>

            <div style="text-align:right">
                <el-button type="primary" :loading="busy" @click="save">Save &amp; regenerate</el-button>
            </div>
        </div>

        <div class="panel">
            <el-tabs v-model="activeTab">
                <el-tab-pane label="1. Announcement" name="announcement">
                    <post-preview :text="posts.announcement" />
                </el-tab-pane>

                <el-tab-pane label="2. 1st round seeding" name="seeding">
                    <div style="display:flex; gap:0.75rem; margin-bottom:1rem; flex-wrap:wrap; align-items:center">
                        <el-button type="primary" :loading="busy" @click="shuffle">
                            <el-icon><Refresh /></el-icon>&nbsp;Shuffle again
                        </el-button>
                        <span style="color:var(--el-text-color-secondary)">
                            {{ signupCount }} accepted signup(s)
                        </span>
                    </div>
                    <el-empty v-if="!seedingTablesLocal.length" description="Add at least one table admin above first." />
                    <post-preview v-else :text="posts.seeding" />
                </el-tab-pane>

                <el-tab-pane label="3. Final round seeding" name="final-seeding">
                    <post-preview :text="posts.finalSeeding" />
                </el-tab-pane>

                <el-tab-pane label="4. Results &amp; awards" name="results">
                    <post-preview :text="posts.results" />
                </el-tab-pane>
            </el-tabs>
        </div>
    </div>
</template>

<script>
import { ElMessage } from 'element-plus/es/components/message/index';

const PostPreview = {
    props: { text: { type: String, default: '' } },
    emits: [],
    methods: {
        copy() {
            navigator.clipboard.writeText(this.text)
                .then(() => ElMessage.success('BBCode copied.'))
                .catch(() => ElMessage.error('Copying failed.'));
        },
    },
    template: `
        <div>
            <div style="text-align:right; margin-bottom:0.5rem">
                <el-button @click="copy">
                    <el-icon><CopyDocument /></el-icon>&nbsp;Copy BBCode
                </el-button>
            </div>
            <pre class="seed-list">{{ text }}</pre>
        </div>
    `,
};

export default {
    components: { PostPreview },
    props: {
        year: { type: Number, required: true },
        month: { type: Number, required: true },
        monthName: { type: String, required: true },
        months: { type: Array, default: () => [] },
        config: { type: Object, required: true },
        signupCount: { type: Number, default: 0 },
        announcement: { type: String, default: '' },
        seeding: { type: String, default: '' },
        seedingTables: { type: Array, default: () => [] },
        seedingSubstitutes: { type: Array, default: () => [] },
        finalSeeding: { type: String, default: '' },
        results: { type: String, default: '' },
        saveUrl: { type: String, required: true },
        shuffleUrl: { type: String, required: true },
    },
    data() {
        return {
            selectedMonth: this.month,
            activeTab: 'announcement',
            busy: false,
            tableCount: Math.max(1, this.config.admins.length || 1),
            form: {
                admins: this.config.admins.length ? [...this.config.admins] : [''],
                admin_subs: [...this.config.admin_subs],
                players_per_table: this.config.players_per_table,
                theme_image: this.config.theme_image,
                cup_date_label: this.config.cup_date_label,
                seeding_time_label: this.config.seeding_time_label,
            },
            posts: {
                announcement: this.announcement,
                seeding: this.seeding,
                finalSeeding: this.finalSeeding,
                results: this.results,
            },
            seedingTablesLocal: this.seedingTables,
        };
    },
    methods: {
        switchMonth(value) {
            window.location.href = `${window.location.pathname}?month=${value}`;
        },
        resizeAdmins(count) {
            const admins = this.form.admins.slice(0, count);
            while (admins.length < count) admins.push('');
            this.form.admins = admins;
        },
        save() {
            this.busy = true;
            window.axios.post(this.saveUrl, {
                year: this.year,
                month: this.month,
                ...this.form,
            }).then(({ data }) => {
                this.posts.announcement = data.announcement;
                this.posts.seeding = data.seeding;
                this.seedingTablesLocal = data.seeding_tables;
                ElMessage.success(data.message);
            }).catch(this.fail).finally(() => { this.busy = false; });
        },
        shuffle() {
            this.busy = true;
            window.axios.post(this.shuffleUrl, { year: this.year, month: this.month })
                .then(({ data }) => {
                    this.posts.seeding = data.seeding;
                    this.seedingTablesLocal = data.seeding_tables;
                })
                .catch(this.fail)
                .finally(() => { this.busy = false; });
        },
        fail(err) {
            const data = err.response?.data;
            ElMessage.error(data?.message
                || Object.values(data?.errors || {})[0]?.[0]
                || 'Request failed.');
        },
    },
};
</script>
