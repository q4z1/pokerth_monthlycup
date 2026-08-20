<template>
    <div>
        <h1 class="page-title">Settings</h1>
        <p class="page-subtitle">Season configuration</p>

        <div class="panel">
            <div style="display:flex; gap:0.75rem; align-items:center; flex-wrap:wrap">
                <span>Season</span>
                <el-select v-model="selectedYear" style="width:130px" @change="switchYear">
                    <el-option v-for="y in years" :key="y" :label="y" :value="y" />
                </el-select>
                <el-button @click="showSeason = true">
                    <el-icon><Plus /></el-icon>&nbsp;Start new season
                </el-button>
            </div>
        </div>

        <div class="panel">
            <h2 class="section-title">Cup dates</h2>
            <p style="color:var(--el-text-color-secondary)">
                Leave a month empty when no cup is scheduled. Registration closes one hour
                before the given time.
            </p>
            <el-row :gutter="16">
                <el-col :xs="24" :sm="12" :md="8" v-for="m in months" :key="m.value">
                    <el-form-item :label="m.label">
                        <el-date-picker
                            v-model="form.dates[m.value]"
                            type="datetime"
                            format="YYYY-MM-DD HH:mm"
                            value-format="YYYY-MM-DD HH:mm:ss"
                            placeholder="not scheduled"
                            style="width:100%"
                        />
                    </el-form-item>
                </el-col>
            </el-row>
        </div>

        <div class="panel">
            <h2 class="section-title">Ranking points</h2>
            <div class="table-scroll">
                <el-table :data="pointRows" style="width:100%" stripe>
                    <el-table-column prop="place" label="Place" width="90" />
                    <el-table-column label="1st Round" width="140">
                        <template #default="scope">
                            <el-input-number v-model="form.points.first[scope.row.place]"
                                             :controls="false" size="small" style="width:100%" />
                        </template>
                    </el-table-column>
                    <el-table-column v-for="(label, key) in finalTables" :key="key" :label="label" width="140">
                        <template #default="scope">
                            <el-input-number v-model="form.points.final[key][scope.row.place]"
                                             :controls="false" size="small" style="width:100%" />
                        </template>
                    </el-table-column>
                </el-table>
            </div>
            <p style="color:var(--el-text-color-secondary); margin-top:0.75rem">
                First round players additionally receive 1 point for taking part.
            </p>
        </div>

        <div class="panel">
            <h2 class="section-title">Forum links</h2>
            <el-row :gutter="16">
                <el-col :xs="24" :sm="12" v-for="m in months" :key="m.value">
                    <el-form-item :label="m.label">
                        <el-input v-model="form.forum_links[m.value]" placeholder="https://..." />
                    </el-form-item>
                </el-col>
            </el-row>
        </div>

        <div class="panel">
            <h2 class="section-title">Footer</h2>
            <el-input v-model="form.footer" type="textarea" :rows="4"
                      placeholder="Shown at the bottom of every page (HTML allowed)" />
        </div>

        <div class="panel" style="text-align:right">
            <el-button type="primary" :loading="busy" @click="save">Save settings</el-button>
        </div>

        <el-dialog v-model="showSeason" title="Start a new season" width="460px">
            <el-form label-position="top">
                <el-form-item label="New season">
                    <el-input-number v-model="newSeason.year" :min="2000" :max="2100" :controls="false" />
                </el-form-item>
                <el-form-item label="Copy ranking points from">
                    <el-select v-model="newSeason.copy_from" style="width:100%">
                        <el-option v-for="y in years" :key="y" :label="y" :value="y" />
                    </el-select>
                </el-form-item>
            </el-form>
            <template #footer>
                <el-button @click="showSeason = false">Cancel</el-button>
                <el-button type="primary" :loading="busy" @click="createSeason">Create</el-button>
            </template>
        </el-dialog>
    </div>
</template>

<script>
import { ElMessage } from 'element-plus/es/components/message/index';

export default {
    props: {
        year: { type: Number, required: true },
        years: { type: Array, default: () => [] },
        initialSettings: { type: Object, required: true },
        months: { type: Array, default: () => [] },
        finalTables: { type: Object, default: () => ({}) },
        updateUrl: { type: String, required: true },
        seasonUrl: { type: String, required: true },
    },
    data() {
        return {
            selectedYear: this.year,
            form: JSON.parse(JSON.stringify(this.initialSettings)),
            busy: false,
            showSeason: false,
            newSeason: {
                year: Math.max(...this.years) + 1,
                copy_from: this.year,
            },
        };
    },
    computed: {
        pointRows() {
            return Object.keys(this.form.points.first)
                .map((place) => ({ place: Number(place) }))
                .sort((a, b) => a.place - b.place);
        },
    },
    methods: {
        switchYear(value) {
            window.location.href = `${window.location.pathname}?year=${value}`;
        },
        save() {
            this.busy = true;
            window.axios.put(this.updateUrl, { ...this.form, year: this.selectedYear })
                .then(({ data }) => {
                    this.form = data.settings;
                    ElMessage.success(data.message);
                })
                .catch(this.fail)
                .finally(() => { this.busy = false; });
        },
        createSeason() {
            this.busy = true;
            window.axios.post(this.seasonUrl, this.newSeason)
                .then(({ data }) => { window.location.href = data.redirect; })
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
