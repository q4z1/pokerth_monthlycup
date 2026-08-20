<template>
    <div>
        <h1 class="page-title">Registration for the {{ cup.month_name }} Cup</h1>
        <p class="page-subtitle" v-if="cup.date_label">
            Scheduled for <strong>{{ cup.date_label }}</strong>
        </p>

        <div class="panel" v-if="cup.open">
            <el-form label-position="top" @submit.prevent="submit">
                <el-form-item :error="error">
                    <template #label>
                        Playername <span style="color:var(--el-color-danger)">(case-sensitive!)</span>
                    </template>
                    <el-input
                        v-model="playername"
                        maxlength="64"
                        placeholder="Your PokerTH playername"
                        @keyup.enter="submit"
                    />
                </el-form-item>
            </el-form>

            <el-alert type="warning" :closable="false" show-icon style="margin-bottom:1rem">
                <ul style="margin:0; padding-left:1.1rem">
                    <li>Multiple signups from the same IP or person result in only one randomly
                        picked account being accepted by a member of the orga team.</li>
                    <li>Double registrations and not following admin instructions lead to a
                        temporary PokerTH ban until the first round cup games have started.</li>
                    <li>If a double registration shows up in a later review, all accounts involved
                        get 0 points for participation.</li>
                </ul>
            </el-alert>

            <div style="text-align:right">
                <el-button type="success" :loading="busy" @click="submit">Register</el-button>
            </div>
        </div>

        <el-alert v-else type="error" :closable="false" show-icon title="Registration is closed.">
            Please leave a message in the lobby if you still want to participate &mdash;
            usually you will get a seat as a substitute.
        </el-alert>

        <div style="margin-top:1rem; text-align:center">
            <el-link :href="signupsUrl">Show the current signup list</el-link>
        </div>
    </div>
</template>

<script>
import { ElMessage } from 'element-plus/es/components/message/index';
import { ElMessageBox } from 'element-plus/es/components/message-box/index';

export default {
    props: {
        cup: { type: Object, required: true },
        year: { type: Number, required: true },
        action: { type: String, required: true },
        signupsUrl: { type: String, required: true },
    },
    data() {
        return { playername: '', busy: false, error: '' };
    },
    methods: {
        submit() {
            if (this.busy) return;
            this.error = '';

            if (!this.playername.trim()) {
                this.error = 'Please enter your playername.';
                return;
            }

            this.busy = true;
            window.axios.post(this.action, { playername: this.playername.trim() })
                .then(({ data }) => {
                    this.playername = '';
                    ElMessageBox.alert(data.message, 'Success', { type: 'success' });
                })
                .catch((err) => {
                    const errors = err.response?.data?.errors?.playername;
                    this.error = errors ? errors[0] : (err.response?.data?.message || 'Registration failed.');
                    ElMessage.error(this.error);
                })
                .finally(() => { this.busy = false; });
        },
    },
};
</script>
