<template>
    <div>
        <h1 class="page-title">{{ title }}</h1>
        <p class="page-subtitle">Season {{ year }}</p>

        <div class="panel">
            <el-form label-position="top">
                <el-row :gutter="16">
                    <el-col :xs="24" :sm="12">
                        <el-form-item label="Month">
                            <el-select v-model="form.month" style="width:100%">
                                <el-option v-for="m in months" :key="m.value" :label="m.label" :value="m.value" />
                            </el-select>
                        </el-form-item>
                    </el-col>
                    <el-col :xs="24" :sm="12">
                        <el-form-item label="Table">
                            <el-select v-model="form.table" style="width:100%">
                                <el-option v-for="t in tables" :key="t.value" :label="t.label" :value="t.value" />
                            </el-select>
                        </el-form-item>
                    </el-col>
                </el-row>

                <el-form-item label="Game log link">
                    <el-input v-model="form.url"
                              placeholder="https://pokerth.net/gamelog?pdb=1234567890abcdef&game_id=1" />
                </el-form-item>
            </el-form>

            <div style="text-align:right">
                <el-button type="primary" :loading="busy" @click="preview">Preview</el-button>
            </div>
        </div>

        <el-dialog v-model="showPreview" title="The following upload will be performed" width="640px">
            <p>
                <strong>{{ monthLabel }}</strong> &middot;
                <strong>{{ tableLabel }}</strong>
            </p>
            <el-table :data="rows" style="width:100%" stripe max-height="440">
                <el-table-column prop="position" label="Pos." width="90" />
                <el-table-column prop="playername" label="Player" />
                <el-table-column prop="points" label="Points" width="110" />
            </el-table>
            <template #footer>
                <el-button @click="showPreview = false">Cancel</el-button>
                <el-button type="success" :loading="busy" @click="store">Upload</el-button>
            </template>
        </el-dialog>
    </div>
</template>

<script>
import { ElMessage, ElMessageBox } from 'element-plus';

export default {
    props: {
        mode: { type: String, required: true },
        title: { type: String, required: true },
        year: { type: Number, required: true },
        month: { type: Number, required: true },
        months: { type: Array, default: () => [] },
        tables: { type: Array, default: () => [] },
        previewUrl: { type: String, required: true },
        storeUrl: { type: String, required: true },
    },
    data() {
        return {
            form: {
                type: this.mode,
                month: this.month,
                table: this.tables.length ? this.tables[0].value : '',
                url: '',
            },
            rows: [],
            showPreview: false,
            busy: false,
        };
    },
    computed: {
        monthLabel() {
            return this.months.find((m) => m.value === this.form.month)?.label || '';
        },
        tableLabel() {
            return this.tables.find((t) => t.value === this.form.table)?.label || '';
        },
    },
    methods: {
        preview() {
            if (!this.form.url.trim()) {
                ElMessage.warning('Please paste the game log link.');
                return;
            }
            this.busy = true;
            window.axios.post(this.previewUrl, this.form)
                .then(({ data }) => {
                    this.rows = data.rows;
                    this.showPreview = true;
                })
                .catch(this.fail)
                .finally(() => { this.busy = false; });
        },
        store() {
            this.busy = true;
            window.axios.post(this.storeUrl, this.form)
                .then(({ data }) => {
                    this.showPreview = false;
                    this.form.url = '';
                    ElMessageBox.alert(data.message, 'Success', { type: 'success' });
                })
                .catch(this.fail)
                .finally(() => { this.busy = false; });
        },
        fail(err) {
            const data = err.response?.data;
            const message = data?.message
                || Object.values(data?.errors || {})[0]?.[0]
                || 'The upload failed.';
            ElMessage.error(message);
        },
    },
};
</script>
