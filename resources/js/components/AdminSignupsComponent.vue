<template>
    <div>
        <h1 class="page-title">Signups &mdash; {{ monthName }} Cup {{ year }}</h1>
        <p class="page-subtitle" v-if="dateLabel">Scheduled for <strong>{{ dateLabel }}</strong></p>

        <div class="panel">
            <div style="display:flex; gap:0.75rem; align-items:center; margin-bottom:1rem; flex-wrap:wrap">
                <el-select v-model="selectedMonth" style="width:170px" @change="changeMonth">
                    <el-option v-for="m in months" :key="m.value" :label="m.label" :value="m.value" />
                </el-select>
                <el-input v-model="search" placeholder="Filter player / IP" clearable style="width:240px" />
                <el-tag type="info">{{ accepted }} accepted / {{ signups.length }} total</el-tag>
            </div>

            <el-table :data="filtered" style="width:100%" stripe max-height="700">
                <el-table-column label="No." type="index" width="70" />
                <el-table-column prop="registered_at" label="Date" width="180" sortable />
                <el-table-column label="Player" width="200">
                    <template #default="scope">
                        <a class="player-link" target="_blank" rel="noopener"
                           :href="playerUrl + encodeURIComponent(scope.row.playername)">
                            {{ scope.row.playername }}
                        </a>
                    </template>
                </el-table-column>
                <el-table-column prop="ip" label="IP" width="150" />
                <el-table-column prop="fp" label="fp" width="120" show-overflow-tooltip />
                <el-table-column prop="fpnew" label="new fp" width="120" show-overflow-tooltip />
                <el-table-column label="Accepted" width="110">
                    <template #default="scope">
                        <el-tag :type="scope.row.valid ? 'success' : 'info'">
                            {{ scope.row.valid ? 'Yes' : 'No' }}
                        </el-tag>
                    </template>
                </el-table-column>
                <el-table-column label="Action" width="140">
                    <template #default="scope">
                        <div class="row-actions">
                            <el-button v-if="!scope.row.valid" size="small" type="success"
                                       :loading="busy === scope.row.id" @click="accept(scope.row)">
                                Accept
                            </el-button>
                            <el-button v-else size="small" :loading="busy === scope.row.id" @click="reject(scope.row)">
                                Revoke
                            </el-button>
                            <el-button size="small" type="danger" :loading="busy === scope.row.id"
                                       @click="remove(scope.row)">
                                Remove
                            </el-button>
                        </div>
                    </template>
                </el-table-column>
            </el-table>

            <el-empty v-if="!signups.length" description="No signups found." />
        </div>
    </div>
</template>

<script>
import { ElMessage, ElMessageBox } from 'element-plus';

export default {
    props: {
        year: { type: Number, required: true },
        month: { type: Number, required: true },
        monthName: { type: String, required: true },
        dateLabel: { type: String, default: '' },
        months: { type: Array, default: () => [] },
        initialSignups: { type: Array, default: () => [] },
        baseUrl: { type: String, required: true },
        playerUrl: { type: String, required: true },
    },
    data() {
        return {
            signups: [...this.initialSignups],
            search: '',
            busy: null,
            selectedMonth: this.month,
        };
    },
    computed: {
        accepted() {
            return this.signups.filter((s) => s.valid).length;
        },
        filtered() {
            const term = this.search.trim().toLowerCase();
            if (!term) return this.signups;
            return this.signups.filter((s) => (s.playername || '').toLowerCase().includes(term)
                || (s.ip || '').toLowerCase().includes(term));
        },
    },
    methods: {
        changeMonth(value) {
            window.location.href = `${window.location.pathname}?month=${value}`;
        },
        accept(row) {
            this.send(`${this.baseUrl}/${row.id}/accept`, 'post', row);
        },
        reject(row) {
            this.send(`${this.baseUrl}/${row.id}/reject`, 'post', row);
        },
        remove(row) {
            ElMessageBox.confirm(
                `Really delete the signup of ${row.playername}?`,
                'Delete signup',
                { type: 'warning', confirmButtonText: 'Delete', cancelButtonText: 'Cancel' },
            ).then(() => this.send(`${this.baseUrl}/${row.id}`, 'delete', row)).catch(() => {});
        },
        send(url, method, row) {
            this.busy = row.id;
            window.axios[method](url)
                .then(({ data }) => {
                    this.signups = data.signups;
                    ElMessage.success(data.message);
                })
                .catch((err) => ElMessage.error(err.response?.data?.message || 'Request failed.'))
                .finally(() => { this.busy = null; });
        },
    },
};
</script>
