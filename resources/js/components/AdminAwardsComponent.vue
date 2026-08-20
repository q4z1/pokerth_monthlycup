<template>
    <div>
        <h1 class="page-title">Awards {{ year }}</h1>
        <p class="page-subtitle">Upload award images and assign them to players</p>

        <div class="panel">
            <el-button type="primary" @click="showUpload = true">
                <el-icon><Plus /></el-icon>&nbsp;Upload new award
            </el-button>
        </div>

        <div class="panel">
            <el-table :data="awards" style="width:100%" stripe>
                <el-table-column prop="month_name" label="Month" width="130" sortable />
                <el-table-column prop="label" label="Type" width="150" sortable />
                <el-table-column prop="filename" label="Filename" show-overflow-tooltip />
                <el-table-column label="Preview" width="180">
                    <template #default="scope">
                        <img :src="scope.row.url" :alt="scope.row.label" loading="lazy" style="width:110px">
                    </template>
                </el-table-column>
                <el-table-column label="Assigned to" width="220">
                    <template #default="scope">
                        <el-tag v-for="p in scope.row.players" :key="p.id" size="small"
                                style="margin:0 4px 4px 0">
                            {{ p.playername }}
                        </el-tag>
                        <span v-if="!scope.row.players.length" style="color:var(--el-text-color-secondary)">—</span>
                    </template>
                </el-table-column>
                <el-table-column label="Action" width="140">
                    <template #default="scope">
                        <div class="row-actions">
                            <el-button size="small" type="success" @click="openAssign(scope.row)">Assign</el-button>
                            <el-button size="small" @click="openReplace(scope.row)">Replace</el-button>
                            <el-button size="small" type="danger" @click="remove(scope.row)">Delete</el-button>
                        </div>
                    </template>
                </el-table-column>
            </el-table>

            <el-empty v-if="!awards.length" description="No awards available." />
        </div>

        <el-dialog v-model="showUpload" title="Upload award" width="560px">
            <el-form label-position="top">
                <el-row :gutter="16">
                    <el-col :span="12">
                        <el-form-item label="Month">
                            <el-select v-model="upload.month" style="width:100%">
                                <el-option v-for="m in months" :key="m.value" :label="m.label" :value="m.value" />
                            </el-select>
                        </el-form-item>
                    </el-col>
                    <el-col :span="12">
                        <el-form-item label="Type">
                            <el-select v-model="upload.type" style="width:100%">
                                <el-option v-for="t in types" :key="t.value" :label="t.label" :value="t.value" />
                            </el-select>
                        </el-form-item>
                    </el-col>
                </el-row>
                <el-form-item label="Image">
                    <input type="file" accept="image/*" @change="pick">
                </el-form-item>
            </el-form>
            <template #footer>
                <el-button @click="showUpload = false">Cancel</el-button>
                <el-button type="primary" :loading="busy" @click="submitUpload">Upload</el-button>
            </template>
        </el-dialog>

        <el-dialog v-model="showAssign" :title="'Assign ' + (current ? current.label : '')" width="560px">
            <el-select v-model="selectedPlayers" multiple filterable style="width:100%"
                       :multiple-limit="allowsMany ? 0 : 1"
                       :placeholder="allowsMany ? 'Select the players holding this award' : 'Select the player holding this award'">
                <el-option v-for="p in players" :key="p.id" :label="p.playername" :value="p.id" />
            </el-select>
            <p style="color:var(--el-text-color-secondary); margin-top:0.75rem">
                <template v-if="allowsMany">Players removed from this list lose the award.</template>
                <template v-else>This award belongs to a single finishing place, so exactly one
                    player can hold it. Only the admin award may go to several players.</template>
            </p>
            <template #footer>
                <el-button @click="showAssign = false">Cancel</el-button>
                <el-button type="success" :loading="busy" @click="submitAssign">Save</el-button>
            </template>
        </el-dialog>

        <el-dialog v-model="showReplace" title="Replace award image" width="480px">
            <input type="file" accept="image/*" @change="pick">
            <template #footer>
                <el-button @click="showReplace = false">Cancel</el-button>
                <el-button type="primary" :loading="busy" @click="submitReplace">Replace</el-button>
            </template>
        </el-dialog>
    </div>
</template>

<script>
import { ElMessage } from 'element-plus/es/components/message/index';
import { ElMessageBox } from 'element-plus/es/components/message-box/index';

export default {
    props: {
        year: { type: Number, required: true },
        month: { type: Number, required: true },
        initialAwards: { type: Array, default: () => [] },
        players: { type: Array, default: () => [] },
        types: { type: Array, default: () => [] },
        months: { type: Array, default: () => [] },
        storeUrl: { type: String, required: true },
        baseUrl: { type: String, required: true },
    },
    data() {
        return {
            awards: [...this.initialAwards],
            showUpload: false,
            showAssign: false,
            showReplace: false,
            busy: false,
            current: null,
            selectedPlayers: [],
            file: null,
            upload: {
                month: this.month,
                type: this.types.length ? this.types[0].value : '',
            },
        };
    },
    computed: {
        allowsMany() {
            return this.current?.type === 'admin';
        },
    },
    methods: {
        pick(event) {
            this.file = event.target.files[0] || null;
        },
        submitUpload() {
            if (!this.file) {
                ElMessage.warning('Please select an image file.');
                return;
            }
            const body = new FormData();
            body.append('year', this.year);
            body.append('month', this.upload.month);
            body.append('type', this.upload.type);
            body.append('file', this.file);
            this.post(this.storeUrl, body, () => { this.showUpload = false; this.file = null; });
        },
        openAssign(award) {
            this.current = award;
            this.selectedPlayers = award.players.map((p) => p.id);
            this.showAssign = true;
        },
        submitAssign() {
            this.post(`${this.baseUrl}/${this.current.id}/assign`,
                { players: this.selectedPlayers },
                () => { this.showAssign = false; });
        },
        openReplace(award) {
            this.current = award;
            this.file = null;
            this.showReplace = true;
        },
        submitReplace() {
            if (!this.file) {
                ElMessage.warning('Please select an image file.');
                return;
            }
            const body = new FormData();
            body.append('file', this.file);
            this.post(`${this.baseUrl}/${this.current.id}`, body,
                () => { this.showReplace = false; this.file = null; });
        },
        remove(award) {
            ElMessageBox.confirm(
                `Really delete the award ${award.label} (${award.month_name})?`,
                'Delete award',
                { type: 'warning', confirmButtonText: 'Delete', cancelButtonText: 'Cancel' },
            ).then(() => {
                this.busy = true;
                window.axios.delete(`${this.baseUrl}/${award.id}`)
                    .then(({ data }) => { this.awards = data.awards; ElMessage.success(data.message); })
                    .catch(this.fail)
                    .finally(() => { this.busy = false; });
            }).catch(() => {});
        },
        post(url, body, done) {
            this.busy = true;
            window.axios.post(url, body)
                .then(({ data }) => {
                    if (data.awards) this.awards = data.awards;
                    ElMessage.success(data.message);
                    done();
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
